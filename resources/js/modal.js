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
            console.log(`Ação: ${action}, URL: ${url}`);
        });

    }

    handleAction(action, url, element) {
        switch(action) {
            case 'editar':
                // Para edição, redireciona normalmente (semelhante ao inserir)
                window.location.href = url;
                break;
            case 'novo':
                // Carrega o modal via AJAX para evitar recarregar a página
                this.loadModal(url, element);
                break;
            default:
                this.loadModal(url, element);
        }
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

            this.showError(userMessage);
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

            // Encontra o modal criado
            const modal = modalContainer.querySelector('.modal');
            if (!modal) {
                console.error('Modal não encontrado no HTML retornado');
                return;
            }

            // Inicializa o modal Bootstrap
            const bsModal = new bootstrap.Modal(modal, {
                backdrop: 'static',
                keyboard: true
            });

            // Armazena referência para cleanup
            modal._bsModal = bsModal;

            // Remove eventos anteriores para evitar duplicação
            this.removeModalEvents(modal);

            // Bind eventos do modal (sem duplicação)
            this.bindModalEvents(modal);

            // Mostra o modal
            bsModal.show();

            // Remove do DOM quando fechar
            modal.addEventListener('hidden.bs.modal', () => {
                modalContainer.remove();
            });
        }, 100);
    }

    removeModalEvents(modal) {
        // Remove event listeners anteriores se existirem
        const forms = modal.querySelectorAll('form');
        forms.forEach(form => {
            if (form._modalEventsBound) {
                form.removeEventListener('submit', form._modalSubmitHandler);
                form._modalEventsBound = false;
            }
        });

        const actionButtons = modal.querySelectorAll('[data-action]');
        actionButtons.forEach(button => {
            if (button._modalEventsBound) {
                button.removeEventListener('click', button._modalClickHandler);
                button._modalEventsBound = false;
            }
        });
    }

    bindModalEvents(modal) {
        // Bind para formulários dentro do modal (apenas se não foi feito ainda)
        const forms = modal.querySelectorAll('form');
        forms.forEach(form => {
            if (!form._modalEventsBound) {
                form._modalSubmitHandler = (e) => {
                    e.preventDefault();
                    this.handleFormSubmit(e);
                };
                form.addEventListener('submit', form._modalSubmitHandler);
                form._modalEventsBound = true;
            }
        });

        // Bind para botões que carregam outros modais (apenas se não foi feito ainda)
        const actionButtons = modal.querySelectorAll('[data-action]');
        actionButtons.forEach(button => {
            if (!button._modalEventsBound) {
                button._modalClickHandler = (e) => {
                    e.preventDefault();
                    const action = button.getAttribute('data-action');
                    const url = button.getAttribute('href') || button.getAttribute('data-url');

                    if (url) {
                        this.handleAction(action, url, button);
                    }
                };
                button.addEventListener('click', button._modalClickHandler);
                button._modalEventsBound = true;
            }
        });
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

            const data = await response.json();

            if (data.success) {
                this.showSuccess(data.message || 'Operação realizada com sucesso!');
                this.closeAllModals();

                // Recarrega a página após um tempo
                setTimeout(() => {
                    window.location.reload();
                }, 1500);

            } else {
                this.showError(data.message || 'Erro ao processar a solicitação');

                // Se há erros de validação, mostra eles
                if (data.errors) {
                    this.showValidationErrors(form, data.errors);
                }
            }

        } catch (error) {
            console.error('Erro ao enviar formulário:', error);
            this.showError('Erro ao processar a solicitação. Tente novamente.');
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
        const modals = document.querySelectorAll('.modal.show');
        modals.forEach(modal => {
            if (modal._bsModal) {
                modal._bsModal.hide();
            }
        });
    }

    showLoading() {
        // Remove loading anterior se existir
        this.hideLoading();

        const loading = document.createElement('div');
        loading.id = 'modal-loading';
        loading.className = 'modal-loading';
        loading.innerHTML = `
            <div class="modal-loading-backdrop">
                <div class="modal-loading-content">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <div class="mt-2">Carregando...</div>
                </div>
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

    showSuccess(message) {
        this.showToast(message, 'success');
    }

    showError(message) {
        this.showToast(message, 'error');
    }

    showToast(message, type = 'info') {
        // Remove toasts anteriores
        document.querySelectorAll('.modal-toast').forEach(toast => toast.remove());

        const toast = document.createElement('div');
        toast.className = `modal-toast toast-${type}`;
        toast.innerHTML = `
            <div class="toast-content">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'}"></i>
                <span>${message}</span>
            </div>
        `;

        document.body.appendChild(toast);

        // Auto remove
        setTimeout(() => {
            toast.remove();
        }, 5000);
    }
}

// CSS para loading e toasts
const style = document.createElement('style');
style.textContent = `
    .modal-loading {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 9999;
    }

    .modal-loading-backdrop {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-loading-content {
        background: white;
        padding: 20px;
        border-radius: 8px;
        text-align: center;
    }

    .modal-toast {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 10000;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        padding: 16px;
        max-width: 300px;
        animation: slideIn 0.3s ease-out;
    }

    .modal-toast.toast-success {
        border-left: 4px solid #28a745;
    }

    .modal-toast.toast-error {
        border-left: 4px solid #dc3545;
    }

    .toast-content {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .toast-content i {
        font-size: 18px;
    }

    .toast-success .toast-content i {
        color: #28a745;
    }

    .toast-error .toast-content i {
        color: #dc3545;
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
`;
document.head.appendChild(style);

// Inicializa o sistema quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    // Previne múltiplas inicializações
    if (!window.modalManager) {
        window.modalManager = new ModalManager();
    }
});

// Exporta para uso global
window.ModalManager = ModalManager;
