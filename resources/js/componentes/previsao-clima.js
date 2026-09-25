/**
 * Prévia do tempo no ponto escolhido (Alpine) — par de <x-ui.previsao-clima>.
 *
 * Lê a latitude/longitude dos campos do próprio formulário e consulta
 * /localidades/clima (Open-Meteo no servidor). Só busca quando o bloco está
 * visível e o ponto mudou desde a última consulta — dentro de um stepper,
 * isso é quando a etapa aparece.
 */
export default function previsaoClima({ url, campoLatitude = 'nu_latitude', campoLongitude = 'nu_longitude' }) {
    let consultado = null;

    return {
        clima: null,
        carregando: false,
        falhou: false,
        semPonto: false,

        init() {
            this.form = this.$root.closest('form');

            new ResizeObserver(([entrada]) => {
                if (entrada.contentRect.width > 0) this.carregar();
            }).observe(this.$root);

            this.form?.addEventListener('localidade-alterada', () => {
                if (this.$root.offsetWidth > 0) this.carregar();
            });
        },

        ponto() {
            const valor = (nome) => this.form?.querySelector(`[name="${nome}"]`)?.value ?? '';
            const latitude = valor(campoLatitude);
            const longitude = valor(campoLongitude);

            return latitude !== '' && longitude !== '' ? { latitude, longitude } : null;
        },

        async carregar() {
            const ponto = this.ponto();
            this.semPonto = !ponto;
            if (!ponto) {
                this.clima = null;
                return;
            }

            const chave = `${ponto.latitude},${ponto.longitude}`;
            if (chave === consultado) return;
            consultado = chave;

            this.carregando = true;
            this.falhou = false;

            try {
                const resposta = await fetch(`${url}?${new URLSearchParams(ponto)}`, {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                const dados = resposta.ok ? await resposta.json() : {};

                if (chave !== consultado) return; // o ponto mudou no meio do caminho

                this.clima = dados.clima ?? null;
                this.falhou = !this.clima;
            } catch {
                this.clima = null;
                this.falhou = true;
                consultado = null; // permite tentar de novo
            } finally {
                this.carregando = false;
            }
        },

        numero(valor, casas = 0) {
            return valor === null || valor === undefined
                ? '—'
                : Number(valor).toLocaleString('pt-BR', { maximumFractionDigits: casas });
        },
    };
}
