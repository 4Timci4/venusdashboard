<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol - Venus IT Help Desk</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="<?= ASSETS_URL ?>/img/favicon.ico" type="image/x-icon">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    
    <style>
        .register-bg {
            background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
        }
    </style>
</head>
<body class="bg-gray-100 font-sans h-screen">
    <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md">
            <!-- Logo ve başlık -->
            <div class="text-center mb-8">
                <div class="text-center mb-6">
                    <div class="inline-block bg-indigo-600 rounded-full p-4 mb-4">
                        <i class="fas fa-laptop-medical text-5xl text-white"></i>
                    </div>
                    <h1 class="text-3xl font-extrabold text-gray-800">
                        Venus IT Help Desk
                    </h1>
                    <p class="mt-2 text-gray-600">
                        IT destek talepleriniz için hesap oluşturun
                    </p>
                </div>
            </div>
            
            <!-- Kayıt kartı -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="register-bg px-6 py-8 text-white text-center">
                    <h2 class="text-2xl font-bold">Kayıt Ol</h2>
                    <p>Yeni bir hesap oluşturun</p>
                </div>
                
                <div class="p-6">
                    <!-- Hata mesajı -->
                    <?php if (isset($_SESSION['register_error'])): ?>
                        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                <p><?= $_SESSION['register_error'] ?></p>
                            </div>
                        </div>
                        <?php unset($_SESSION['register_error']); ?>
                    <?php endif; ?>
                    
                    <!-- Kayıt formu -->
                    <form action="<?= BASE_URL ?>/register_process.php" method="POST">
                        <!-- CSRF token -->
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        
                        <!-- Ad Soyad -->
                        <div class="mb-4">
                            <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Ad Soyad</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                                <input id="full_name" name="full_name" type="text" required 
                                       class="pl-10 w-full py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            </div>
                        </div>
                        
                        <!-- E-posta -->
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-posta</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-envelope text-gray-400"></i>
                                </div>
                                <input id="email" name="email" type="email" required 
                                       class="pl-10 w-full py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            </div>
                        </div>
                        
                        <!-- Kullanıcı adı -->
                        <div class="mb-4">
                            <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Kullanıcı Adı</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user-tag text-gray-400"></i>
                                </div>
                                <input id="username" name="username" type="text" required autocomplete="username" 
                                       class="pl-10 w-full py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            </div>
                        </div>
                        
                        <!-- Departman -->
                        <div class="mb-4">
                            <label for="department" class="block text-sm font-medium text-gray-700 mb-1">Departman</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-building text-gray-400"></i>
                                </div>
                                <input id="department" name="department" type="text" required 
                                       class="pl-10 w-full py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            </div>
                        </div>
                        
                        <!-- Şifre -->
                        <div class="mb-4">
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Şifre</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input id="password" name="password" type="password" required autocomplete="new-password" 
                                       class="pl-10 w-full py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">En az 8 karakter ve içinde harf, rakam ve özel karakter bulunmalıdır.</p>
                        </div>
                        
                        <!-- Şifre Tekrarı -->
                        <div class="mb-6">
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Şifre Tekrarı</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input id="confirm_password" name="confirm_password" type="password" required 
                                       class="pl-10 w-full py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            </div>
                        </div>
                        
                        <!-- Kayıt butonu -->
                        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-user-plus mr-2"></i> Kayıt Ol
                        </button>
                    </form>
                    
                    <!-- Giriş sayfasına dön -->
                    <div class="mt-6 text-center">
                        <a href="<?= BASE_URL ?>/login.php" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                            <i class="fas fa-arrow-left mr-1"></i> Giriş sayfasına dön
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Alt bilgi -->
            <div class="mt-6 text-center text-sm text-gray-600">
                <p>&copy; <?= date('Y') ?> Venus IT Help Desk. Tüm hakları saklıdır.</p>
            </div>
        </div>
    </div>
</body>
</html>
