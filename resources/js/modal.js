/**
 * Sistema genérico de modais para AgroTwin
 * Segue os princípios KISS (Keep It Simple, Stupid) e DRY (Don't Repeat Yourself)
 *
 */

class ModalManager {
    constructor() {
        this.isLoading = false;
        this.init();
    }

    init() {
        // Previne múltiplas inicializações
        if (window.modalManagerInitialized) {
            return;
        }

        this.bindEvents();
        window.modalManagerInitialized = true;
    }

    bindEvents() {
        // Event delegation para capturar cliques em elementos com data-action
        document.addEventListener('click', (e) => {
            const target = e.target.closest('[data-action]');
            if (!target) return;

            e.preventDefault(); // Previne o comportamento padrão de recarregar a página

            const action = target.getAttribute('data-action');
            const url = target.getAttribute('href') || target.getAttribute('data-url');

            if (!url) {
                console.error('URL não encontrada para a ação:', action);
                return;
            }

            this.handleAction(action, url, target);
        });

        // Event delegation para o submit de qualquer formulário marcado como
        // "ajax-form" — telas normais de inserir/editar e formulários dentro de
        // modais usam o mesmo caminho (ver <x-form.form> e <x-form.form-modal>).
        document.addEventListener('submit', (e) => {
            if (e.target.matches('form.ajax-form')) {
                e.preventDefault();
                this.handleFormSubmit(e);
            }
        });
    }

    handleAction(action, url, element) {
        switch(action) {
            case 'editar':
                // Para edição, redireciona normalmente (semelhante ao inserir)
                window.location.href = url;
                break;
            case 'inativar':
                this.confirmAndExecute(url, 'Deseja realmente inativar esta propriedade?');
                break;
            case 'ativar':
                this.confirmAndExecute(url, 'Deseja realmente ativar esta propriedade?');
                break;
            case 'excluir':
                this.confirmAndExecute(url, 'Deseja realmente excluir este registro? Essa ação não pode ser desfeita.', 'DELETE');
                break;
            case 'novo':
                // Carrega o modal via AJAX para evitar recarregar a página
                this.loadModal(url, element);
                break;
            default:
                this.loadModal(url, element);
        }
    }

