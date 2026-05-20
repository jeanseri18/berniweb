<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BERRNI Admin - @yield('title')</title>
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
<body class="bg-cream font-sans text-gray-800">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r border-gray-200 flex flex-col shadow-sm relative z-20">
            <div class="p-6 border-b border-gray-100 flex items-center justify-center">
                 <div class="text-center">
                    <img src="{{ asset('Logo_BERRNI-removebg-preview.png') }}" alt="BERRNI" class="h-20 w-auto mx-auto mb-2">
                    <span class="text-xs uppercase tracking-widest text-berrni-600 font-semibold">Administration</span>
                 </div>
            </div>
            
            <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 mt-2">Général</p>
                
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-berrni-50 text-berrni-600 font-semibold shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-chart-pie w-5 {{ request()->routeIs('admin.dashboard') ? 'text-berrni-500' : 'text-gray-400' }}"></i>
                    <span>Tableau de bord</span>
                </a>

                <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 mt-5">Utilisateurs</p>

                <a href="{{ route('admin.users.index') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.users.index') || request()->routeIs('admin.users.show') ? 'bg-berrni-50 text-berrni-600 font-semibold shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-users w-5 {{ request()->routeIs('admin.users.index') ? 'text-berrni-500' : 'text-gray-400' }}"></i>
                    <span>Tous les utilisateurs</span>
                </a>
                
                <a href="{{ route('admin.users.couriers') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.users.couriers') ? 'bg-berrni-50 text-berrni-600 font-semibold shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-truck w-5 {{ request()->routeIs('admin.users.couriers') ? 'text-berrni-500' : 'text-gray-400' }}"></i>
                    <span>Livreurs</span>
                </a>

                <a href="{{ route('admin.users.senders') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.users.senders') ? 'bg-berrni-50 text-berrni-600 font-semibold shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-paper-plane w-5 {{ request()->routeIs('admin.users.senders') ? 'text-berrni-500' : 'text-gray-400' }}"></i>
                    <span>Expéditeurs</span>
                </a>

                <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 mt-5">Opérations</p>

                <a href="{{ route('admin.kyc.list') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.kyc*') ? 'bg-berrni-50 text-berrni-600 font-semibold shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-id-card w-5 {{ request()->routeIs('admin.kyc*') ? 'text-berrni-500' : 'text-gray-400' }}"></i>
                    <span>Validations KYC</span>
                    @php $pendingCount = \App\Models\KycSubmission::where('status', 'pending')->count(); @endphp
                    @if($pendingCount > 0)
                        <span class="ml-auto bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $pendingCount }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.parcels') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.parcels*') ? 'bg-berrni-50 text-berrni-600 font-semibold shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-box w-5 {{ request()->routeIs('admin.parcels*') ? 'text-berrni-500' : 'text-gray-400' }}"></i>
                    <span>Colis</span>
                </a>

                <a href="{{ route('admin.offers') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.offers') ? 'bg-berrni-50 text-berrni-600 font-semibold shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-handshake w-5 {{ request()->routeIs('admin.offers') ? 'text-berrni-500' : 'text-gray-400' }}"></i>
                    <span>Offres</span>
                </a>

                <a href="{{ route('admin.sos') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.sos') ? 'bg-red-50 text-red-600 font-semibold shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-exclamation-triangle w-5 {{ request()->routeIs('admin.sos') ? 'text-red-500' : 'text-gray-400' }}"></i>
                    <span>SOS Alertes</span>
                    @php $sosCount = \App\Models\SosAlert::where('status', 'open')->count(); @endphp
                    @if($sosCount > 0)
                        <span class="ml-auto bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $sosCount }}</span>
                    @endif
                </a>

                <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 mt-5">Finances</p>

                <a href="{{ route('admin.finances') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.finances') ? 'bg-berrni-50 text-berrni-600 font-semibold shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-wallet w-5 {{ request()->routeIs('admin.finances') ? 'text-berrni-500' : 'text-gray-400' }}"></i>
                    <span>Portefeuilles</span>
                </a>

                <a href="{{ route('admin.transactions') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.transactions') ? 'bg-berrni-50 text-berrni-600 font-semibold shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-exchange-alt w-5 {{ request()->routeIs('admin.transactions') ? 'text-berrni-500' : 'text-gray-400' }}"></i>
                    <span>Transactions</span>
                </a>

                <a href="{{ route('admin.payments') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.payments*') ? 'bg-berrni-50 text-berrni-600 font-semibold shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-credit-card w-5 {{ request()->routeIs('admin.payments*') ? 'text-berrni-500' : 'text-gray-400' }}"></i>
                    <span>Paiements CinetPay</span>
                </a>

                <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 mt-5">Modération</p>

                <a href="{{ route('admin.messages') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.messages') ? 'bg-berrni-50 text-berrni-600 font-semibold shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-comments w-5 {{ request()->routeIs('admin.messages') ? 'text-berrni-500' : 'text-gray-400' }}"></i>
                    <span>Messagerie</span>
                </a>

                <a href="{{ route('admin.reviews') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.reviews') ? 'bg-berrni-50 text-berrni-600 font-semibold shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-star w-5 {{ request()->routeIs('admin.reviews') ? 'text-berrni-500' : 'text-gray-400' }}"></i>
                    <span>Avis</span>
                </a>

                <a href="{{ route('admin.notifications') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.notifications') ? 'bg-berrni-50 text-berrni-600 font-semibold shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-bell w-5 {{ request()->routeIs('admin.notifications') ? 'text-berrni-500' : 'text-gray-400' }}"></i>
                    <span>Notifications</span>
                </a>
            </nav>

            <div class="p-4 border-t border-gray-100">
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center space-x-3 px-4 py-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Déconnexion</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto bg-gray-50/50">
            <!-- Header Mobile / Breadcrumb placeholder -->
            <header class="bg-white/80 backdrop-blur-md sticky top-0 z-10 border-b border-gray-100 px-8 py-4 flex justify-between items-center">
                <h2 class="text-xl font-serif font-bold text-gray-800">@yield('title', 'Admin')</h2>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-full bg-berrni-100 flex items-center justify-center text-berrni-600 font-bold">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <span class="text-sm font-medium text-gray-700 hidden md:block">{{ auth()->user()->name }}</span>
                    </div>
                </div>
            </header>

            <div class="p-8 max-w-7xl mx-auto">
                @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg shadow-sm flex items-center" role="alert">
                        <i class="fas fa-check-circle mr-3 text-xl"></i>
                        <p>{{ session('success') }}</p>
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow-sm flex items-center" role="alert">
                        <i class="fas fa-exclamation-circle mr-3 text-xl"></i>
                        <p>{{ session('error') }}</p>
                    </div>
                @endif
                
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
