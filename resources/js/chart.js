import {
    Chart,
    BarController,
    DoughnutController,
    BarElement,
    ArcElement,
    CategoryScale,
    LinearScale,
    Tooltip,
    Legend
} from 'chart.js';

// Mendaftarkan hanya komponen yang digunakan untuk efisiensi ukuran bundel (tree shaking)
Chart.register(
    BarController,
    DoughnutController,
    BarElement,
    ArcElement,
    CategoryScale,
    LinearScale,
    Tooltip,
    Legend
);

// Ekspos ke window agar bisa diakses oleh skrip di dalam tag <script> pada blade template
window.Chart = Chart;
