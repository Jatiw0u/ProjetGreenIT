@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h2 class="text-center">Tableau de bord</h2>
            <div class="card">
                <div class="card-header text-center">Visualiser la demande énergétique</div>
                <div class="card-body">
                    <form class="row g-3">
                        <div class="col-md-4">
                            <label for="location" class="form-label">Lieu</label>
                            <select id="location" class="form-select">
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}">{{ $location->NameLocation }}, {{ $location->Country }}</option>
                                    @endforeach
                                </select>
                        </div>
                        <div class="col-md-4">
                            <label for="frequency" class="form-label">Fréquences</label>
                            <select id="frequency" class="form-select">
                                <option selected>toutes les heures</option>
                                <!-- Ajouter d'autres options ici -->
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="displayPeriod" class="form-label">Période d'affichage</label>
                            <select id="displayPeriod" class="form-select">
                                <option selected>7j</option>
                                <!-- Ajouter d'autres options ici -->
                            </select>
                        </div>
                    </form>
                    <div class="mt-4">
                        <h5 class="text-center">Exemple de graphique</h5>
                        <canvas id="exampleChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
