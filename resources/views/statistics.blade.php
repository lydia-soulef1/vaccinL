<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

@extends('layouts.main')

@section('content')



<div class="dashboard-container">
    <div class="sidebar">
        <div class="logo text-center mb-4">
            <img src="{{ asset('images/logo-white.jpg') }}" alt="VacciBaby" class="img-fluid">
        </div>

        <ul class="nav-menu list-unstyled">
            <!-- Dashboard link -->
            <li class="nav-item">
                <a href="#tableu" class="nav-link active">
                    <i class="fas fa-home"></i> Tableau de bord
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

    <div class="main-content">
    <h1 class="text-center fw-bold display-4 my-2">Tableau de bord admin</h1>
    <h1 class="text-2xl font-bold mb-6">Tableu de bord</h1>
        <div class="stats-grid">
            <div class="card stat-card">
                <div>👨‍⚕️ Pédiatres</div>
                <div class="stat-number">{{ $pediatriciansCount }}</div>
            </div>
            <div class="card stat-card">
                <div>👪 Parents</div>
                <div class="stat-number" style="color: var(--success);">
                    {{ $parentsCount }}
                </div>
            </div>
            <div class="card stat-card">
                <div>🧒 Enfants</div>
                <div class="stat-number" style="color: var(--warning);">
                    {{ $childrenCount }}
                </div>
            </div>
        </div>


        <h1 class="text-2xl font-bold mb-6">Liste des pédiatres non valider</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($pediatricians->where('accepted', false) as $pediatrician)
            <div class="flex flex-col p-4 bg-white shadow-md rounded-xl border border-gray-200 relative">
                <!-- Symbole avec la première lettre du nom -->
                <div class="w-12 h-12 bg-blue-500 text-white flex items-center justify-center rounded-full text-lg font-bold mb-4">
                    {{ strtoupper(substr($pediatrician->name, 0, 1)) }}
                </div>

                <!-- Informations du pédiatre -->
                <div class="ml-4 flex-1">
                    <h2 class="text-lg font-semibold text-gray-800">Dr. {{ $pediatrician->name }} {{ $pediatrician->prenom }}</h2>

                    <!-- Labels pour chaque donnée -->
                    <div class="mt-2">
                        <p class="text-sm text-gray-600"><strong>Email:</strong> {{ $pediatrician->email ?? 'Aucun e-mail disponible' }}</p>
                        <p class="text-sm text-gray-600"><strong>Hôpital:</strong> {{ $pediatrician->hospital ?? 'Aucun hôpital disponible' }}</p>
                        <p class="text-sm text-gray-600"><strong>Numéro RPPS:</strong> {{ $pediatrician->rpps_number ?? 'Aucun numéro RPPS disponible' }}</p>
                    </div>
                </div>

                <!-- A Buttons "Valider" et "Cancel" à la fin du container -->
                <div class="flex justify-start space-x-2 mt-4">
                    <!-- Valider Button -->
                    <button onclick="acceptPediatrician({{ $pediatrician->id }})"
                        class="bg-green-600 text-white rounded-full px-6 py-2 hover:bg-green-700 transition duration-300">
                        Valider
                    </button>


                    <!-- Cancel Button -->
                    <button class="bg-red-600 text-white rounded-full px-6 py-2 hover:bg-red-700 transition duration-300">
                        Cancel
                    </button>
                </div>
            </div>
            @empty
            <p class="text-gray-600">Aucun pédiatre non valider.</p>
            @endforelse
        </div>

        <!-- قسم الآباء -->
        <div class="mt-10">
            <h1 class="text-2xl font-bold mb-6">Liste des parents</h1>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($parents as $parent)
                @php
                $children = DB::table('children')->where('parent_id', $parent->id)->get();
                @endphp

                <div class="p-4 bg-white shadow-md rounded-xl border border-gray-200 relative w-full">
                    <div class="grid grid-cols-[auto_1fr] gap-4 items-center mb-2">
                        <!-- الدائرة -->
                        <div class="w-12 h-12 bg-purple-500 text-white rounded-full text-lg font-bold flex items-center justify-center">
                            {{ strtoupper(substr($parent->name, 0, 1)) }}
                        </div>

                        <!-- المعلومات -->
                        <div>
                            <h2 class="text-lg font-semibold text-gray-800">{{ $parent->name }}</h2>
                            <p class="text-sm text-gray-600 mt-1 break-words"><strong>Email :</strong> {{ $parent->email }}</p>
                        </div>
                    </div>

                    <button onclick="loadChildren({{ $parent->user_id }})" class="bg-blue-500 text-white px-4 py-2 rounded">
                        Détails
                    </button>

                    <!-- المودال -->
                    <div id="modal-{{ $parent->user_id }}" class="hidden fixed top-0 left-0 w-full h-full flex items-center justify-center bg-black bg-opacity-50 z-50">
                        <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-lg space-y-4">
                            <h3 class="text-xl font-bold text-center mb-4">Enfants de {{ $parent->name }} 🧸</h3>
                            <ul class="space-y-4" id="children-list-{{ $parent->user_id }}">
                                <li>Chargement...</li>
                            </ul>
                            <button onclick="closeModal({{ $parent->user_id }})" class="mt-4 bg-gray-500 hover:bg-gray-700 text-white px-6 py-3 rounded-full w-full">
                                Fermer
                            </button>
                        </div>
                    </div>

                </div>
                @endforeach

            </div>
        </div>
    </div>
