<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
@extends('layouts.main')

@section('content')

<div class="dashboard-container">
    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <div class="logo">
            <img src="{{ asset('images/logo-white.jpg') }}" alt="VacciBaby">
        </div>

        <ul class="nav-menu">
            <li class="nav-item">
                <a href="#tableu" class="nav-link active">
                    <i class="fas fa-home"></i> Tableau de bord
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link" data-toggle="modal" data-target="#addChildModal">
                    <i class="fas fa-baby"></i> Ajouter un enfant
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link" data-toggle="modal" data-target="#calendarModal">
                    <i class="fas fa-calendar-alt"></i> Calendrier
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link" data-toggle="modal" data-target="#messagesModal">
                    <i class="fas fa-envelope"></i> Messages
                </a>
            </li>

            <li class="nav-item">
                <form action="{{ route('logout') }}" method="POST" class="nav-link">
                    @csrf
                    <button type="submit"><i class="fas fa-sign-out-alt"></i> Déconnexion</button>
                </form>
            </li>
        </ul>
    </div>

    <!-- Messages Modal -->
    <div class="modal fade" id="messagesModal" tabindex="-1" role="dialog" aria-labelledby="messagesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="messagesModalLabel">📬 Messages du système</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-light">
                    <!-- Message de bienvenue -->
                    <div class="alert alert-info d-flex align-items-center">
                        <i class="fas fa-user-md mr-2"></i>
                        <div>
                            👋 Bonjour Monsieur {{ $parent->prenom ? $parent->prenom . ' ' . $parent->name : $parent->name }},
                            ,
                            Bienvenue sur votre tableau de bord. Nous vous remercions pour votre confiance et votre engagement envers la santé de votre enfant.
                            👶 Grâce à notre plateforme, soyez assuré que votre enfant est entre de bonnes mains
                        </div>
                    </div>

                    <!-- Messages des enfants -->
                    @forelse ($children as $child)
                    <div class="media mb-4 p-3 rounded shadow-sm bg-white border-left-success">
                        <img src="https://cdn-icons-png.flaticon.com/512/4086/4086679.png" width="40" class="mr-3" alt="icon">
                        <div class="media-body">
                            <h6 class="mt-0 text-success">
                                👶 L’enfant <strong>{{ $child->name }}</strong> a été traité avec succès.
                            </h6>
                            <p class="mb-1">
                                💉 Dernier vaccin administré : <strong>{{ $child->last_vaccine_name ?? 'Non disponible' }}</strong><br>
                                👨‍⚕️ Pédiatre responsable : <strong>Dr. {{ $child->pediatrician_name }}</strong><br>
                                📅 Prochain rendez-vous :
                                <strong>
                                    {{ $child->next_rendez_vous ? \Carbon\Carbon::parse($child->next_rendez_vous)->format('d/m/Y') : 'Non défini' }}
                                </strong>
                            </p>
                            <a href="#progress" class="btn btn-outline-success btn-sm mt-2 view-progress-btn">
                                📊 Voir la progression
                            </a>

                            <br>
                            <small class="text-muted">⏱️ Mise à jour récente</small>
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-muted">Aucun message pour le moment.</p>
                    @endforelse

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>




    <!-- Modal -->
    <div class="modal fade" id="addChildModal" tabindex="-1" aria-labelledby="addChildModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addChildModalLabel">Ajouter un enfant</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form for adding a child -->
                    <form action="{{ route('child.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="child_name" class="form-label">Nom de l'enfant</label>
                            <input type="text" class="form-control" id="child_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="dob" class="form-label">Date de naissance</label>
                            <input type="date" class="form-control" id="dob" name="dob" required>
                        </div>
                        <div class="mb-3">
                            <label for="gender" class="form-label">Sexe</label>
                            <select class="form-control" id="gender" name="gender" required>
                                <option value="M">Masculin</option>
                                <option value="F">Féminin</option>
                            </select>
                        </div>
                        <!-- Hidden field to pass parent_id -->
                        <input type="hidden" name="parent_id" value="{{ Auth::user()->id }}">
                        <button type="submit" class="btn btn-primary">Ajouter l'enfant</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
    <h1 class="text-center fw-bold display-4 my-2">Tableau de bord parent</h1>
        <div class="header" id="tableu">
            <h1>Tableau de Bord</h1>
            <div class="user-info">
                <div class="user-avatar">
                    {{ strtoupper(substr($parent->name, 0, 1)) }}
                </div>
                <div>
                    <div>Mr. {{ $parent->prenom ? $parent->prenom . ' ' . $parent->name : $parent->name }}</div>
                    <small>{{ $parent->email }}</small>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="card stat-card">
                <div>Enfants enregistrés</div>
                <div class="stat-number">{{ count($children) }}</div>
            </div>
            <div class="card stat-card">
                <div>Vaccins complétés</div>
                <div class="stat-number" style="color: var(--success);">
                    {{ collect($children)->sum('vaccines_done') }}
                </div>
            </div>
            <div class="card stat-card">
                <div>Vaccins à faire</div>
                <div class="stat-number" style="color: var(--warning);">
                    {{ collect($children)->sum(fn($child) => $child['vaccines_total'] - $child['vaccines_done']) }}
                </div>
            </div>
        </div>

        <!-- Upcoming Vaccines -->
        <div class="card">
            <h2 class="card-title">
                <i class="fas fa-bell"></i> Prochains vaccins
            </h2>

            @if (!empty($children))
            <ul style="list-style: none; padding: 0;">
                @foreach ($children as $child)
                <div class="card mb-3 shadow-sm border-left-primary">
                    <div class="card-body">
                        <h5 class="card-title text-primary">
                            👶 Nom de l’enfant : {{ $child->name }}
                        </h5>

                        <p class="card-text">
                            🚻 <strong>Sexe :</strong> {{ ucfirst($child->gender) }}
                        </p>

                        <p class="card-text">
                            💉 <strong>Prochain vaccin :</strong> {{ $child->next_vaccine_name }}
                        </p>

                        <p class="card-text">
                            📅 <strong>Rendez-vous :</strong>
                            {{ $child->next_rendez_vous ? \Carbon\Carbon::parse($child->next_rendez_vous)->format('d/m/Y') : 'Non défini' }}
                        </p>
                    </div>
                </div>


                @endforeach

            </ul>
            @else
            <div style="text-align: center; padding: 1rem; color: #666;">
                <i class="fas fa-check-circle" style="color: var(--success); font-size: 1.5rem; margin-bottom: 0.5rem;"></i>
                <p>Aucun vaccin en attente pour le moment</p>
            </div>
            @endif
        </div>

        <!-- Children List -->
        <div class="card" id="progress">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2 class="card-title">
                    <i class="fas fa-child"></i> Mes enfants
                </h2>
                <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#addChildModal">
                    <i class="fas fa-plus"></i> Ajouter un enfant
                </a>
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
                            <small>{{ ucfirst($child->gender) }}</small><br>
                            <small>{{ $child->age }} an(s) - Né(e) le {{ \Carbon\Carbon::parse($child['dob'])->format('d/m/Y') }}</small>
                        </div>
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
                    <div class="mt-3">
                        <!-- زر تعديل -->
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editChildModal{{ $child->id }}">
                            Modifier
                        </button>
                        <!-- زر حذف -->
                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteChildModal{{ $child->id }}">
                            Supprimer
                        </button>
                    </div>
                </div>
                <div class="modal fade" id="editChildModal{{ $child->id }}" tabindex="-1" aria-labelledby="editChildModalLabel{{ $child->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editChildModalLabel{{ $child->id }}">Modifier l'enfant</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('children.update', $child->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="mb-3">
                                        <label for="child_name{{ $child->id }}" class="form-label">Nom de l'enfant</label>
                                        <input type="text" class="form-control" id="child_name{{ $child->id }}" name="name" value="{{ $child->name }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="dob{{ $child->id }}" class="form-label">Date de naissance</label>
                                        <input type="date" class="form-control" id="dob{{ $child->id }}" name="dob" value="{{ $child->dob }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="gender{{ $child->id }}" class="form-label">Sexe</label>
                                        <select class="form-control" id="gender{{ $child->id }}" name="gender" required>
                                            <option value="M" {{ $child->gender === 'M' ? 'selected' : '' }}>Masculin</option>
                                            <option value="F" {{ $child->gender === 'F' ? 'selected' : '' }}>Féminin</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-success">Enregistrer les modifications</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal تأكيد الحذف -->
                <div class="modal fade" id="deleteChildModal{{ $child->id }}" tabindex="-1" aria-labelledby="deleteChildModalLabel{{ $child->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title" id="deleteChildModalLabel{{ $child->id }}">Confirmation de suppression</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                            </div>
                            <div class="modal-body">
                                Êtes-vous sûr de vouloir supprimer l'enfant <strong>{{ $child->name }}</strong> ?
                            </div>
                            <div class="modal-footer">
                                <form action="{{ route('children.destroy', $child->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-danger">Supprimer</button>
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
                <p style="color: #888; margin-bottom: 1.5rem;">Commencez par ajouter votre premier enfant pour suivre son calendrier vaccinal</p>
                <a href="{{ route('child.add') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Ajouter un enfant
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="calendarModal" tabindex="-1" aria-labelledby="calendarModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="calendarModalLabel">Calendrier de Vaccination</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- محتويات تقويم اللقاح -->
                <p>هنا يتم عرض تقويم اللقاحات للطفل.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
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
    document.addEventListener('DOMContentLoaded', function() {
        const buttons = document.querySelectorAll('.view-progress-btn');
        buttons.forEach(btn => {
            btn.addEventListener('click', function() {
                // إغلاق المودال
                $('#messagesModal').modal('hide');

                // الانتقال إلى قسم #progress بعد وقت بسيط لتفادي مشاكل التحريك داخل المودال
                setTimeout(() => {
                    document.getElementById('progress')?.scrollIntoView({
                        behavior: 'smooth'
                    });
                }, 400);
            });
        });
    });
</script>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script><!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-..." crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

@endsection