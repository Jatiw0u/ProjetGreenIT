import '../sass/app.scss';
import { Modal } from 'bootstrap';
import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

let carbonChart = null;
let energyChart = null;

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded and parsed');

    // Obtenir le contexte des éléments canvas pour les graphiques
    const carbonCtx = document.getElementById('carbonIntensityChart')?.getContext('2d');
    const energyCtx = document.getElementById('energyDemandChart')?.getContext('2d');

    console.log('carbonCtx:', carbonCtx);
    console.log('energyCtx:', energyCtx);

    // Fonction pour mettre à jour le graphique de l'intensité carbone
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
                console.error('Failed to fetch carbone intensities data');
                return;
            }
            const data = await response.json();
            console.log('Carbone intensities data:', data);
            const labels = data.carbone_intensities.map(intensity => intensity.DateTimeIntensity);
            const values = data.carbone_intensities.map(intensity => intensity.value);
    
            // Détruire l'ancien graphique s'il existe
            if (carbonChart) {
                carbonChart.destroy();
            }
    
            // Créer un nouveau graphique
            carbonChart = new Chart(carbonCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Intensité carbone (gCO2)',
                        data: values,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'gCO2'
                            }
                        }
                    }
                }
            });
    
            // Récupération des paramètres de seuil pour le lieu sélectionné
            const settingData = await getSettingByLocation(locationId);
            if (!settingData) {
                return;
            }
    
            // Calcul du seuil
            const threshold = settingData[0].Number_Setting;
            const exceedance = values.find(value => value > threshold);
            const exceedanceIndex = values.findIndex(value => value > threshold);
            const locationName = document.querySelector(`#location option[value="${locationId}"]`).textContent;

            console.log(threshold)
    
            // Afficher une alerte si le seuil est dépassé
            if (exceedance) {
                const dateAlert = labels[exceedanceIndex];

                document.getElementById('alertLocation').textContent = `Lieu: ${locationName}`;
                document.getElementById('alertValue').textContent = exceedance;
                document.getElementById('thresholdValue').textContent = threshold;
                document.getElementById('alertTime').textContent = dateAlert;
                document.getElementById('customAlert').style.display = 'block';
            }
    
        } catch (error) {
            console.error('Error fetching data:', error);
        }
    };

    // Fonction pour mettre à jour le graphique de la demande énergétique
    const updateEnergyDemandChart = async () => {
        if (!energyCtx) {
            console.warn('energyCtx is not defined');
            return;
        }
        const locationId = document.getElementById('location').value;
        console.log('Fetching energy demands for location:', locationId);
        try {
            const response = await fetch(`/ProjetGreenIT/public/api/electrical-demands/${locationId}`);
            if (!response.ok) {
                console.error('Failed to fetch data');
                return;
            }
            const data = await response.json();
            console.log('Energy demands data:', data);
            const labels = data.electrical_demands.map(demand => demand.DateTimeDemand);
            const datasets = [
                {
                    label: 'Nucléaire (MW)',
                    data: data.electrical_demands.map(demand => demand.ValueNuclear),
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)'
                },
                {
                    label: 'Géothermie (MW)',
                    data: data.electrical_demands.map(demand => demand.ValueGeothermal),
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)'
                },
                {
                    label: 'Biomasse (MW)',
                    data: data.electrical_demands.map(demand => demand.ValueBiomass),
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)'
                },
                {
                    label: 'Charbon (MW)',
                    data: data.electrical_demands.map(demand => demand.ValueCoal),
                    borderColor: 'rgba(153, 102, 255, 1)',
                    backgroundColor: 'rgba(153, 102, 255, 0.2)'
                },
                {
                    label: 'Éolien (MW)',
                    data: data.electrical_demands.map(demand => demand.ValueWind),
                    borderColor: 'rgba(255, 159, 64, 1)',
                    backgroundColor: 'rgba(255, 159, 64, 0.2)'
                },
                {
                    label: 'Solaire (MW)',
                    data: data.electrical_demands.map(demand => demand.ValueSolar),
                    borderColor: 'rgba(255, 206, 86, 1)',
                    backgroundColor: 'rgba(255, 206, 86, 0.2)'
                },
                {
                    label: 'Hydro (MW)',
                    data: data.electrical_demands.map(demand => demand.ValueHydro),
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)'
                },
                {
                    label: 'Gaz (MW)',
                    data: data.electrical_demands.map(demand => demand.ValueGas),
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)'
                },
                {
                    label: 'Pétrole (MW)',
                    data: data.electrical_demands.map(demand => demand.ValueOil),
                    borderColor: 'rgba(153, 102, 255, 1)',
                    backgroundColor: 'rgba(153, 102, 255, 0.2)'
                }
            ];

            // Détruire l'ancien graphique s'il existe
            if (energyChart) {
                energyChart.destroy();
            }

            // Créer un nouveau graphique
            energyChart = new Chart(energyCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: datasets
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'MW'
                            }
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error fetching energy demands:', error);
        }
    };

    // Fonction pour obtenir les paramètres de seuil pour un lieu donné
    const getSettingByLocation = async (locationId) => {
        try {
            const response = await fetch(`/ProjetGreenIT/public/api/parametres/${locationId}`);
            if (!response.ok) {
                console.warn('No settings found for this location.');
                return null;
            }
            return await response.json();
        } catch (error) {
            console.error('Error fetching setting:', error);
            return null;
        }
    };

    // Ajouter des écouteurs d'événements pour la sélection de la localisation
    const locationElement = document.getElementById('location');
    if (locationElement) {
        locationElement.addEventListener('change', () => {
            console.log('Location changed:', locationElement.value);
            updateCarbonIntensityChart();
            updateEnergyDemandChart();
        });

        // Appel initial pour afficher les données au chargement de la page
        updateCarbonIntensityChart();
        updateEnergyDemandChart();
    } else {
        console.warn('locationElement is not defined');
    }

    // Gestionnaire d'événement pour ouvrir la modale de paramétrage des alertes
    const openModalBtn = document.getElementById('openModalBtn');
    const alertModal = new Modal(document.getElementById("alertModal"));

    if (openModalBtn) {
        openModalBtn.addEventListener('click', function () {
            alertModal.show();
        });
    }

    // Gestionnaire pour fermer l'alerte personnalisée
    const customAlert = document.getElementById('customAlert');
    const closeAlertBtn = document.getElementById('closeAlertBtn');

    if (customAlert && closeAlertBtn) {
        closeAlertBtn.addEventListener('click', () => {
            customAlert.style.display = 'none';
        });
    } else {
        console.warn('customAlert or closeAlertBtn is not defined');
    }

    // Ajouter le gestionnaire de soumission du formulaire de paramétrage des alertes
    const alertForm = document.getElementById('alertForm');
    const saveAlertSettingsBtn = document.getElementById('saveAlertSettingsBtn');

    if (alertForm && saveAlertSettingsBtn) {
        saveAlertSettingsBtn.addEventListener('click', async (event) => {
            event.preventDefault();

            const locationAlert = document.getElementById('locationAlert').value;
            const carbonThreshold = document.getElementById('carbonThreshold').value;

            try {
                const response = await fetch(`/ProjetGreenIT/public/api/parametres/${carbonThreshold}/${locationAlert}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    const result = await response.json();
                    console.log('Settings updated successfully:', result);
                    // Optionally close the modal here
                    alertModal.hide();
                } else {
                    console.error('Failed to update settings:', response.statusText);
                }
            } catch (error) {
                console.error('Error updating settings:', error);
            }
        });
    }

});
