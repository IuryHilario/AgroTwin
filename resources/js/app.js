import './bootstrap';
import stepper from './componentes/stepper';
import seletorLocalidade from './componentes/localidade';
import previsaoClima from './componentes/previsao-clima';

// Componentes Alpine reutilizáveis. Precisam ser registrados aqui, no bundle
// carregado antes do Alpine: scripts empilhados pelas páginas (@push) rodam
// depois que o Alpine já iniciou e perderiam o evento alpine:init.
document.addEventListener('alpine:init', () => {
    Alpine.data('stepper', stepper);
    Alpine.data('seletorLocalidade', seletorLocalidade);
    Alpine.data('previsaoClima', previsaoClima);

    Alpine.data('sidebarNav', () => ({
        sidebarOpen: false,

        init() {
            // Fechar sidebar ao redimensionar tela para desktop
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1024) {
                    this.sidebarOpen = false;
                }
            });

            // Fechar sidebar ao clicar em links no mobile
            document.addEventListener('click', (e) => {
                if (e.target.closest('.sidebar .menu-link') && window.innerWidth < 1024) {
                    setTimeout(() => { this.sidebarOpen = false; }, 100);
                }
            });
        },
    }));
});

// Usado pelo botão "Enviar por Email" do relatório de insumo (modal carregado
// via AJAX — scripts embutidos no HTML do modal não rodam, então essa função
// precisa estar no bundle global carregado no layout).
window.enviarRelatorioInsumoEmail = async function (idInsumo, button) {
    const original = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Enviando...';

    try {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
        const response = await fetch(`/insumos/relatorio/${idInsumo}/email`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json',
            },
        });
        const data = await response.json();
        alert(data.message || 'Não foi possível enviar o relatório.');
    } catch (error) {
        alert('Erro ao enviar o relatório por email.');
    } finally {
        button.disabled = false;
        button.innerHTML = original;
    }
};