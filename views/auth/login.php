<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş - Venus IT Help Desk</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="<?= ASSETS_URL ?>/img/favicon.ico" type="image/x-icon">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.20/dist/sweetalert2.min.css">
    
    <style>
        .login-bg {
            background: linear-gradient(135deg, #3182ce 0%, #2c5282 100%);
        }
    </style>
</head>
<body class="bg-gray-100 font-sans h-screen">
    <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md">
            <!-- Logo ve başlık -->
            <div class="text-center mb-8">
                <div class="text-center mb-6">
                    <div class="inline-block bg-blue-600 rounded-full p-4 mb-4">
                        <i class="fas fa-laptop-medical text-5xl text-white"></i>
                    </div>
                    <h1 class="text-3xl font-extrabold text-gray-800">
                        Venus IT Help Desk
                    </h1>
                    <p class="mt-2 text-gray-600">
                        IT destek taleplerini yönetin ve takip edin
                    </p>
                </div>
            </div>
            
            <!-- Login kartı -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="login-bg px-6 py-8 text-white text-center">
                    <h2 class="text-2xl font-bold">Hoş Geldiniz</h2>
                    <p>Lütfen hesabınıza giriş yapın</p>
                </div>
                
                <div class="p-6">
                    <!-- Hata mesajı -->
                    <?php if (isset($error)): ?>
                        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                <p><?= $error['message'] ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Login formu -->
                    <form action="<?= BASE_URL ?>/login_process.php" method="POST" id="login-form">
                        <!-- CSRF token -->
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        
                        <!-- Kullanıcı adı -->
                        <div class="mb-4">
                            <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Kullanıcı Adı</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                                <input id="username" name="username" type="text" required autocomplete="username" 
                                       class="pl-10 w-full py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                        
                        <!-- Şifre -->
                        <div class="mb-6">
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Şifre</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input id="password" name="password" type="password" required autocomplete="current-password" 
                                       class="pl-10 w-full py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                        
                        <!-- Beni hatırla -->
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center">
                                <input id="remember_me" name="remember_me" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="remember_me" class="ml-2 block text-sm text-gray-700">
                                    Beni hatırla
                                </label>
                            </div>
                            
                            <div class="text-sm">
                                <a href="<?= BASE_URL ?>/forgot_password.php" class="text-blue-600 hover:text-blue-800">
                                    Şifremi unuttum
                                </a>
                            </div>
                        </div>
                        
                        <!-- Giriş butonu -->
                        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-sign-in-alt mr-2"></i> Giriş Yap
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Alt bilgi -->
            <div class="mt-6 text-center text-sm text-gray-600">
                <p>&copy; <?= date('Y') ?> Venus IT Help Desk. Tüm hakları saklıdır.</p>
            </div>
        </div>
    </div>
    
    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.20/dist/sweetalert2.all.min.js"></script>
    
    <script>
        // AJAX form gönderimi
        $(document).ready(function() {
            $('#login-form').on('submit', function(e) {
                e.preventDefault();
                
                $.ajax({
                    type: 'POST',
                    url: '<?= BASE_URL ?>/api/auth/login.php',
                    data: $(this).serialize(),
                    dataType: 'json',
                    beforeSend: function() {
                        // Buton durumunu güncelle
                        $('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Giriş yapılıyor...');
                    },
                    success: function(response) {
                        if (response.success) {
                            // Başarılı giriş
                            Swal.fire({
                                icon: 'success',
                                title: 'Giriş Başarılı!',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(function() {
                                window.location.href = response.redirect;
                            });
                        } else {
                            // Hata durumu
                            Swal.fire({
                                icon: 'error',
                                title: 'Giriş Başarısız',
                                text: response.message
                            });
                            
                            // Butonu eski haline getir
                            $('button[type="submit"]').prop('disabled', false).html('<i class="fas fa-sign-in-alt mr-2"></i> Giriş Yap');
                        }
                    },
                    error: function(xhr, status, error) {
                        // AJAX hata durumu
                        console.error(xhr.responseText);
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Bağlantı Hatası',
                            text: 'Sunucuya bağlanırken bir hata oluştu. Lütfen tekrar deneyin.'
                        });
                        
                        // Butonu eski haline getir
                        $('button[type="submit"]').prop('disabled', false).html('<i class="fas fa-sign-in-alt mr-2"></i> Giriş Yap');
                    }
                });
            });
        });
    </script>
</body>
</html>
