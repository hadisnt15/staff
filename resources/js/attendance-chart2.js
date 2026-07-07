window.chart2 = null;

function renderAttendanceChart2() {
    const wrapper = document.getElementById('attendanceChartWrapper2');
    const canvas = document.getElementById('attendanceChart2');

    if (!wrapper || !canvas) return;
    if (typeof Chart === 'undefined') return;

    const raw = wrapper.dataset.chart;
    if (!raw) return;

    let data;
    try {
        data = JSON.parse(raw);
    } catch (e) {
        console.log('JSON error', e);
        return;
    }

    const total = data.presence + data.leave;
    const tepatWaktu = Math.max(data.presence - data.late, 0);

    const tepatWaktuPct = total ? (tepatWaktu / total) * 100 : 0;
    const telatPct = total ? (data.late / total) * 100 : 0;
    const tidakHadirPct = total ? (data.leave / total) * 100 : 0;

    if (window.chart2) {
        window.chart2.destroy();
        window.chart2 = null;
    }

    window.chart2 = new Chart(canvas, {
        type: 'bar',
        data: {
            labels: [''],
            datasets: [
                {
                    label: 'Tepat Waktu',
                    data: [tepatWaktuPct],
                    backgroundColor: '#10b981',
                    barThickness: 40
                },
                {
                    label: 'Telat',
                    data: [telatPct],
                    backgroundColor: '#f59e0b',
                    barThickness: 40
                },
                {
                    label: 'Tidak Hadir',
                    data: [tidakHadirPct],
                    backgroundColor: '#ef4444',
                    barThickness: 40
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,

            categoryPercentage: 0.4,
            barPercentage: 0.5,

            interaction: {
                mode: 'index',
                intersect: true
            },

            scales: {
                x: {
                    stacked: true,
                    grid: { display: false }
                },
                y: {
                    stacked: true,
                    max: 100,
                    ticks: {
                        callback: v => v + '%'
                    }
                }
            },

            plugins: {
                legend: { display: false },
                tooltip: {
                    enabled: true,
                    mode: 'nearest',
                    intersect: true,

                    callbacks: {
                        title: () => '',

                        label: function(context) {
                            const wrapper = document.getElementById('attendanceChartWrapper2');
                            const raw = JSON.parse(wrapper.dataset.chart);

                            const mapping = [
                                Math.max(raw.presence - raw.late, 0),
                                raw.late,
                                raw.leave
                            ];

                            const total = raw.presence + raw.leave;

                            const value = mapping[context.datasetIndex] ?? 0;
                            const percent = total ? (value / total) * 100 : 0;

                            return `${context.dataset.label}: ${value} hari (${percent.toFixed(1)}%)`;
                        }
                    }
                },

            },

            // 🔥 klik = munculin tooltip semua stack
            onClick: (evt, elements, chart) => {
                const points = chart.getElementsAtEventForMode(
                    evt,
                    'nearest',
                    { intersect: true },
                    true
                );

                if (!points.length) return;

                const { datasetIndex, index } = points[0];

                const active = [{
                    datasetIndex,
                    index
                }];

                chart.setActiveElements(active);

                chart.tooltip.setActiveElements(active, {
                    x: evt.x,
                    y: evt.y
                });

                chart.update();
            },

        }
    });
}

document.addEventListener('livewire:navigated', () => {
    renderAttendanceChart2();
});

document.addEventListener('livewire:init', () => {
    Livewire.on('refresh-chart', ({ chartData }) => {
        const wrapper = document.getElementById('attendanceChartWrapper2');
        wrapper.dataset.chart = JSON.stringify(chartData);
        renderAttendanceChart2();
    });
});