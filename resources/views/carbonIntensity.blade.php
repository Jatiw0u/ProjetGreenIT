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
    <!-- Contenu du modal -->
</div>
@endsection
