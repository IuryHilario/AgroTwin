/**
 * Seletor de localidade (Alpine) — par do componente <x-form.localidade>.
 *
 * Três formas de chegar às coordenadas, todas terminando no mesmo par
 * latitude/longitude que o Open-Meteo usa:
 *   - busca de município (geocodificação do Open-Meteo, via /localidades/buscar);
 *   - GPS do navegador, para quem está na própria fazenda;
 *   - clique ou arraste do pino no mapa, para acertar o ponto exato — o centro
 *     da cidade pode ficar a dezenas de km da lavoura.
 *
 * Dentro de um <x-ui.stepper>, bloqueia o avanço da etapa enquanto não houver
 * ponto escolhido (evento "etapa-validar").
 */

// Centro aproximado do Brasil, para o mapa abrir em algum lugar útil.
const CENTRO_PADRAO = [-14.2, -51.9];
const ZOOM_PADRAO = 4;
const ZOOM_LOCAL = 12;
const BUSCA_INDISPONIVEL = 'A busca de municípios não respondeu agora. Marque o ponto direto no mapa ou use sua localização.';

let leafletCarregando = null;

/** Leaflet só é baixado quando algum seletor aparece na tela. */
function carregarLeaflet() {
    leafletCarregando ??= Promise.all([
        import('leaflet'),
        import('leaflet/dist/leaflet.css'),
    ]).then(([modulo]) => modulo.default ?? modulo);

    return leafletCarregando;
}

