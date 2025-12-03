@php
use Illuminate\Support\Facades\Cache;
$headers = Cache::get('csv_headers', []);
$csvData = Cache::get('csv_data', []);
$filename = Cache::get('csv_filename', '');
$rowCount = count($csvData);
@endphp

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Envoi de Mails - Mailwo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-[#1B97B1]/30 to-[#DC2C8C]/30 px-2 md:px-6 py-6 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-center">
            <div>
                <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-800 mb-2">
                    <i class="fas fa-paper-plane text-[#1B97B1]"></i> Envoi de Mails Personnalisés
                </h1>
                <p class="text-gray-600">Composez et envoyez des emails personnalisés à vos contacts</p>
            </div>
            
            <a href="{{ route('import.create') }}" class="px-3 py-1 mt-5 bg-[#1B97B1] text-white text-center rounded cursor-pointer">
                Importer un nouveaux fichier
            </a>
        </div>

        <!-- Messages -->
        @if(session('success'))
        <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-md">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-xl mr-3"></i>
                <div>
                    <p class="font-bold">{{ session('success') }}</p>
                    @if(session('sent_count'))
                    <p class="text-sm">{{ session('sent_count') }} email(s) envoyé(s) avec succès</p>
                    @endif
                </div>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-md">
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

        <!-- Alerte si pas de fichier importé -->
        @if(empty($headers))
        <div class="mb-6 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-lg shadow-md">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-xl mr-3"></i>
                <div>
                    <p class="font-bold">Aucun fichier importé</p>
                    <p class="text-sm mt-1">Veuillez d'abord <a href="{{ route('import.create') }}" class="underline font-semibold">importer un fichier</a> pour utiliser les variables dynamiques.</p>
                </div>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Formulaire principal -->
            <div class="lg:col-span-2">
                <form action="{{ route('sendMail.store') }}" method="POST" id="mailForm" class="bg-white rounded-xl shadow-xl p-8">
                    @csrf

                    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-envelope text-[#1B97B1] mr-3"></i>
                        Composer votre message
                    </h2>

                    <!-- Sujet -->
                    <div class="mb-6">
                        <label for="subject" class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-heading mr-2"></i>Sujet du mail
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   name="subject" 
                                   id="subject" 
                                   value="{{ old('subject') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   placeholder="Ex: Bonjour @{{prenom}}, voici notre offre spéciale"
                                   required>
                            <button type="button" onclick="showVariables('subject')" class="absolute right-3 top-3 text-[#1B97B1] hover:text-purple-800">
                                <i class="fas fa-code"></i>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Utilisez @{{variable}} pour insérer des données dynamiques</p>
                    </div>

                    <!-- Corps du message -->
                    <div class="mb-6">
                        <label for="body" class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-align-left mr-2"></i>Corps du message
                        </label>
                        <div class="relative">
                            <textarea name="body" 
                                      id="body" 
                                      rows="12"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                      placeholder="Bonjour @{{prenom}},&#x0a;&#x0a;Nous sommes ravis de vous contacter..."
                                      required>{{ old('body') }}</textarea>
                            <button type="button" onclick="showVariables('body')" class="absolute right-3 top-3 text-[#1B97B1] hover:text-purple-800">
                                <i class="fas fa-code"></i>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Utilisez @{{variable}} pour personnaliser le message</p>
                    </div>

                    <!-- Email de l'expéditeur -->
                    <div class="mb-6">
                        <label for="from_email" class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-user mr-2"></i>Email expéditeur
                        </label>
                        <input type="email" name="from_email" id="from_email" 
                               value="{{ old('from_email') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               placeholder="votre-email@exemple.com"
                               required>
                    </div>

                    <!-- Nom de l'expéditeur -->
                    <div class="mb-6">
                        <label for="from_name" class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-signature mr-2"></i>Nom de l'expéditeur
                        </label>
                        <input type="text" name="from_name" id="from_name" value="{{ old('from_name') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               placeholder="Votre Nom ou Entreprise"
                               required>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="flex gap-4">
                        <button type="submit" class="flex-1 bg-gradient-to-r from-[#1B97B1]/90 to-[#DC2C8C]/90 text-white font-bold py-4 px-6 rounded-xl shadow-lg hover:from-[#1B97B1] hover:to-[#DC2C8C] transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-paper-plane mr-2"></i>Envoyer les emails
                        </button>
                        <button type="button" onclick="previewEmail()" class="bg-gray-200 text-gray-700 font-bold py-4 px-6 rounded-xl hover:bg-gray-300 transition-colors duration-300">
                            <i class="fas fa-eye mr-2"></i>Aperçu
                        </button>
                    </div>
                </form>
            </div>

            <!-- Panneau latéral -->
            <div class="space-y-6">
                <!-- Variables disponibles -->
                @if(!empty($headers))
                <div class="bg-white rounded-xl shadow-xl p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-tags text-[#DC2C8C] mr-3"></i>
                        Variables disponibles
                    </h3>
                    <p class="text-sm text-gray-600 mb-4">Cliquez pour copier</p>
                    <div class="space-y-2">
                        @foreach($headers as $header)
                        <button type="button" 
                                onclick="copyVariable('{{ $header }}')"
                                class="w-full text-left bg-[#1B97B1]/10 hover:bg-[#1B97B1]/20 px-4 py-3 rounded-lg transition-colors duration-200 group">
                            <code class="text-[#1B97B1] font-mono text-sm">&#123;&#123;{{ $header }}&#125;&#125;</code>
                            <i class="fas fa-copy float-right text-[#1B97B1]/80 group-hover:text-[#1B97B1] mt-1"></i>
                        </button>
                        @endforeach
                    </div>
                </div>

                <!-- Informations sur le fichier -->
                <div class="bg-white rounded-xl shadow-xl p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-info-circle text-[#1B97B1] mr-3"></i>
                        Informations
                    </h3>
                    <div class="space-y-3 text-sm text-gray-700">
                        <div class="flex items-center justify-between pb-2 border-b">
                            <span class="font-semibold">Fichier :</span>
                            <span class="text-gray-600">{{ $filename ?? 'N/A' }}</span>
                        </div>
                        <div class="flex items-center justify-between pb-2 border-b">
                            <span class="font-semibold">Destinataires :</span>
                            <span class="text-[#DC2C8C] font-bold">{{ $rowCount ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-semibold">Colonnes :</span>
                            <span class="text-[#1B97B1] font-bold">{{ count($headers) }}</span>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Conseils -->
                <div class="bg-white rounded-xl shadow-xl p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-lightbulb text-yellow-500 mr-3"></i>
                        Conseils
                    </h3>
                    <ul class="space-y-2 text-sm text-gray-700">
                        <li class="flex items-start">
                            <i class="fas fa-check text-[#1B97B1] mr-2 mt-1"></i>
                            <span>Personnalisez avec <code class="bg-gray-100 px-1 rounded">@{{variable}}</code></span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-[#1B97B1] mr-2 mt-1"></i>
                            <span>Testez avec un aperçu avant d'envoyer</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-[#1B97B1] mr-2 mt-1"></i>
                            <span>Vérifiez que votre fichier contient la colonne "email"</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal d'aperçu -->
    <div id="previewModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-2xl font-bold text-gray-800"><i class="fas fa-eye text-[#1B97B1] mr-2"></i>Aperçu du message</h3>
                <button onclick="closePreview()" class="text-gray-500 hover:text-gray-700"><i class="fas fa-times text-2xl"></i></button>
            </div>
            <div class="p-6">
                <p class="text-sm font-bold text-gray-600 mb-1">Sujet:</p>
                <p id="previewSubject" class="text-lg font-semibold text-gray-800 mb-4"></p>
                <p class="text-sm font-bold text-gray-600 mb-1">Corps:</p>
                <div id="previewBody" class="bg-gray-50 p-4 rounded-lg whitespace-pre-wrap text-gray-800"></div>
                <p class="text-xs text-gray-500 italic mt-2"><i class="fas fa-info-circle mr-1"></i>Ceci est un aperçu avec les données de la première ligne du fichier</p>
            </div>
        </div>
    </div>

    <script>
        function copyVariable(varName) {
            const text = `@{{${varName}}}`;
            navigator.clipboard.writeText(text).then(() => { showToast(`Variable ${text} copiée!`) });
        }

        function showToast(message) {
            const toast = document.createElement('div');
            toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in';
            toast.innerHTML = `<i class="fas fa-check-circle mr-2"></i>${message}`;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 2000);
        }

        function showVariables(targetField) {
            const variablesSection = document.querySelector('.space-y-2');
            if(variablesSection) variablesSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function closePreview() { document.getElementById('previewModal').classList.add('hidden'); }
        document.getElementById('previewModal')?.addEventListener('click', (e) => { if(e.target.id==='previewModal'){closePreview()} });

        function previewEmail() {
            const subject = document.getElementById('subject').value;
            const body = document.getElementById('body').value;

            @if(!empty($csvData) && count($csvData) > 0)
                let previewSubject = subject;
                let previewBody = body;
                const firstRow = @json($csvData[0] ?? []);
                const headers = @json($headers);

                headers.forEach((header, index) => {
                    const regex1 = new RegExp('{{' + header + '}}', 'g');
                    const regex2 = new RegExp('@{{' + header + '}}', 'g');
                    const value = firstRow[index] || '';
                    previewSubject = previewSubject.replace(regex1, value).replace(regex2, value);
                    previewBody = previewBody.replace(regex1, value).replace(regex2, value);
                });

                document.getElementById('previewSubject').textContent = previewSubject;
                document.getElementById('previewBody').textContent = previewBody;
            @else
                document.getElementById('previewSubject').textContent = subject;
                document.getElementById('previewBody').textContent = body;
            @endif

            document.getElementById('previewModal').classList.remove('hidden');
        }

        // Animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
            .animate-fade-in { animation: fadeIn 0.3s ease-out; }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
