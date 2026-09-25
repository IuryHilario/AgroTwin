/**
 * Stepper de formulário (Alpine) — par do componente <x-ui.stepper>.
 *
 * Validação de cada etapa, antes de avançar:
 *   1. Validação nativa do HTML (required, min, max, step, pattern...) dos
 *      campos visíveis da etapa — funciona em qualquer formulário sem código
 *      extra.
 *   2. Evento cancelável "etapa-validar" disparado no painel da etapa, para
 *      regras que o HTML não expressa (ex.: "escolha um ponto no mapa").
 *      Quem escuta chama event.preventDefault() e pode preencher
 *      event.detail.erro com a mensagem a mostrar.
 *
 * Erros vindos do servidor (validação AJAX do modal.js ou redirect com
 * $errors) levam o stepper direto para a etapa do primeiro campo inválido.
 *
 * Eventos emitidos no elemento raiz:
 *   "etapa-alterada" — detail: { etapa, anterior }. Útil para conteúdo que
 *   precisa se ajustar ao aparecer (mapa, resumo da revisão...).
 *
 * `resumo` (usado por <x-ui.resumo-etapas>) lista, por etapa, os campos com
 * nome e rótulo e os seus valores — a revisão final se monta sozinha em
 * qualquer formulário. Elementos com data-resumo-rotulo entram também (o
 * valor vem de data-resumo-valor ou do texto); data-resumo-ignorar tira.
 */
