import { Chart, DoughnutController, BarController, ArcElement, BarElement, CategoryScale, LinearScale, Tooltip, Legend } from 'chart.js';

// Register only the pieces we use, so the bundle stays small.
Chart.register(DoughnutController, BarController, ArcElement, BarElement, CategoryScale, LinearScale, Tooltip, Legend);

// The server (DashboardController) puts the numbers in a <script type="application/json"> tag.
const data = JSON.parse(document.getElementById('dashboard-data').textContent);

new Chart(document.getElementById('status-chart'), {
    type: 'doughnut',
    data: {
        labels: data.status.labels,
        datasets: [{ data: data.status.values, backgroundColor: data.status.colors, borderWidth: 0 }],
    },
    options: {
        cutout: '62%',
        plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } } },
    },
});

new Chart(document.getElementById('weekly-chart'), {
    type: 'bar',
    data: {
        labels: data.weekly.labels,
        datasets: [{ label: 'Applications', data: data.weekly.values, backgroundColor: '#6366f1', borderRadius: 4 }],
    },
    options: {
        plugins: { legend: { display: false } },
        scales: {
            // Whole numbers only: you can't send half an application.
            y: { beginAtZero: true, ticks: { precision: 0 } },
            x: { grid: { display: false } },
        },
    },
});
