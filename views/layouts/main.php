<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title . ' - ' : '' ?>Venus IT Help Desk</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="<?= ASSETS_URL ?>/img/favicon.ico" type="image/x-icon">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.20/dist/sweetalert2.min.css">
    
    <!-- Quill Editor -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/style.css">
    
    <?php if (isset($extraStyles)): ?>
        <?= $extraStyles ?>
    <?php endif; ?>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex h-screen">
        <!-- Sidebar - Sol Menü -->
        <aside class="bg-gray-800 text-white w-64 flex-shrink-0 hidden md:block">
            <div class="p-4 border-b border-gray-700">
                <h1 class="text-xl font-bold">
                    <i class="fas fa-laptop-medical mr-2"></i>
                    Venus IT Desk
                </h1>
            </div>
            
            <nav class="mt-4">
                <ul>
                    <!-- Dashboard -->
                    <li class="mb-2">
                        <a href="<?= BASE_URL ?>/index.php" class="flex items-center px-4 py-2 hover:bg-gray-700 rounded">
                            <i class="fas fa-tachometer-alt mr-3"></i>
                            Dashboard
                        </a>
                    </li>
                    
                    <!-- Ticketlar -->
                    <li class="mb-2">
                        <a href="<?= BASE_URL ?>/tickets.php" class="flex items-center px-4 py-2 hover:bg-gray-700 rounded">
                            <i class="fas fa-ticket-alt mr-3"></i>
                            Tickets
                        </a>
                    </li>
                    
                    <!-- Yeni Ticket -->
                    <li class="mb-2">
                        <a href="<?= BASE_URL ?>/ticket/create.php" class="flex items-center px-4 py-2 hover:bg-gray-700 rounded">
                            <i class="fas fa-plus-circle mr-3"></i>
                            Yeni Ticket
                        </a>
                    </li>
                    
                    <?php if (AuthHelper::isAdmin()): ?>
                    <!-- Yönetim (Sadece Admin) -->
                    <li class="mb-1 mt-6">
                        <h3 class="px-4 text-xs text-gray-400 uppercase tracking-wider">
                            Yönetim
                        </h3>
                    </li>
                    
                    <!-- Kullanıcılar -->
                    <li class="mb-2">
                        <a href="<?= BASE_URL ?>/users.php" class="flex items-center px-4 py-2 hover:bg-gray-700 rounded">
                            <i class="fas fa-users mr-3"></i>
                            Kullanıcılar
                        </a>
                    </li>
                    
                    <!-- Teknisyenler -->
                    <li class="mb-2">
                        <a href="<?= BASE_URL ?>/technicians.php" class="flex items-center px-4 py-2 hover:bg-gray-700 rounded">
                            <i class="fas fa-user-cog mr-3"></i>
                            Teknisyenler
                        </a>
                    </li>
                    
                    <!-- Ayarlar -->
                    <li class="mb-2">
                        <a href="<?= BASE_URL ?>/settings.php" class="flex items-center px-4 py-2 hover:bg-gray-700 rounded">
                            <i class="fas fa-cogs mr-3"></i>
                            Ayarlar
                        </a>
                    </li>
                    
                    <!-- Raporlar -->
                    <li class="mb-2">
                        <a href="<?= BASE_URL ?>/reports.php" class="flex items-center px-4 py-2 hover:bg-gray-700 rounded">
                            <i class="fas fa-chart-bar mr-3"></i>
                            Raporlar
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navigation -->
            <header class="bg-white border-b border-gray-200 shadow-sm flex justify-between items-center py-3 px-6">
                <!-- Mobile menu button -->
                <button id="mobile-menu-button" class="md:hidden text-gray-600 hover:text-gray-900 focus:outline-none">
                    <i class="fas fa-bars"></i>
                </button>
                
                <!-- Search Form -->
                <div class="hidden md:block w-1/3">
                    <form action="<?= BASE_URL ?>/tickets.php" method="GET" class="relative">
                        <input type="text" name="search" placeholder="Ticket ara..." class="w-full bg-gray-100 border border-gray-300 rounded-lg py-2 px-4 pl-10 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <span class="absolute left-3 top-2 text-gray-400">
                            <i class="fas fa-search"></i>
                        </span>
                    </form>
                </div>
                
                <!-- User Menu -->
                <div class="flex items-center">
                    <div class="relative">
                        <button id="user-menu-button" class="flex items-center focus:outline-none">
                            <span class="hidden md:block mr-2 text-sm text-gray-700">
                                <?= AuthHelper::getUsername() ?>
                            </span>
                            <img src="https://ui-avatars.com/api/?name=<?= urlencode(AuthHelper::getUsername()) ?>&background=3498db&color=fff" alt="User Avatar" class="h-8 w-8 rounded-full">
                        </button>
                        
                        <!-- User Dropdown -->
                        <div id="user-menu" class="hidden absolute right-0 w-48 mt-2 py-2 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
                            <a href="<?= BASE_URL ?>/profile.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i> Profil
                            </a>
                            <a href="<?= BASE_URL ?>/tickets.php?my=1" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-ticket-alt mr-2"></i> Ticketlarım
                            </a>
                            <hr class="my-1 border-gray-200">
                            <a href="<?= BASE_URL ?>/logout.php" class="block px-4 py-2 text-red-600 hover:bg-gray-100">
                                <i class="fas fa-sign-out-alt mr-2"></i> Çıkış
                            </a>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Mobile Menu (hidden by default) -->
            <div id="mobile-menu" class="hidden md:hidden bg-gray-800 text-white w-full">
                <nav class="py-2">
                    <ul>
                        <li>
                            <a href="<?= BASE_URL ?>/index.php" class="block px-4 py-2 hover:bg-gray-700">
                                <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="<?= BASE_URL ?>/tickets.php" class="block px-4 py-2 hover:bg-gray-700">
                                <i class="fas fa-ticket-alt mr-2"></i> Tickets
                            </a>
                        </li>
                        <li>
                            <a href="<?= BASE_URL ?>/ticket/create.php" class="block px-4 py-2 hover:bg-gray-700">
                                <i class="fas fa-plus-circle mr-2"></i> Yeni Ticket
                            </a>
                        </li>
                        
                        <?php if (AuthHelper::isAdmin()): ?>
                        <li class="mt-3 mb-1">
                            <h3 class="px-4 text-xs text-gray-400 uppercase tracking-wider">
                                Yönetim
                            </h3>
                        </li>
                        <li>
                            <a href="<?= BASE_URL ?>/users.php" class="block px-4 py-2 hover:bg-gray-700">
                                <i class="fas fa-users mr-2"></i> Kullanıcılar
                            </a>
                        </li>
                        <li>
                            <a href="<?= BASE_URL ?>/technicians.php" class="block px-4 py-2 hover:bg-gray-700">
                                <i class="fas fa-user-cog mr-2"></i> Teknisyenler
                            </a>
                        </li>
                        <li>
                            <a href="<?= BASE_URL ?>/settings.php" class="block px-4 py-2 hover:bg-gray-700">
                                <i class="fas fa-cogs mr-2"></i> Ayarlar
                            </a>
                        </li>
                        <li>
                            <a href="<?= BASE_URL ?>/reports.php" class="block px-4 py-2 hover:bg-gray-700">
                                <i class="fas fa-chart-bar mr-2"></i> Raporlar
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
            
            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                <!-- Bildirimler - Ticket detay sayfasında gösterilmesin -->
                <?php 
                // URL yolunu kontrol et - hem ticket/view.php hem de views/tickets/view.php kontrol edilmeli
                $isTicketViewPage = strpos($_SERVER['PHP_SELF'], 'ticket/view.php') !== false || 
                                   strpos($_SERVER['PHP_SELF'], 'views/tickets/view.php') !== false;
                
                // Ticket görüntüleme sayfasındaysa bildirimleri hiç gösterme
                if (isset($messages) && !empty($messages) && !$isTicketViewPage): 
                ?>
                    <?php foreach ($messages as $key => $message): ?>
                        <div class="mb-4 p-4 rounded-lg <?= getAlertClass($message['type']) ?>">
                            <div class="flex items-center">
                                <span class="mr-2"><?= getAlertIcon($message['type']) ?></span>
                                <p><?= $message['message'] ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <?= $content ?>
            </main>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.20/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?= ASSETS_URL ?>/js/app.js"></script>
    
    <?php if (isset($extraScripts)): ?>
        <?= $extraScripts ?>
    <?php endif; ?>
    
    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
        
        // User menu toggle
        document.getElementById('user-menu-button').addEventListener('click', function() {
            document.getElementById('user-menu').classList.toggle('hidden');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const userMenu = document.getElementById('user-menu');
            const userMenuButton = document.getElementById('user-menu-button');
            
            if (!userMenuButton.contains(event.target) && !userMenu.contains(event.target)) {
                userMenu.classList.add('hidden');
            }
        });
    </script>
</body>
</html>

<?php
// Helper fonksiyonlar
function getAlertClass($type) {
    switch ($type) {
        case 'success':
            return 'bg-green-100 text-green-800 border border-green-200';
        case 'error':
            return 'bg-red-100 text-red-800 border border-red-200';
        case 'warning':
            return 'bg-yellow-100 text-yellow-800 border border-yellow-200';
        case 'info':
        default:
            return 'bg-blue-100 text-blue-800 border border-blue-200';
    }
}

function getAlertIcon($type) {
    switch ($type) {
        case 'success':
            return '<i class="fas fa-check-circle"></i>';
        case 'error':
            return '<i class="fas fa-exclamation-circle"></i>';
        case 'warning':
            return '<i class="fas fa-exclamation-triangle"></i>';
        case 'info':
        default:
            return '<i class="fas fa-info-circle"></i>';
    }
}
?>