</div>

<script>
    function loadChildren(parentId) {
        // إظهار المودال أولًا حتى المستخدم يرى التحميل
        const modal = document.getElementById('modal-' + parentId);
        const list = document.getElementById('children-list-' + parentId);
        list.innerHTML = '<li>Chargement...</li>';
        modal.classList.remove('hidden');
        console.log("Parent ID:", parentId);

        fetch(`/admin/parent/${parentId}/children`)
            .then(res => res.json())
            .then(data => {
                console.log("Données récupérées:", data);
                list.innerHTML = ''; // تفريغ القائمة أولاً

                if (data.children.length > 0) {
                    data.children.forEach(child => {
                        const item = document.createElement('li');
                        let genderIcon = '';
                        if (child.gender === 'M') {
                            genderIcon = '👦'; // ذكر
                        } else if (child.gender === 'F') {
                            genderIcon = '👧'; // أنثى
                        }

                        item.innerHTML = `
                            <div class="flex items-center justify-between p-4 bg-blue-100 rounded-lg shadow-lg">
                                <div class="flex items-center space-x-2">
                                    <span class="text-xl font-bold">${genderIcon} ${child.name}</span>
                                </div>
                                <span class="text-gray-600 text-sm">Né le ${child.dob}</span>
                            </div>
                        `;
                        list.appendChild(item);
                    });
                } else {
                    list.innerHTML = '<li class="text-center text-gray-500">Aucun enfant associé. 🤷‍♂️</li>';
                }
            })
            .catch(err => {
                list.innerHTML = '<li class="text-center text-red-500">Erreur lors du chargement. ⚠️</li>';
                console.error(err);
            });
    }



    function closeModal(id) {
        document.getElementById('modal-' + id).classList.add('hidden');
    }
</script>
<script>
    function acceptPediatrician(id) {
        if (confirm("Confirmer la validation de ce pédiatre ?")) {
            fetch(`/pediatrician/accept/${id}`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    "Content-Type": "application/json"
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Pédiatre validé et email envoyé !");
                    location.reload(); // إعادة تحميل الصفحة لتحديث القائمة
                } else {
                    alert("Erreur lors de la validation.");
                }
            })
            .catch(error => {
                console.error("Erreur:", error);
                alert("Erreur lors de la validation.");
            });
        }
    }
</script>




<!-- بعد Tailwind -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

@endsection