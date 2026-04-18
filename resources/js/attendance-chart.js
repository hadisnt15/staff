let chart;

const centerTextPlugin = {
    id: 'centerText',
    beforeDraw(chartInstance) {
        const { width, height, ctx } = chartInstance;
        ctx.save();

        const data = window.chartData || {
            presence: 0,
            late: 0,
            leave: 0
        };

        const hadir = data.presence;

        const centerX = width / 2;
        const centerY = height / 2;

        // TEXT ATAS
        ctx.font = '600 12px sans-serif';
        ctx.fillStyle = '#6b7280';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText('Total Hadir', centerX, centerY - 20);

        // ANGKA (lebih besar & center)
        ctx.font = 'bold 12px sans-serif';
        ctx.fillStyle = '#3b82f6';
        ctx.fillText(hadir + ' Hari', centerX, centerY - 8);

        ctx.restore();
    }
};


function renderAttendanceChart() {
    const canvas = document.getElementById('attendanceChart');

    if (!canvas) return;

    if (typeof Chart === 'undefined') {
        console.log('Chart.js belum siap');
        return;
    }

    if (!window.chartData) {
        console.log('Data chart kosong');
        return;
    }

    const data = window.chartData;
    const tepatWaktu = Math.max(data.presence - data.late, 0);

    if (chart) chart.destroy();

    chart = new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels: ['Tepat Waktu', 'Telat', 'Tidak Hadir'],
            datasets: [{
                data: [
                    tepatWaktu,
                    data.late,
                    data.leave
                ],
                backgroundColor: [
                    '#10b981',
                    '#f59e0b',
                    '#ef4444'
                ],
                borderWidth: 0
            }]
        },
        options: {
            cutout: '65%',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                }
            },
        },
        plugins: [centerTextPlugin] // 🔥 plugin center text
    });

    console.log('🔥 chart render sukses');
}

function initAttendanceChart() {
    setTimeout(renderAttendanceChart, 150);
}

// Livewire + normal load
document.addEventListener('DOMContentLoaded', initAttendanceChart);
document.addEventListener('livewire:navigated', initAttendanceChart);
