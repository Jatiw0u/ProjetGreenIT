@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h2 class="text-center">Tableau de bord</h2>
            <div class="card">
                <div class="card-header text-center">Visualiser l'intensité carbone</div>
                <div class="card-body">
                    <form class="row g-3">
                        <!-- Vos champs de formulaire -->
                    </form>
                    <div class="mt-4">
                        <h5 class="text-center">Exemple de graphique</h5>
                        <canvas id="exampleChart"></canvas>
                    </div>
                    <div class="text-center mt-4">
                        <button type="button" class="btn btn-secondary" id="openModalBtn">Paramétrage des alertes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="alertModal" tabindex="-1" aria-labelledby="alertModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="alertModalLabel">Paramétrage des alertes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="alertForm">
                    <div class="mb-3">
                        <label for="carbonThreshold" class="form-label">Seuil d'intensité carbone</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="carbonThreshold" name="carbonThreshold" min="0" step="0.01" required>
                            <span class="input-group-text">gCO2</span>
                        </div>
                    </div>
                    <!-- Ajouter d'autres champs de paramétrage ici si nécessaire -->
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="submit" form="alertForm" class="btn btn-primary">Enregistrer</button>
            </div>
        </div>
    </div>
</div>

<!-- Code de l'alerte -->
<div id="customAlert" class="custom-alert" style="display: none;">
    <h4 class="alert-title">Dépassement seuil carbone !</h4>
    <p class="alert-info">Avec la valeur <span id="alertValue"></span> / Avec le seuil de dépassement à <span id="thresholdValue"></span> / A <span id="alertTime"></span></p>
    <button id="closeAlertBtn" class="close-alert-btn">Fermer</button>
</div>
@endsection
