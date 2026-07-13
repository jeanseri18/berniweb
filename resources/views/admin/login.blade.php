<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin - BERRNI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        berrni: {
                            50: '#f0fdf2',
                            100: '#dcfce4',
                            500: '#25ce28', // BERRNI Green
                            600: '#1e944f', // BERRNI Dark
                            900: '#14532d',
                        },
                        cream: '#fbf5e7',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        serif: ['Playfair Display', 'serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-berrni-900 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-xl shadow-2xl w-full max-w-md border border-berrni-500">
        <div class="text-center mb-8">
            <img src="{{ asset('Logo_BERRNI-removebg-preview.png') }}" alt="BERRNI" class="h-24 w-auto mx-auto mb-4">
            <h1 class="text-2xl font-serif font-bold text-gray-800">Administration</h1>
            <p class="text-gray-500 mt-2">Connectez-vous pour accéder au backoffice</p>
        </div>

        @if($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm" role="alert">
                <p>{{ $errors->first() }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <form action="{{ route('admin.login.post') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Adresse Email</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-envelope text-gray-400"></i>
                    </div>
                    <input type="email" name="email" id="email" required 
                        class="pl-10 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-berrni-500 focus:border-berrni-500 sm:text-sm p-2.5 border" 
                        placeholder="admin@berrni.com">
                </div>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-gray-400"></i>
                    </div>
                    <input type="password" name="password" id="password" required 
                        class="pl-10 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-berrni-500 focus:border-berrni-500 sm:text-sm p-2.5 border" 
                        placeholder="••••••••">
                </div>
            </div>

            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-berrni-600 hover:bg-berrni-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-berrni-500 transition-colors font-bold">
                Se connecter
            </button>
        </form>

        <div class="mt-6 text-center text-xs text-gray-400">
            &copy; {{ date('Y') }} BERRNI. Tous droits réservés.
        </div>
    </div>
</body>
</html>
