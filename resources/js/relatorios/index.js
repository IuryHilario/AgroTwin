// Chart.js é carregado globalmente via CDN em layouts/app.blade.php
const FORA_DA_FAIXA = '#f43f5e';
const DENTRO_DA_FAIXA = '#16a34a';

// Uma casa decimal, em pt-BR. As marcas do eixo saem da divisão do intervalo
// pelo Chart.js e chegam como 5.140000000000001 quando a faixa é estreita.
const formatarNumero = new Intl.NumberFormat('pt-BR', { maximumFractionDigits: 1 }).format;

/**
 * Faixa ideal desenhada como área preenchida entre o máximo e o mínimo:
 * o produtor vê de relance em que dias a linha saiu do verde.
 */
function datasetsDaFaixa(minimo, maximo) {
    if (minimo === null && maximo === null) {
        return [];
    }

    const linha = (valor, preencher) => ({
        data: null, // preenchido depois com o mesmo comprimento dos labels
        valorFixo: valor,
        borderColor: 'rgba(22, 163, 74, 0.45)',
        borderWidth: 1,
        borderDash: [5, 4],
        pointRadius: 0,
        fill: preencher,
        backgroundColor: 'rgba(22, 163, 74, 0.10)',
        tension: 0,
    });

    // Sem um dos lados a faixa vira apenas a linha limite existente.
    if (minimo === null) {
        return [linha(maximo, false)];
    }

    if (maximo === null) {
        return [linha(minimo, false)];
    }

    return [linha(maximo, '+1'), linha(minimo, false)];
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.sensor-report-chart').forEach((canvas) => {
        const labels = JSON.parse(canvas.dataset.labels || '[]').map((data) => {
            const [, mes, dia] = data.split('-');
            return dia + '/' + mes;
        });
        const valores = JSON.parse(canvas.dataset.valores || '[]');
        const unidade = canvas.dataset.unidade || '';
        const minimo = canvas.dataset.min ? Number(canvas.dataset.min) : null;
        const maximo = canvas.dataset.max ? Number(canvas.dataset.max) : null;

        const foraDaFaixa = (valor) =>
            (minimo !== null && valor < minimo) || (maximo !== null && valor > maximo);

        const faixa = datasetsDaFaixa(minimo, maximo).map((dataset) => ({
            ...dataset,
            data: labels.map(() => dataset.valorFixo),
        }));

        new Chart(canvas, {
            type: 'line',
            data: {
                labels,
                datasets: [
                    ...faixa,
                    {
                        label: 'Média do dia',
                        data: valores,
                        borderColor: DENTRO_DA_FAIXA,
                        backgroundColor: 'rgba(22, 163, 74, 0.08)',
                        borderWidth: 2.5,
                        fill: false,
                        tension: 0.4,
                        // Cada ponto vermelho é um dia em que a média saiu da faixa ideal.
                        pointBackgroundColor: valores.map((valor) =>
                            foraDaFaixa(valor) ? FORA_DA_FAIXA : DENTRO_DA_FAIXA
                        ),
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: valores.map((valor) => (foraDaFaixa(valor) ? 5 : 4)),
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        // Só a série de leituras interessa: as linhas da faixa são referência.
                        filter: (ctx) => ctx.datasetIndex === faixa.length,
                        callbacks: {
                            label: (ctx) =>
                                formatarNumero(ctx.parsed.y) + unidade +
                                (foraDaFaixa(ctx.parsed.y) ? ' · fora da faixa ideal' : ''),
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        grid: { color: 'rgba(148, 163, 184, 0.15)' },
                        ticks: { callback: (value) => formatarNumero(value) + unidade },
                    },
                    x: {
                        grid: { display: false },
                    },
                },
            },
        });
    });
});
