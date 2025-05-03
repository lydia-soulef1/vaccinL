<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
@extends('layouts.main')

@section('title', 'Liste des enfants')

@section('content')
<div class="dashboard-container">

    <div class="sidebar">
        <div class="logo text-center mb-4">
            <img src="{{ asset('images/logo-white.jpg') }}" alt="VacciBaby" class="img-fluid">
        </div>

        <!-- Search form -->
        <div class="search-box mb-4 position-relative">
            <input type="text" id="searchChild" class="form-control" placeholder="Rechercher un enfant" onkeyup="searchChild()">
            <i class="fas fa-search search-icon"></i>
        </div>


        <ul class="nav-menu list-unstyled">
            <!-- Dashboard link -->
            <li class="nav-item">
                <a href="#tableu" class="nav-link active">
                    <i class="fas fa-home"></i> Tableau de bord
                </a>
            </li>

            <!-- Calendar link -->
            <li class="nav-item">
                <a href="#" class="nav-link" data-toggle="modal" data-target="#calendarModal">
                    <i class="fas fa-calendar-alt"></i> Calendrier
                </a>
            </li>

            <!-- Vaccination Progress link -->
            <li class="nav-item">
                <a href="#" class="nav-link" data-toggle="modal" data-target="#vaccinationProgressModal">
                    <i class="fas fa-syringe"></i> Progression Vaccinale
                </a>
            </li>

            <!-- Notifications link -->
            <li class="nav-item">
                <a href="#" class="nav-link" data-toggle="modal" data-target="#notificationsModal">
                    <i class="fas fa-bell"></i> Notifications
                </a>
            </li>

            <!-- Logout link -->
            <li class="nav-item">
                <form action="{{ route('logout') }}" method="POST" class="nav-link">
                    @csrf
                    <button type="submit"><i class="fas fa-sign-out-alt"></i> Déconnexion</button>
                </form>
            </li>
        </ul>
    </div>


    <div class="container mt-4">

        <div class="header" id="tableu">
            <h1>Tableau de Bord</h1>
            <div class="user-info">
                <div class="user-avatar">
                    {{ strtoupper(substr($children[0]->pediatrician_name, 0, 1)) }}
                </div>
                <div>
                    <div>
                        Dr. {{ $children[0]->pediatrician->full_name ?? $children[0]->pediatrician_name ?? 'Pédiatre non défini' }}
                    </div>



                    <small>{{ $children[0]->pediatrician->email ?? 'Non disponible' }}</small>
                </div>

            </div>
        </div>
        <h1 class="mb-4">Enfants suivis</h1>

        <div id="children-list">
            @foreach ($children as $child)
            <div class="card child-card" style="position: relative;" data-name="{{ strtolower($child['name']) }}">
                <div class="child-header">
                    <div class="child-avatar">
                        {{ strtoupper(substr($child['name'], 0, 1)) }}
                    </div>
                    <div>
                        <h3 style="margin: 0;">{{ $child['name'] }}</h3>
                        <small>{{ $child->age }} an(s) - Né(e) le {{ \Carbon\Carbon::parse($child['dob'])->format('d/m/Y') }}</small>
                    </div>
                </div>

                <!-- Pending Icon on Top Right -->
                <div class="pending-icon" style="position: absolute; top: 10px; right: 10px; font-size: 20px;">
                    <i class="fas fa-clock" style="color: #ffc107;"></i> <!-- Clock Icon -->
                </div>

                <div style="margin-top: 1.5rem;">
                    <!-- Modal Trigger Button -->
                    <a href="#" class="btn btn-primary" style="display: block; text-align: center;" data-toggle="modal" data-target="#calendarModal-{{ $child['id'] }}">
                        <i class="fas fa-info-circle"></i> Details
                    </a>
                </div>
            </div>
            @endforeach
        </div>



        <!-- Modal pour ajouter un rendez-vous -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2 class="card-title">
                    <i class="fas fa-child"></i> Les enfants traités
                </h2>
            </div>

            @if (!empty($children))
            <div class="children-grid">
                @foreach ($children as $child)
                @php
                $progress = $child['vaccines_total'] > 0 ? round(($child['vaccines_done'] / $child['vaccines_total']) * 100) : 0;
                @endphp
                <div class="card child-card">
                    <div class="child-header">
                        <div class="child-avatar">
                            {{ strtoupper(substr($child['name'], 0, 1)) }}
                        </div>
                        <div>
                            <h3 style="margin: 0;">{{ $child['name'] }}</h3>
                            <small>{{ $child->age }} an(s) - Né(e) le {{ \Carbon\Carbon::parse($child['dob'])->format('d/m/Y') }}</small>
                        </div>
                        <i class="fas fa-check-circle" style="color: green; position: absolute; top: 10px; right: 10px; font-size: 1.5rem;"></i>
                    </div>

                    <div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.9rem; margin-bottom: 0.5rem;">
                            <span>Progression vaccinale :</span>
                            <span>{{ $child['vaccines_done'] }}/{{ $child['vaccines_total'] }}</span>
                        </div>
                        <div class="progress-container">
                            <div class="progress-bar" style="width: {{ $progress }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Child Specific Modal -->
                <div class="modal fade" id="calendarModal-{{ $child['id'] }}" tabindex="-1" role="dialog" aria-labelledby="calendarModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="calendarModalLabel">Calendrier de Vaccination de {{ $child['name'] }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p><strong>Nom :</strong> {{ $child['name'] }}</p>
                                <p><strong>Âge :</strong> {{ $child->age }} an(s)</p>
                                <p><strong>Date de naissance :</strong> {{ \Carbon\Carbon::parse($child['dob'])->format('d/m/Y') }}</p>
                                <p><strong>Nom du parent :</strong> {{ $parent->name }}</p>
                                @if($lastVaccination)
                                <p><strong>Date de rendez-vous :</strong> {{ \Carbon\Carbon::parse($lastVaccination->next_rendez_vous)->format('d/m/Y') }}</p>
                                @else
                                <p>Aucun rendez-vous disponible</p>
                                @endif

                                @if($child->next_vaccine_name)
                                <p><strong>Prochain vaccin :</strong> {{ $child->next_vaccine_name }}</p>
                                @else
                                <p class="text-success">Tous les vaccins sont complétés 🎉</p>
                                @endif

                                <div class="mt-3">
                                    <button class="btn btn-secondary" data-toggle="modal" data-target="#progressModal-{{ $child['id'] }}">Voir le progrès</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Progress Modal (This can be reused for all children) -->
                <div class="modal fade" id="progressModal-{{ $child->id }}" tabindex="-1" role="dialog" aria-labelledby="progressModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="progressModalLabel">Progrès Vaccinaux de {{ $child->name }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <h6>Sélectionnez les vaccins réalisés :</h6>
                                <form action="{{ route('vaccines.update') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="child_id" value="{{ $child['id'] }}">

                                    @php
                                    $doneVaccines = \App\Models\Vaccination::where('child_id', $child['id'])
                                    ->orderByDesc('date_administered')
                                    ->get()
                                    ->keyBy('vaccine_id');

                                    $lastVaccination = $doneVaccines->sortByDesc('date_administered')->first();
                                    @endphp

                                    @foreach($vaccines as $vaccine)
                                    @php
                                    $vaccination = $doneVaccines->get($vaccine->id);
                                    $isDone = !is_null($vaccination);
                                    @endphp
                                    <div>
                                        <input type="checkbox" name="vaccine_id[]" value="{{ $vaccine->id }}"
                                            {{ $isDone ? 'checked disabled' : '' }}>
                                        {{ $vaccine->name }} - {{ $vaccine->recommended_age }}

                                        @if($isDone)
                                        <span style="color: green; font-size: 0.9em;">
                                            ({{ \Carbon\Carbon::parse($vaccination->date_administered)->format('d/m/Y') }})
                                        </span>

                                        @if($lastVaccination && $lastVaccination->vaccine_id == $vaccine->id && $lastVaccination->next_rendez_vous)
                                        <span style="color: red; font-size: 0.9em;">
                                            – {{ \Carbon\Carbon::parse($lastVaccination->next_rendez_vous)->format('d/m/Y') }}
                                        </span>
                                        @endif
                                        @endif
                                    </div>
                                    @endforeach

                                    <div style="margin-top: 1rem; text-align: right;">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-check-circle"></i> Valider
                                        </button>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>

                @endforeach
            </div>

            @else
            <div style="text-align: center; padding: 2rem;">
                <img src="{{ asset('assets/img/empty-child.png') }}" alt="Aucun enfant" style="width: 150px; opacity: 0.6; margin-bottom: 1rem;">
                <h3 style="color: #666; margin-bottom: 0.5rem;">Vous n'avez pas encore ajouté d'enfant</h3>
            </div>
            @endif
        </div>

    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const childCards = document.querySelectorAll('.child-card');
        childCards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = `all 0.3s ease ${index * 0.1}s`;
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100);
        });

        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('error')) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.innerHTML = `
                <i class="fas fa-exclamation-circle"></i>
                ${decodeURIComponent(urlParams.get('error'))}
            `;
            document.querySelector('.main-content').prepend(errorDiv);

            setTimeout(() => {
                errorDiv.style.transition = 'opacity 1s';
                errorDiv.style.opacity = '0';
                setTimeout(() => errorDiv.remove(), 1000);
            }, 5000);
        }
    });
</script>
<script>
    function openModal(childId, childName) {
        document.getElementById('child_id').value = childId;
    }
</script>

<script>
    function searchChild() {
        var input, filter, children, cards, name, i;
        input = document.getElementById("searchChild");
        filter = input.value.toLowerCase();
        children = document.getElementById("children-list");
        cards = children.getElementsByClassName("child-card");

        for (i = 0; i < cards.length; i++) {
            name = cards[i].getAttribute("data-name");
            if (name.indexOf(filter) > -1) {
                cards[i].style.display = ""; // Show the card
            } else {
                cards[i].style.display = "none"; // Hide the card
            }
        }
    }
</script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

@endsection