export default function stepper({ total, navegacaoLivre = false } = {}) {
    return {
        atual: 1,
        total,
        // Até onde o usuário pode clicar no cabeçalho. Na edição tudo já está
        // preenchido, então todas as etapas ficam liberadas desde o início.
        maxLiberada: navegacaoLivre ? total : 1,
        erro: null,
        resumo: [],

        init() {
            this.form = this.$root.closest('form');

            if (this.form) {
                // O próprio stepper valida etapa por etapa. Sem isto, o navegador
                // tentaria focar um campo inválido de uma etapa escondida e
                // bloquearia o envio sem mostrar nada.
                this.form.noValidate = true;

                // Na fase de captura, antes do listener de submit do modal.js.
                this.form.addEventListener('submit', (evento) => this.aoEnviar(evento), true);
                this.form.addEventListener('validacao-servidor', (evento) => this.irParaCampos(evento.detail?.campos ?? []));
            }

            this.$root.addEventListener('keydown', (evento) => this.aoTeclar(evento));

            // Página recarregada com erros do servidor (redirect com $errors).
            // .invalid-feedback cobre campos escondidos, como as coordenadas.
            this.$nextTick(() => {
                const invalido = this.$root.querySelector('.is-invalid, .invalid-feedback');
                if (invalido) this.irParaElemento(invalido);
            });
        },

        get ultima() {
            return this.atual === this.total;
        },

        painel(numero) {
            return this.$root.querySelector(`[data-etapa="${numero}"]`);
        },

        /** atual · concluida (já passou) · liberada (visitada adiante) · pendente */
        estado(numero) {
            if (numero === this.atual) return 'atual';
            if (numero < this.atual) return 'concluida';
            return numero <= this.maxLiberada ? 'liberada' : 'pendente';
        },

        podeIrPara(numero) {
            return numero >= 1 && numero <= this.maxLiberada;
        },

        avancar() {
            if (!this.validar(this.atual)) return;

            this.maxLiberada = Math.max(this.maxLiberada, this.atual + 1);
            this.irPara(this.atual + 1);
        },

        voltar() {
            this.irPara(this.atual - 1);
        },

        irPara(numero) {
            if (!this.podeIrPara(numero) || numero > this.total || numero === this.atual) return;

            const anterior = this.atual;
            this.atual = numero;
            this.erro = null;
            this.resumo = this.montarResumo();

            this.$nextTick(() => {
                this.$root.dispatchEvent(new CustomEvent('etapa-alterada', {
                    detail: { etapa: numero, anterior },
                }));

                // Leva o foco (e a leitura do leitor de tela) para o título da etapa.
                this.painel(numero)?.querySelector('[data-etapa-titulo]')?.focus({ preventScroll: true });
                this.$root.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        },

        /** @returns {boolean} */
        validar(numero, { mostrar = true } = {}) {
            const painel = this.painel(numero);
            if (!painel) return true;

            const invalido = [...painel.querySelectorAll('input, select, textarea')]
                .find((campo) => campo.willValidate && !campo.checkValidity());

            if (invalido) {
                if (mostrar) {
                    this.erro = null;
                    invalido.reportValidity();
                    invalido.focus();
                }
                return false;
            }

            const evento = new CustomEvent('etapa-validar', {
                cancelable: true,
                detail: { etapa: numero, erro: null },
            });
            painel.dispatchEvent(evento);

            if (evento.defaultPrevented) {
                if (mostrar) this.erro = evento.detail.erro || 'Revise as informações desta etapa antes de continuar.';
                return false;
            }

            this.erro = null;
            return true;
        },

        /**
         * No envio, revalida todas as etapas: alguém pode ter voltado e apagado
         * um campo. A primeira etapa com problema é aberta com o erro visível.
         */
        aoEnviar(evento) {
            for (let numero = 1; numero <= this.total; numero++) {
                if (this.validar(numero, { mostrar: false })) continue;

                evento.preventDefault();
                evento.stopImmediatePropagation();

                this.maxLiberada = Math.max(this.maxLiberada, numero);
                this.irPara(numero);
                // Só depois de a etapa aparecer o navegador consegue mostrar a
                // mensagem (e o foco no campo vence o foco no título da etapa).
                this.$nextTick(() => this.validar(numero));
                return;
            }
        },

        /** Enter em um campo avança a etapa em vez de enviar o formulário pela metade. */
        aoTeclar(evento) {
            const alvo = evento.target;

            if (evento.key !== 'Enter' || this.ultima) return;
            if (alvo.tagName === 'TEXTAREA' || alvo.tagName === 'BUTTON') return;

            // Sempre bloqueia o envio implícito do formulário pelo Enter no meio
            // do caminho. Campos marcados (ex.: busca com lista de sugestões)
            // tratam o Enter sozinhos e não fazem a etapa avançar.
            evento.preventDefault();
            if (alvo.closest('[data-stepper-ignorar-enter]')) return;

            this.avancar();
        },

        /** Campos preenchidos de cada etapa (menos a atual), para a revisão final. */
        montarResumo() {
            return [...this.$root.querySelectorAll('[data-etapa]')]
                .map((painel) => ({
                    etapa: Number(painel.dataset.etapa),
                    titulo: painel.querySelector('[data-etapa-titulo]')?.textContent.trim() || `Etapa ${painel.dataset.etapa}`,
                    campos: this.camposDoResumo(painel),
                }))
                .filter((bloco) => bloco.etapa !== this.atual && bloco.campos.length);
        },

        camposDoResumo(painel) {
            const semAsterisco = (texto) => texto.replace(/\*\s*$/, '').trim();

            return [...painel.querySelectorAll('input[name], select[name], textarea[name], [data-resumo-rotulo]')]
                .filter((campo) => !campo.closest('[data-resumo-ignorar]'))
                .filter((campo) => !['hidden', 'password', 'submit', 'button'].includes(campo.type))
                .filter((campo) => campo.type !== 'radio' || campo.checked)
                .map((campo) => {
                    if (campo.dataset.resumoRotulo) {
                        return {
                            rotulo: campo.dataset.resumoRotulo,
                            valor: (campo.dataset.resumoValor ?? campo.textContent).trim() || null,
                        };
                    }

                    const rotulo = campo.id && painel.querySelector(`label[for="${CSS.escape(campo.id)}"]`);
                    let valor = campo.value;
                    if (campo.tagName === 'SELECT') valor = campo.value ? campo.selectedOptions[0]?.textContent : '';
                    if (campo.type === 'checkbox') valor = campo.checked ? 'Sim' : 'Não';
                    if (campo.type === 'number' && campo.value !== '') valor = Number(campo.value).toLocaleString('pt-BR');

                    return {
                        rotulo: semAsterisco(rotulo?.textContent ?? campo.name),
                        valor: String(valor ?? '').trim() || null,
                    };
                });
        },

        irParaCampos(nomes) {
            const campo = nomes
                .map((nome) => this.form?.querySelector(`[name="${CSS.escape(nome)}"]`))
                .find(Boolean);

            if (campo) this.irParaElemento(campo);
        },

        irParaElemento(elemento) {
            const numero = Number(elemento.closest('[data-etapa]')?.dataset.etapa);
            if (!numero) return;

            this.maxLiberada = Math.max(this.maxLiberada, numero);
            if (numero !== this.atual) this.irPara(numero);
        },
    };
}
