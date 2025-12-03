@php
use Illuminate\Support\Facades\Cache;
$headers = Cache::get('csv_headers', []);
$rowCount = count(Cache::get('csv_data', []));
$filename = Cache::get('csv_filename', '');
@endphp

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Import fichier - Mailwo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-[#1B97B1]/30 to-[#DC2C8C]/30 px-2 md:px-6 py-6 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8 flex flex-col md:flex-row justify-between items-center">
            <div>
                <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-800 mb-2">
                <i class="fas fa-file-excel text-[#1B97B1]"></i> Import de fichiers
                </h1>
                <p class="text-gray-600">Importez vos contacts depuis un fichier csv,xlsx ou xls</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="px-3 py-1 bg-[#DC2C8C] text-white rounded cursor-pointer">
                    Déconnexion
                </button>
            </form>

        </div>

        <!-- Messages de succès/erreur -->
        @if(session('success'))
        <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-md animate-fade-in">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-xl mr-3"></i>
                <div>
                    <p class="font-bold">{{ session('success') }}</p>
                    @if(session('row_count'))
                    <p class="text-sm">{{ session('row_count') }} lignes détectées</p>
                    @endif
                </div>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-md animate-fade-in">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-xl mr-3"></i>
                <p class="font-bold">{{ session('error') }}</p>
            </div>
        </div>
        @endif

        @if($errors->any())
        <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-md">
            <div class="flex items-center mb-2">
                <i class="fas fa-exclamation-circle text-xl mr-3"></i>
                <p class="font-bold">Erreurs de validation:</p>
            </div>
            <ul class="list-disc list-inside ml-8">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Formulaire d'upload -->
            <div class="bg-white rounded-xl shadow-xl p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-upload text-[#1B97B1] mr-3"></i>
                    Télécharger un fichier 
                </h2>

                <form action="{{ route('import.store') }}" method="POST" enctype="multipart/form-data" id="csvForm">
                    @csrf
                    
                    <!-- Zone de drag & drop -->
                    <div class="mb-6">
                        <div id="dropZone" class="border-3 border-dashed border-indigo-300 rounded-xl p-8 text-center transition-all duration-300 hover:border-[#1B97B1] hover:bg-[#1B97B1]/10 cursor-pointer">
                            <input type="file" name="csv_file" id="csv_file" class="hidden" accept=".csv,.txt,.xlsx,.xls" required>
                            
                            <div id="uploadIcon">
                                <i class="fas fa-cloud-upload-alt text-6xl text-[#1B97B1]/50 mb-4"></i>
                                <p class="text-lg font-semibold text-gray-700 mb-2">Glissez-déposez votre fichier ici</p>
                                <p class="text-sm text-gray-500 mb-4">ou cliquez pour sélectionner un fichier</p>
                                <button type="button" onclick="document.getElementById('csv_file').click()" class="bg-[#1B97B1] text-white px-6 py-2 rounded-lg hover:bg-[#1B97B1] transition-colors duration-300">
                                    <i class="fas fa-folder-open mr-2"></i>Parcourir
                                </button>
                            </div>

                            <div id="fileInfo" class="hidden">
                                <i class="fas fa-file text-6xl text-[#1B97B1] mb-4"></i>
                                <p class="text-lg font-semibold text-gray-700" id="fileName"></p>
                                <p class="text-sm text-gray-500" id="fileSize"></p>
                                <button type="button" onclick="clearFile()" class="mt-3 text-red-600 hover:text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i>Supprimer
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Bouton de soumission -->
                    <button type="submit" id="submitBtn" disabled class="w-full bg-gradient-to-r from-[#1B97B1]/90 to-purple-600 text-white font-bold py-4 px-6 rounded-xl shadow-lg hover:from-[#1B97B1] hover:to-purple-700 transition-all duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                        <i class="fas fa-upload mr-2"></i>Extraire les données du fichier
                    </button>
                </form>
            </div>

            <!-- Informations et aperçu -->
            <div>
                <!-- Instructions -->
                <div class="bg-white rounded-xl shadow-xl p-8 mb-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-info-circle text-[#DC2C8C] mr-3"></i>
                        Instructions
                    </h3>
                    <ul class="space-y-3 text-gray-700">
                        <li class="flex items-start"><i class="fas fa-check text-[#1B97B1] mr-3 mt-1"></i>Le fichier doit être au format : <strong>csv,xlsx ou xls</strong></li>
                        <li class="flex items-start"><i class="fas fa-check text-[#1B97B1] mr-3 mt-1"></i>Taille maximale : <strong>10 MB</strong></li>
                        <li class="flex items-start"><i class="fas fa-check text-[#1B97B1] mr-3 mt-1"></i>La première ligne doit contenir les <strong>en-têtes</strong></li>
                    </ul>
                </div>

                <!-- Aperçu des headers si disponibles -->
                @if(!empty($headers))
                <div class="bg-white rounded-xl shadow-xl p-8 mt-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-list text-[#1B97B1] mr-3"></i>
                        Colonnes détectées
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($headers as $header)
                        <span class="bg-[#DC2C8C]/10 text-[#1B97B1] px-4 py-2 rounded-full font-semibold text-sm">
                            <i class="fas fa-tag mr-1"></i>{{ $header }}
                        </span>
                        @endforeach
                    </div>
                    <div class="my-4 pt-4 border-t border-gray-200">
                        <p class="text-sm text-gray-600"><i class="fas fa-database mr-2"></i>Fichier : <strong>{{ $filename }}</strong></p>
                        <p class="text-sm text-gray-600 mt-1"><i class="fas fa-list-ol mr-2"></i>Nombre de lignes : <strong>{{ $rowCount }}</strong></p>
                        <p class="text-sm text-gray-600 mt-1"><i class="fas fa-list-ol mr-2"></i>Nombre de colonnes : <strong>{{ count($headers) }}</strong></p>
                    </div>
                    <a href="{{ route('sendMail.create') }}" class="bg-[#1B97B1] text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition-colors duration-300">Envoyer des mails</a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('csv_file');
        const uploadIcon = document.getElementById('uploadIcon');
        const fileInfo = document.getElementById('fileInfo');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const submitBtn = document.getElementById('submitBtn');

        dropZone.addEventListener('click', () => { if(!fileInput.files.length) fileInput.click(); });
        dropZone.addEventListener('dragover', (e) => { e.preventDefault(); dropZone.classList.add('border-indigo-500', 'bg-indigo-50'); });
        dropZone.addEventListener('dragleave', () => { dropZone.classList.remove('border-indigo-500', 'bg-indigo-50'); });
        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-indigo-500', 'bg-indigo-50');
            const files = e.dataTransfer.files;
            if(files.length>0) { fileInput.files=files; displayFileInfo(files[0]); }
        });
        fileInput.addEventListener('change', (e) => { if(e.target.files.length>0) displayFileInfo(e.target.files[0]); });

        function displayFileInfo(file) {
            uploadIcon.classList.add('hidden');
            fileInfo.classList.remove('hidden');
            fileName.textContent = file.name;
            fileSize.textContent = `Taille: ${(file.size/1024).toFixed(2)} KB`;
            submitBtn.disabled=false;
        }

        function clearFile() {
            fileInput.value='';
            uploadIcon.classList.remove('hidden');
            fileInfo.classList.add('hidden');
            submitBtn.disabled=true;
        }

        // Animation fadeIn
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
            .animate-fade-in { animation: fadeIn 0.5s ease-out; }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