export default function seletorLocalidade({ latitude = null, longitude = null, rotulo = '', obrigatorio = false, urlBusca }) {
    // Fora do estado reativo de propósito: o Alpine envolve em Proxy tudo que
    // fica em `this`, e o Leaflet quebra quando seus objetos são proxiados.
    let L = null;
    let mapa = null;
    let marcador = null;
    let montando = false;
    let temporizador = null;

    return {
        latitude,
        longitude,
        rotulo,
        termo: '',
        resultados: [],
        destacado: -1,
        buscando: false,
        semResultado: false,
        localizando: false,
        aviso: null,

        init() {
            this.observarVisibilidade();

            // Validação própria, na ordem que faz sentido para quem preenche:
            // primeiro o ponto (que também preenche o nome), depois o nome.
            this.$root.closest('[data-etapa]')?.addEventListener('etapa-validar', (evento) => {
                if (!obrigatorio) return;

                if (!this.temPonto) {
                    evento.preventDefault();
                    evento.detail.erro = 'Escolha o município na busca, use sua localização ou marque o ponto no mapa.';
                } else if (!this.rotulo.trim()) {
                    evento.preventDefault();
                    evento.detail.erro = 'Dê um nome ao local — a cidade ou uma referência da fazenda.';
                    this.$refs.rotulo?.focus();
                }
            });

            this.$watch('termo', () => this.agendarBusca());

            // Propriedade antiga: tem o nome da cidade, mas não o ponto. Já deixa
            // a busca feita com esse nome, para ser só escolher.
            if (!this.temPonto && this.rotulo) {
                this.aviso = 'Esta propriedade ainda não tem o ponto no mapa. Escolha o município na lista ou marque direto no mapa.';
                this.termo = this.rotulo;
            }
        },

        get temPonto() {
            return this.latitude !== null && this.latitude !== '' && this.longitude !== null && this.longitude !== '';
        },

        get coordenadasTexto() {
            if (!this.temPonto) return '';
            const formatar = (valor) => Number(valor).toLocaleString('pt-BR', { minimumFractionDigits: 5, maximumFractionDigits: 5 });
            return `${formatar(this.latitude)}, ${formatar(this.longitude)}`;
        },

        // --- Busca de município -------------------------------------------

        agendarBusca() {
            clearTimeout(temporizador);
            this.semResultado = false;

            if (this.termo.trim().length < 2) {
                this.resultados = [];
                return;
            }

            temporizador = setTimeout(() => this.buscar(), 350);
        },

        async buscar() {
            const termo = this.termo.trim();
            this.buscando = true;

            try {
                const resposta = await fetch(`${urlBusca}?q=${encodeURIComponent(termo)}`, {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                const dados = resposta.ok ? await resposta.json() : { resultados: [] };

                // Descarta a resposta se o usuário já digitou outra coisa.
                if (termo !== this.termo.trim()) return;

                if (!resposta.ok || dados.indisponivel) throw new Error('busca indisponível');

                this.resultados = dados.resultados ?? [];
                this.destacado = this.resultados.length ? 0 : -1;
                this.semResultado = this.resultados.length === 0;
                // Só some o aviso de falha da busca; os demais (ex.: "sem ponto no mapa") continuam.
                if (this.aviso === BUSCA_INDISPONIVEL) this.aviso = null;
            } catch {
                this.resultados = [];
                this.semResultado = false;
                this.aviso = BUSCA_INDISPONIVEL;
            } finally {
                this.buscando = false;
            }
        },

        escolher(local) {
            this.definirPonto(local.latitude, local.longitude, { centralizar: true });
            this.rotulo = local.rotulo;
            this.termo = '';
            this.resultados = [];
            this.aviso = null;
        },

        teclarBusca(evento) {
            if (!this.resultados.length) return;

            if (evento.key === 'ArrowDown') {
                evento.preventDefault();
                this.destacado = (this.destacado + 1) % this.resultados.length;
            } else if (evento.key === 'ArrowUp') {
                evento.preventDefault();
                this.destacado = (this.destacado - 1 + this.resultados.length) % this.resultados.length;
            } else if (evento.key === 'Enter') {
                evento.preventDefault();
                this.escolher(this.resultados[Math.max(this.destacado, 0)]);
            } else if (evento.key === 'Escape') {
                this.resultados = [];
            }
        },

        // --- GPS ----------------------------------------------------------

        usarMinhaLocalizacao() {
            if (!('geolocation' in navigator)) {
                this.aviso = 'Este navegador não informa a localização. Use a busca ou o mapa.';
                return;
            }

            this.localizando = true;
            this.aviso = null;

            navigator.geolocation.getCurrentPosition(
                ({ coords }) => {
                    this.localizando = false;
                    this.definirPonto(coords.latitude, coords.longitude, { centralizar: true });

                    if (!this.rotulo) {
                        this.aviso = 'Ponto marcado. Dê um nome ao local no campo abaixo (cidade ou endereço da fazenda).';
                        this.$nextTick(() => this.$refs.rotulo?.focus());
                    }
                },
                (erro) => {
                    this.localizando = false;
                    this.aviso = erro.code === erro.PERMISSION_DENIED
                        ? 'Permissão de localização negada. Use a busca ou marque o ponto no mapa.'
                        : 'Não foi possível obter sua localização agora. Use a busca ou o mapa.';
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 },
            );
        },

        // --- Mapa -----------------------------------------------------------

        definirPonto(lat, lon, { centralizar = false } = {}) {
            this.latitude = Number(Number(lat).toFixed(6));
            this.longitude = Number(Number(lon).toFixed(6));

            // Avisa quem estiver ouvindo (ex.: resumo da etapa de finalização).
            this.$root.dispatchEvent(new CustomEvent('localidade-alterada', {
                bubbles: true,
                detail: { latitude: this.latitude, longitude: this.longitude },
            }));

            if (!mapa) return;

            const posicao = [this.latitude, this.longitude];
            marcador ? marcador.setLatLng(posicao) : this.criarMarcador(posicao);
            if (centralizar) mapa.setView(posicao, Math.max(mapa.getZoom(), ZOOM_LOCAL));
        },

        /**
         * O mapa só é montado quando o container tem tamanho: dentro de uma etapa
         * escondida ou de uma aba fechada ele mede 0 e o Leaflet desenharia errado.
         */
        observarVisibilidade() {
            const container = this.$refs.mapa;
            if (!container) return;

            new ResizeObserver(([entrada]) => {
                if (entrada.contentRect.width === 0) return;
                mapa ? mapa.invalidateSize() : this.montarMapa();
            }).observe(container);
        },

        async montarMapa() {
            if (montando) return;
            montando = true;

            try {
                L = await carregarLeaflet();
            } catch {
                this.aviso = 'Não foi possível carregar o mapa. A busca de município continua funcionando.';
                return;
            }

            const inicial = this.temPonto ? [this.latitude, this.longitude] : CENTRO_PADRAO;

            mapa = L.map(this.$refs.mapa, { scrollWheelZoom: false })
                .setView(inicial, this.temPonto ? ZOOM_LOCAL : ZOOM_PADRAO);

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 18,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a>',
            }).addTo(mapa);

            if (this.temPonto) this.criarMarcador(inicial);

            mapa.on('click', ({ latlng }) => this.definirPonto(latlng.lat, latlng.lng));
        },

        criarMarcador(posicao) {
            // Ícone em HTML: os PNGs padrão do Leaflet quebram o caminho quando empacotados pelo Vite.
            const icone = L.divIcon({
                className: 'pino-localidade',
                html: '<span></span>',
                // A ponta do pino (quadrado girado 45°) fica ~31px abaixo do topo.
                iconSize: [28, 32],
                iconAnchor: [14, 31],
            });

            marcador = L.marker(posicao, { draggable: true, icon: icone, keyboard: true, title: 'Arraste para ajustar o ponto' })
                .addTo(mapa)
                .on('dragend', () => {
                    const { lat, lng } = marcador.getLatLng();
                    this.definirPonto(lat, lng);
                });
        },

        limpar() {
            this.latitude = null;
            this.longitude = null;
            marcador?.remove();
            marcador = null;
            mapa?.setView(CENTRO_PADRAO, ZOOM_PADRAO);
            this.$root.dispatchEvent(new CustomEvent('localidade-alterada', { bubbles: true, detail: { latitude: null, longitude: null } }));
        },
    };
}
