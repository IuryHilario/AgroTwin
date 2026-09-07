import './bootstrap';

document.addEventListener('alpine:init', () => {
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