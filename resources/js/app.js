import '../sass/app.scss';
import { Modal } from 'bootstrap'

import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);



document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('exampleChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Pommes', 'Oranges', 'Bananes'],
            datasets: [
                {
                    label: '2013',
                    data: [700, 500, 400],
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                },
                {
                    label: '2014',
                    data: [400, 800, 300],
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                },
                {
                    label: '2015',
                    data: [300, 400, 700],
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const openModalBtn = document.getElementById('openModalBtn');
    //const alertModal = new Bootstrap.Modal(document.getElementById('alertModal'));
    const alertModal = new Modal(document.getElementById("alertModal"));

    if (openModalBtn) {
        openModalBtn.addEventListener('click', function () {
            alertModal.show();
        });
    }
});