    /**
     * Pede confirmação ao usuário e, se confirmado, executa a ação via AJAX
     * (usada por ações de estado como inativar/ativar/excluir). Mostra o
     * resultado num modal e recarrega a página ao fechar (Ok), refletindo o
     * novo estado na listagem. Métodos diferentes de GET enviam o token CSRF.
     */
    confirmAndExecute(url, confirmMessage, method = 'GET') {
        this.showConfirmModal(confirmMessage, async () => {
            try {
                this.showLoading();

                const headers = {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                };

                if (method !== 'GET') {
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    if (token) {
                        headers['X-CSRF-TOKEN'] = token;
                    }
                }

                const response = await fetch(url, { method, headers });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.showResultModal(true, data.message || 'Operação realizada com sucesso!', () => {
                        window.location.reload();
                    });
                } else {
                    this.showResultModal(false, data.message || 'Erro ao processar a solicitação.');
                }
            } catch (error) {
                console.error('Erro ao executar ação:', error);
                this.showResultModal(false, 'Erro ao processar a solicitação. Tente novamente.');
            } finally {
                this.hideLoading();
            }
        });
    }

    async loadModal(url, triggerElement) {
        // Previne múltiplas execuções simultâneas
        if (this.isLoading) {
            return;
        }

        try {
            this.isLoading = true;
            this.showLoading();

            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json, text/javascript, */*; q=0.01',
                    'Content-Type': 'application/json',
                }
            });

            if (!response.ok) {
                // Tenta extrair mensagem de erro do servidor
                let errorMessage = `HTTP ${response.status}: ${response.statusText}`;
                try {
                    const errorData = await response.json();
                    if (errorData.message) {
                        errorMessage = errorData.message;
                    }
                } catch (e) {
                    // Se não conseguir fazer parse de JSON, usa a mensagem padrão
                }
                throw new Error(errorMessage);
            }

            const data = await response.json();

            if (data.success && data.html) {
                this.showModal(data.html, data);
            } else {
                throw new Error(data.message || 'Erro ao carregar o modal');
            }

        } catch (error) {
            console.error('Erro ao carregar modal:', error);

            // Monta mensagem de erro mais descritiva
            let userMessage = 'Erro ao carregar os dados. Tente novamente.';
            if (error.message.includes('HTTP 500')) {
                userMessage = 'Erro do servidor (500). Por favor, contacte o administrador.';
            } else if (error.message.includes('HTTP 404')) {
                userMessage = 'Recurso não encontrado.';
            } else if (error.message.includes('HTTP 403')) {
                userMessage = 'Você não tem permissão para acessar este recurso.';
            } else if (error.message) {
                userMessage = error.message;
            }

            this.showResultModal(false, userMessage);
        } finally {
            this.isLoading = false;
            this.hideLoading();
        }
    }

    showModal(html, data = {}) {
        // Remove modais existentes e aguarda um pouco
        this.closeAllModals();

        // Aguarda um pequeno delay para garantir que os modais anteriores fecharam
        setTimeout(() => {
            // Cria o container do modal
            const modalContainer = document.createElement('div');
            modalContainer.innerHTML = html;
            document.body.appendChild(modalContainer);

            // Encontra o modal criado (o Alpine inicializa o x-data="{ open: true }" sozinho)
            const modal = modalContainer.querySelector('.modal');
            if (!modal) {
                console.error('Modal não encontrado no HTML retornado');
                return;
            }

            // Cliques em [data-action] e submits de .ajax-form dentro do modal já
            // são cobertos pela delegação em bindEvents() — nada a religar aqui.

            // O modal dispara 'modal-closed' (via $dispatch no Alpine) ao fechar
            // pelo backdrop, botão de fechar ou Esc. Remove do DOM após a transição de saída.
            modal.addEventListener('modal-closed', () => {
                setTimeout(() => modalContainer.remove(), 200);
            });
        }, 100);
    }

    async handleFormSubmit(e) {
        e.preventDefault();

        const form = e.target;

        // Previne múltiplos envios do mesmo formulário
        if (form.dataset.submitting === 'true') {
            return;
        }

        form.dataset.submitting = 'true';

        const formData = new FormData(form);
        const url = form.action;
        const method = form.method || 'POST';

        try {
            this.showLoading();

            const response = await fetch(url, {
                method: method.toUpperCase(),
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });

            // Os controllers desta aplicação, ao salvar com sucesso, respondem com um
            // redirect() (padrão usado em toda a app, fora do fluxo AJAX). O fetch já
            // segue esse redirect, retornando a página HTML final — não um JSON. Só as
            // respostas de erro/validação (422, etc.) retornam JSON de fato.
            const contentType = response.headers.get('content-type') || '';
            const isJson = contentType.includes('application/json');

            if (isJson) {
                const data = await response.json();

                if (data.success) {
                    this.showResultModal(true, data.message || 'Operação realizada com sucesso!', () => {
                        window.location.href = response.url;
                    });
                } else {
                    this.showResultModal(false, data.message || 'Erro ao processar a solicitação.');

                    // Se há erros de validação, mostra eles
                    if (data.errors) {
                        this.showValidationErrors(form, data.errors);
                    }
                }
            } else if (response.ok) {
                // response.url já é a URL final após o redirect (ex.: a tela de
                // listagem), tanto para telas normais de inserir/editar quanto
                // para formulários dentro de modais (nesse caso, normalmente a
                // própria página atual — equivalente a um reload).
                this.showResultModal(true, 'Operação realizada com sucesso!', () => {
                    window.location.href = response.url;
                });
            } else {
                this.showResultModal(false, 'Erro ao processar a solicitação. Tente novamente.');
            }

        } catch (error) {
            console.error('Erro ao enviar formulário:', error);
            this.showResultModal(false, 'Erro ao processar a solicitação. Tente novamente.');
        } finally {
            this.hideLoading();
            // Limpa o flag de submissão
            form.dataset.submitting = 'false';
        }
    }

    showValidationErrors(form, errors) {
        // Remove erros anteriores
        form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

        // Adiciona novos erros
        Object.keys(errors).forEach(field => {
            const input = form.querySelector(`[name="${field}"]`);
            if (input) {
                input.classList.add('is-invalid');

                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback';
                errorDiv.textContent = errors[field][0];

                input.parentNode.appendChild(errorDiv);
            }
        });
    }

    closeAllModals() {
        document.querySelectorAll('.modal').forEach(modal => {
            modal.parentElement?.remove();
        });
    }

    showLoading() {
        // Remove loading anterior se existir
        this.hideLoading();

        const loading = document.createElement('div');
        loading.id = 'modal-loading';
        loading.className = 'fixed inset-0 z-[2000] flex items-center justify-center bg-black/50';
        loading.innerHTML = `
            <div class="flex flex-col items-center gap-3 rounded-lg bg-white p-6 shadow-lg">
                <div class="h-8 w-8 animate-spin rounded-full border-4 border-green-600 border-t-transparent"></div>
                <div class="text-sm text-gray-600">Carregando...</div>
            </div>
        `;

        document.body.appendChild(loading);
    }

    hideLoading() {
        const loading = document.getElementById('modal-loading');
        if (loading) {
            loading.remove();
        }
    }

    /**
     * Modal de resultado (sucesso ou erro) com botão "Ok". No sucesso, o
     * callback (normalmente fechar os modais e recarregar a página) só roda
     * quando o usuário clica em Ok — nunca automaticamente.
     */
    showResultModal(success, message, onOk = null) {
        this.closeResultModal();

        const overlay = document.createElement('div');
        overlay.id = 'modal-result';
        overlay.className = 'fixed inset-0 z-[2000] flex items-center justify-center bg-black/50 p-4';
        overlay.innerHTML = `
            <div class="w-full max-w-sm rounded-xl bg-white shadow-2xl dark:bg-gray-800">
                <div class="flex flex-col items-center gap-3 p-6 text-center">
                    <div class="flex h-14 w-14 items-center justify-center rounded-full ${success ? 'bg-green-100 dark:bg-green-900/40' : 'bg-red-100 dark:bg-red-900/40'}">
                        <i class="fas ${success ? 'fa-check text-green-600 dark:text-green-400' : 'fa-times text-red-600 dark:text-red-400'} text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">${success ? 'Sucesso!' : 'Erro'}</h3>
                    <p class="modal-result-message text-sm text-gray-600 dark:text-gray-400"></p>
                </div>
                <div class="flex justify-center border-t border-gray-100 p-4 dark:border-gray-700">
                    <button type="button" class="btn ${success ? 'btn-success' : 'btn-danger'} modal-result-ok">Ok</button>
                </div>
            </div>
        `;

        // Via textContent (não innerHTML) para não interpretar a mensagem como HTML.
        overlay.querySelector('.modal-result-message').textContent = message;

        const okButton = overlay.querySelector('.modal-result-ok');
        okButton.addEventListener('click', () => {
            overlay.remove();
            if (onOk) {
                onOk();
            }
        });

        document.body.appendChild(overlay);
        okButton.focus();
    }

    closeResultModal() {
        document.getElementById('modal-result')?.remove();
    }

    /**
     * Modal de confirmação (Sim/Não) antes de executar uma ação. onConfirm só
     * roda se o usuário clicar em "Sim"; "Não" ou fechar apenas descarta.
     */
    showConfirmModal(message, onConfirm) {
        document.getElementById('modal-confirm')?.remove();

        const overlay = document.createElement('div');
        overlay.id = 'modal-confirm';
        overlay.className = 'fixed inset-0 z-[2000] flex items-center justify-center bg-black/50 p-4';
        overlay.innerHTML = `
            <div class="w-full max-w-sm rounded-xl bg-white shadow-2xl dark:bg-gray-800">
                <div class="flex flex-col items-center gap-3 p-6 text-center">
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-900/40">
                        <i class="fas fa-circle-question text-amber-600 dark:text-amber-400 text-2xl"></i>
                    </div>
                    <p class="modal-confirm-message text-sm text-gray-600 dark:text-gray-400"></p>
                </div>
                <div class="flex justify-center gap-2 border-t border-gray-100 p-4 dark:border-gray-700">
                    <button type="button" class="btn btn-outline modal-confirm-no">Não</button>
                    <button type="button" class="btn btn-success modal-confirm-yes">Sim</button>
                </div>
            </div>
        `;

        overlay.querySelector('.modal-confirm-message').textContent = message;

        const close = () => overlay.remove();
        overlay.querySelector('.modal-confirm-no').addEventListener('click', close);
        overlay.querySelector('.modal-confirm-yes').addEventListener('click', () => {
            close();
            onConfirm();
        });

        document.body.appendChild(overlay);
        overlay.querySelector('.modal-confirm-yes').focus();
    }
}

// Inicializa o sistema quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    // Previne múltiplas inicializações
    if (!window.modalManager) {
        window.modalManager = new ModalManager();
    }
});

// Exporta para uso global
window.ModalManager = ModalManager;
