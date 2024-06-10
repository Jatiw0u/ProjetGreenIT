import '../sass/app.scss';
import { Modal } from 'bootstrap';
import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded and parsed');

    const carbonCtx = document.getElementById('carbonIntensityChart')?.getContext('2d');
    const energyCtx = document.getElementById('energyDemandChart')?.getContext('2d');

    console.log('carbonCtx:', carbonCtx);
    console.log('energyCtx:', energyCtx);

    const updateCarbonIntensityChart = async () => {
        if (!carbonCtx) {
            console.warn('carbonCtx is not defined');
            return;
        }
        const locationId = document.getElementById('location').value;
        console.log('Fetching carbone intensities for location:', locationId);
        try {
            const response = await fetch(`/ProjetGreenIT/public/api/carbone-intensities/${locationId}`);
            if (!response.ok) {
                console.error('Failed to fetch data');
                return;
            }
            const data = await response.json();
            console.log('Carbone intensities data:', data);
            const labels = data.carbone_intensities.map(intensity => intensity.DateTimeIntensity);
            const values = data.carbone_intensities.map(intensity => intensity.value);

            new Chart(carbonCtx, {
                type: 'bar',
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
        } catch (error) {
            console.error('Error fetching carbone intensities:', error);
        }
    };

    const updateEnergyDemandChart = async () => {
        if (!energyCtx) {
            console.warn('energyCtx is not defined');
            return;
        }
        const locationId = document.getElementById('location').value;
        console.log('Fetching energy demands for location:', locationId);
        try {
            const response = await fetch(`/ProjetGreenIT/public/api/electrical-demands/121297`);
            if (!response.ok) {
                console.error('Failed to fetch data');
                return;
            }
            const data = await response.json();
            console.log('Energy demands data:', data);
            const labels = data.electrical_demands.map(demand => demand.DateTimeDemand);
            const datasets = [
                {
                    label: 'Nucléaire',
                    data: data.electrical_demands.map(demand => demand.ValueNuclear),
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)'
                },
                {
                    label: 'Géothermie',
                    data: data.electrical_demands.map(demand => demand.ValueGeothermal),
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)'
                },
                {
                    label: 'Biomasse',
                    data: data.electrical_demands.map(demand => demand.ValueBiomass),
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)'
                },
                {
                    label: 'Charbon',
                    data: data.electrical_demands.map(demand => demand.ValueCoal),
                    borderColor: 'rgba(153, 102, 255, 1)',
                    backgroundColor: 'rgba(153, 102, 255, 0.2)'
                },
                {
                    label: 'Éolien',
                    data: data.electrical_demands.map(demand => demand.ValueWind),
                    borderColor: 'rgba(255, 159, 64, 1)',
                    backgroundColor: 'rgba(255, 159, 64, 0.2)'
                },
                {
                    label: 'Solaire',
                    data: data.electrical_demands.map(demand => demand.ValueSolar),
                    borderColor: 'rgba(255, 206, 86, 1)',
                    backgroundColor: 'rgba(255, 206, 86, 0.2)'
                },
                {
                    label: 'Hydro',
                    data: data.electrical_demands.map(demand => demand.ValueHydro),
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)'
                },
                {
                    label: 'Gaz',
                    data: data.electrical_demands.map(demand => demand.ValueGas),
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)'
                },
                {
                    label: 'Pétrole',
                    data: data.electrical_demands.map(demand => demand.ValueOil),
                    borderColor: 'rgba(153, 102, 255, 1)',
                    backgroundColor: 'rgba(153, 102, 255, 0.2)'
                }
            ];

            new Chart(energyCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: datasets
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error fetching energy demands:', error);
        }
    };

    const locationElement = document.getElementById('location');
    if (locationElement) {
        locationElement.addEventListener('change', () => {
            console.log('Location changed:', locationElement.value);
            updateCarbonIntensityChart();
            updateEnergyDemandChart();
        });

        // Initial call to display data on load
        updateCarbonIntensityChart();
        updateEnergyDemandChart();
    } else {
        console.warn('locationElement is not defined');
    }

    const openModalBtn = document.getElementById('openModalBtn');
    const alertModal = new Modal(document.getElementById("alertModal"));

    if (openModalBtn) {
        openModalBtn.addEventListener('click', function () {
            alertModal.show();
        });
    }

    const customAlert = document.getElementById('customAlert');
    const closeAlertBtn = document.getElementById('closeAlertBtn');

    if (customAlert && closeAlertBtn) {
        customAlert.style.display = 'block';
        closeAlertBtn.addEventListener('click', () => {
            customAlert.style.display = 'none';
        });
    } else {
        console.warn('customAlert or closeAlertBtn is not defined');
    }
});
