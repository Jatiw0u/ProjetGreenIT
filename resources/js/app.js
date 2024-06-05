import '../sass/app.scss';
import { Modal } from 'bootstrap';
import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('exampleChart').getContext('2d');

    const updateChart = async () => {
        const locationId = document.getElementById('location').value;
        const response = await fetch(`/ProjetGreenIT/public/api/carbone-intensities/${locationId}`);
        if (!response.ok) {
            console.error('Failed to fetch data');
            return;
        }
        const data = await response.json();
        const labels = data.carbone_intensities.map(intensity => intensity.DateTimeIntensity);
        const values = data.carbone_intensities.map(intensity => intensity.value);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Intensité carbone',
                    data: values,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    };

    document.getElementById('location').addEventListener('change', updateChart);

    updateChart();  // Initial call to display data on load

    const openModalBtn = document.getElementById('openModalBtn');
    const alertModal = new bootstrap.Modal(document.getElementById("alertModal"));

    if (openModalBtn) {
        openModalBtn.addEventListener('click', function () {
            alertModal.show();
        });
    }

    const customAlert = document.getElementById('customAlert');
    const closeAlertBtn = document.getElementById('closeAlertBtn');

    customAlert.style.display = 'block';

    function hideAlert() {
        customAlert.style.display = 'none';
    }

    closeAlertBtn.addEventListener('click', hideAlert);
});
