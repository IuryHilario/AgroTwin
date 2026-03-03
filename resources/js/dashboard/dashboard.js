// Dashboard functionality
document.addEventListener('DOMContentLoaded', function() {
    initializeDashboard();
});

function initializeDashboard() {
    // Initialize sidebar toggle
    initializeSidebar();

    // Initialize property selector
    initializePropertySelector();

    // Initialize charts
    initializeCharts();

    // Initialize real-time updates
    initializeRealTimeUpdates();

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
            <div class="loading-spinner">
                <i class="fas fa-spinner fa-spin" style="font-size: 24px; color: #3b82f6; margin-bottom: 10px;"></i>
                <br>
                <span>Carregando dados da propriedade...</span>
            </div>
        `;
        document.body.appendChild(loadingDiv);
    }
}

// Update dashboard data based on selected property
function updateDashboardData(propertyId) {
    // Simulate data for different properties
    const propertyData = {
        'fazenda-santa-clara-milho': {
            name: 'Fazenda Santa Clara - Plantação de Milho',
            moisture: 41,
            ph: 6.2,
            temperature: 24,
            npk: '12-5-10',
            status: 'Solo em condição ideal'
        },
        'fazenda-santa-clara-soja': {
            name: 'Fazenda Santa Clara - Plantação de Soja',
            moisture: 38,
            ph: 6.8,
            temperature: 26,
            npk: '10-8-12',
            status: 'Solo em boa condição'
        },
        'sitio-boa-vista-cafe': {
            name: 'Sítio Boa Vista - Plantação de Café',
            moisture: 45,
            ph: 5.8,
            temperature: 22,
            npk: '15-6-8',
            status: 'Solo necessita correção de pH'
        },
        'chacara-esperanca-tomate': {
            name: 'Chácara Esperança - Plantação de Tomate',
            moisture: 52,
            ph: 6.5,
            temperature: 28,
            npk: '18-10-15',
            status: 'Solo em excelente condição'
        }
    };

    const data = propertyData[propertyId];
    if (data) {
        // Update header
        document.querySelector('.property-selector h1').innerHTML =
            `<i class="fas fa-map-marked-alt"></i> ${data.name}`;

        // Update indicators
        updateIndicator('moisture', data.moisture, '%');
        updateIndicator('ph', data.ph, '');
        updateIndicator('temperature', data.temperature, '°C');
        updateIndicator('npk', data.npk, '');

        // Update status
        document.querySelector('.status-card span').textContent = data.status;

        // Update charts with new data
        updateCharts(propertyId);
    }
}

// Update individual indicator
function updateIndicator(type, value, unit) {
    const indicators = {
        'moisture': '.indicator-card:nth-child(1)',
        'ph': '.indicator-card:nth-child(2)',
        'temperature': '.indicator-card:nth-child(3)',
        'npk': '.indicator-card:nth-child(4)'
    };

    const selector = indicators[type];
    if (selector) {
        const card = document.querySelector(selector);
        if (card) {
            card.querySelector('.value').textContent = value;
            card.querySelector('.unit').textContent = unit;
        }
    }
}

// Initialize charts
function initializeCharts() {
    createMoistureChart();
    createPhChart();
}

// Create moisture chart
function createMoistureChart() {
    const ctx = document.getElementById('moistureChart');
    if (!ctx) return;

    const moistureData = {
        labels: ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
        datasets: [{
            label: 'Umidade (%)',
            data: [38, 42, 39, 45, 41, 44, 41],
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#3b82f6',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 6
        }]
    };

    new Chart(ctx, {
        type: 'line',
        data: moistureData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    min: 30,
                    max: 50,
                    grid: {
                        color: '#f3f4f6'
                    },
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            elements: {
                point: {
                    hoverRadius: 8
                }
            }
        }
    });
}

// Create pH chart
function createPhChart() {
    const ctx = document.getElementById('phChart');
    if (!ctx) return;

    const phData = {
        labels: ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
        datasets: [{
            label: 'pH',
            data: [6.0, 6.1, 6.3, 6.2, 6.2, 6.1, 6.2],
            borderColor: '#8b5cf6',
            backgroundColor: 'rgba(139, 92, 246, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#8b5cf6',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 6
        }]
    };

    new Chart(ctx, {
        type: 'line',
        data: phData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    min: 5.5,
                    max: 7.0,
                    grid: {
                        color: '#f3f4f6'
                    },
                    ticks: {
                        stepSize: 0.2
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            elements: {
                point: {
                    hoverRadius: 8
                }
            }
        }
    });
}

// Update charts with new data
function updateCharts(propertyId) {
    // Different data sets for different properties
    const chartData = {
        'fazenda-santa-clara-milho': {
            moisture: [38, 42, 39, 45, 41, 44, 41],
            ph: [6.0, 6.1, 6.3, 6.2, 6.2, 6.1, 6.2]
        },
        'fazenda-santa-clara-soja': {
            moisture: [35, 38, 36, 40, 38, 41, 38],
            ph: [6.5, 6.7, 6.8, 6.8, 6.9, 6.8, 6.8]
        },
        'sitio-boa-vista-cafe': {
            moisture: [42, 45, 43, 48, 45, 47, 45],
            ph: [5.6, 5.7, 5.8, 5.8, 5.9, 5.8, 5.8]
        },
        'chacara-esperanca-tomate': {
            moisture: [50, 52, 51, 55, 52, 54, 52],
            ph: [6.3, 6.4, 6.5, 6.5, 6.6, 6.5, 6.5]
        }
    };

    const data = chartData[propertyId];
    if (data) {
        // Update moisture chart
        const moistureChart = Chart.getChart('moistureChart');
        if (moistureChart) {
            moistureChart.data.datasets[0].data = data.moisture;
            moistureChart.update();
        }

        // Update pH chart
        const phChart = Chart.getChart('phChart');
        if (phChart) {
            phChart.data.datasets[0].data = data.ph;
            phChart.update();
        }
    }
}

// Initialize real-time updates
function initializeRealTimeUpdates() {
    // Simulate real-time data updates every 30 seconds
    setInterval(function() {
        updateLastUpdateTime();
        simulateDataChanges();
    }, 30000);
}

// Update last update time
function updateLastUpdateTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('pt-BR', {
        hour: '2-digit',
        minute: '2-digit'
    });

    const lastUpdateElement = document.querySelector('.last-update strong');
    if (lastUpdateElement) {
        lastUpdateElement.textContent = timeString;
    }
}

// Simulate small data changes
function simulateDataChanges() {
    // Simulate moisture change
    const moistureValue = document.querySelector('.indicator-card:first-child .value');
    if (moistureValue) {
        const currentValue = parseFloat(moistureValue.textContent);
        const newValue = currentValue + (Math.random() - 0.5) * 2;
        moistureValue.textContent = Math.round(newValue * 10) / 10;
    }

    // Simulate temperature change
    const tempValue = document.querySelector('.indicator-card:nth-child(3) .value');
    if (tempValue) {
        const currentValue = parseFloat(tempValue.textContent);
        const newValue = currentValue + (Math.random() - 0.5) * 1;
        tempValue.textContent = Math.round(newValue);
    }
}

// Initialize alert interactions
function initializeAlertInteractions() {
    // Add click handlers for alert items
    const alertItems = document.querySelectorAll('.alert-item');
    alertItems.forEach(item => {
        item.addEventListener('click', function() {
            const alertTitle = this.querySelector('.alert-title').textContent;
            showAlertDetails(alertTitle);
        });
    });

    // Add click handler for "Ver Todos os Alertas" button
    const viewAllButton = document.querySelector('.view-all-alerts');
    if (viewAllButton) {
        viewAllButton.addEventListener('click', function() {
            // Redirect to alerts page or show modal
            console.log('Navegando para página de alertas...');
        });
    }

    // Add click handlers for recommendation buttons
    const applyButton = document.querySelector('.btn-primary');
    if (applyButton) {
        applyButton.addEventListener('click', function() {
            showRecommendationModal('apply');
        });
    }

    const simulateButton = document.querySelector('.btn-secondary');
    if (simulateButton) {
        simulateButton.addEventListener('click', function() {
            showRecommendationModal('simulate');
        });
    }
}

// Show alert details
function showAlertDetails(alertTitle) {
    alert(`Detalhes do alerta: ${alertTitle}\n\nEsta funcionalidade será implementada em uma modal ou página dedicada.`);
}

// Show recommendation modal
function showRecommendationModal(action) {
    const actionText = action === 'apply' ? 'aplicar' : 'simular';
    alert(`Você escolheu ${actionText} a recomendação.\n\nEsta funcionalidade será implementada com uma interface mais detalhada.`);
}

// Utility function to format numbers
function formatNumber(num, decimals = 1) {
    return Number(num).toFixed(decimals);
}

// Export functions for global access
window.AgroTwin = {
    updateDashboardData,
    initializeCharts,
    updateCharts,
    formatNumber
};
