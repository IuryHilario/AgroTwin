// Chart.js é carregado globalmente via CDN em layouts/app.blade.php
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.sensor-report-chart').forEach((canvas) => {
        const labels = JSON.parse(canvas.dataset.labels || '[]').map((data) => {
            const [ano, mes, dia] = data.split('-');
            return dia + '/' + mes;
        });
        const valores = JSON.parse(canvas.dataset.valores || '[]');
        const unidade = canvas.dataset.unidade || '';

        new Chart(canvas, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    data: valores,
                    borderColor: '#16a34a',
                    backgroundColor: 'rgba(22, 163, 74, 0.1)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#16a34a',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => ctx.parsed.y + unidade,
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        grid: { color: 'rgba(148, 163, 184, 0.15)' },
                        ticks: { callback: (value) => value + unidade },
                    },
                    x: {
                        grid: { display: false },
                    },
                },
            },
        });
    });
});
