// Dashboard functionality
document.addEventListener('DOMContentLoaded', function() {
    initializeDashboard();
});

function initializeDashboard() {
    // Initialize sidebar toggle
    initializeSidebar();

    // Initialize property selector
    initializePropertySelector();

    // Initialize lavoura selector
    initializeLavouraSelector();

    // Initialize charts
    initializeCharts();

    // Initialize alert interactions
    initializeAlertInteractions();
}

// Sidebar functionality
function initializeSidebar() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 1024) {
                if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
                    sidebar.classList.remove('active');
                }
            }
        });
    }
}

// Property selector functionality
function initializePropertySelector() {
    const propertySelector = document.getElementById('propertySelector');

    if (propertySelector) {
        propertySelector.addEventListener('change', function() {
            const selectedPropertyId = this.value;
            if (selectedPropertyId) {
                // Mostrar loading enquanto troca de propriedade
                showPropertyLoading();

                // Redirecionar para a nova propriedade
                window.location.href = `${window.location.pathname}?id_propriedade=${selectedPropertyId}`;
            }
        });
    }
}

// Lavoura selector functionality
function initializeLavouraSelector() {
    const lavouraSelector = document.getElementById('lavouraSelector');
    const propertySelector = document.getElementById('propertySelector');

    if (lavouraSelector && propertySelector) {
        lavouraSelector.addEventListener('change', function() {
            const selectedLavouraId = this.value;
            const selectedPropertyId = propertySelector.value;

            showPropertyLoading();

            window.location.href = `${window.location.pathname}?id_propriedade=${selectedPropertyId}&id_lavoura=${selectedLavouraId}`;
        });
    }
}

/**
 * Mostra indicador de loading durante a troca de propriedade
 */
function showPropertyLoading() {
    const dashboardContainer = document.querySelector('.dashboard-container');
    if (dashboardContainer) {
        dashboardContainer.style.opacity = '0.7';
        dashboardContainer.style.pointerEvents = 'none';

        // Adicionar spinner de loading
        const loadingDiv = document.createElement('div');
        loadingDiv.className = 'property-loading';
        loadingDiv.style.cssText = `
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 9999;
            text-align: center;
        `;
        loadingDiv.innerHTML = `
            <div class="loading-spinner" style="display: flex; flex-direction: column; align-items: center; gap: 15px;">
                <i class="fas fa-spinner fa-spin" style="font-size: 24px; color: #3b82f6;"></i>
                <span style="color: #6b7280; font-weight: 500; font-size: 14px;">Carregando dados da propriedade...</span>
            </div>
        `;
        document.body.appendChild(loadingDiv);
    }
}

// Initialize charts
function initializeCharts() {
    createMoistureChart();
    createPhChart();
}

// Lê os data-* injetados pelo Blade (série real vinda do SensorReadingService)
// e formata as datas ISO (Y-m-d) como dd/mm para exibir no eixo X.
function lerSerieDoCanvas(ctx) {
    const labels = JSON.parse(ctx.dataset.labels || '[]');
    const valores = JSON.parse(ctx.dataset.valores || '[]');

    return {
        labels: labels.map(function(data) {
            const [ano, mes, dia] = data.split('-');
            return dia + '/' + mes;
        }),
        valores: valores,
    };
}

/**
 * Número do gráfico com no máximo uma casa decimal, no formato pt-BR.
 * O Chart.js calcula as marcas do eixo dividindo o intervalo, e numa faixa
 * estreita (o pH varia entre 5,1 e 5,6) isso cai em ponto flutuante do tipo
 * 5.140000000000001 — que ia direto pro eixo sem nenhuma formatação.
 */
const formatarNumero = new Intl.NumberFormat('pt-BR', { maximumFractionDigits: 1 }).format;

/**
 * Opções compartilhadas pelos gráficos do dashboard: eixo limpo, fonte
 * monoespaçada nos números (mesma dos medidores) e tooltip com a unidade.
 */
function opcoesGrafico(unidade) {
    const cinza = 'rgba(148, 163, 184, 0.7)';

    return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                displayColors: false,
                padding: 10,
                backgroundColor: 'rgba(17, 24, 39, 0.92)',
                titleFont: { family: "'IBM Plex Mono', monospace", size: 11 },
                bodyFont: { family: "'IBM Plex Mono', monospace", size: 13, weight: '600' },
                callbacks: {
                    label: (item) => formatarNumero(item.parsed.y) + unidade,
                },
            },
        },
        scales: {
            y: {
                beginAtZero: false,
                border: { display: false },
                grid: { color: 'rgba(148, 163, 184, 0.15)' },
                ticks: {
                    color: cinza,
                    font: { family: "'IBM Plex Mono', monospace", size: 11 },
                    callback: (value) => formatarNumero(value) + unidade,
                },
            },
            x: {
                border: { display: false },
                grid: { display: false },
                ticks: {
                    color: cinza,
                    font: { family: "'IBM Plex Mono', monospace", size: 11 },
                },
            },
        },
        elements: {
            point: { hoverRadius: 7 },
        },
    };
}

function dadosLinha(serie, cor, corArea) {
    return {
        labels: serie.labels,
        datasets: [{
            data: serie.valores,
            borderColor: cor,
            backgroundColor: corArea,
            borderWidth: 2.5,
            fill: true,
            tension: 0.35,
            pointBackgroundColor: cor,
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 4,
        }],
    };
}

// Create moisture chart
function createMoistureChart() {
    const ctx = document.getElementById('moistureChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'line',
        data: dadosLinha(lerSerieDoCanvas(ctx), '#0ea5e9', 'rgba(14, 165, 233, 0.12)'),
        options: opcoesGrafico('%'),
    });
}

// Create pH chart
function createPhChart() {
    const ctx = document.getElementById('phChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'line',
        data: dadosLinha(lerSerieDoCanvas(ctx), '#16a34a', 'rgba(22, 163, 74, 0.12)'),
        options: opcoesGrafico(''),
    });
}

// Initialize alert interactions
function initializeAlertInteractions() {
    // Os itens de alerta e o botão "Ver Todos os Alertas" já são links reais
    // (para marcar como lido / para a listagem completa), não precisam de JS.
}
