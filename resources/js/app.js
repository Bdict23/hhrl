import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
import Chart from 'chart.js/auto';
import 'chartjs-adapter-date-fns';
// Expose Chart globally so it can be accessed in item-cost.blade.php
window.Chart = Chart;

// Placeholder for other app logic
document.addEventListener('DOMContentLoaded', () => {
    console.log('Chart.js loaded and ready');
});