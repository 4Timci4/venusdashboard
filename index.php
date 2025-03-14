<?php
/**
 * Venus IT Help Desk
 * Ana giriş noktası ve router
 */

// Temel yapılandırma dosyasını yükle
require_once 'config/config.php';

// Yardımcı sınıfları yükle
require_once 'helpers/AuthHelper.php';
require_once 'helpers/ValidationHelper.php';
require_once 'helpers/FileHelper.php';

// Kontrolcüleri yükle
require_once 'controllers/DashboardController.php';
require_once 'controllers/AuthController.php';
require_once 'controllers/TicketController.php';

// Oturum başlat (eğer daha önce başlatılmamışsa)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Login process dosyasını doğrudan çağırma (özel durum)
if ($_SERVER['REQUEST_URI'] === '/login_process.php' || $_SERVER['REQUEST_URI'] === '/venus_it_desk/login_process.php') {
    include 'login_process.php';
    exit;
}

// Kullanıcı giriş kontrolü - login sayfası hariç tüm sayfalar için gerekli
$page = isset($_GET['page']) ? $_GET['page'] : '';

// Eğer kullanıcı giriş yapmamışsa ve login sayfasında değilse login sayfasına yönlendir
if ($page !== 'login' && !isset($_SESSION['user_id'])) {
    // AuthController'ı kullanarak login sayfasını göster
    $controller = new AuthController();
    $controller->showLoginForm();
    exit;
}

// URL işleme ve yönlendirme
$route = isset($_GET['route']) ? $_GET['route'] : 'dashboard';

// Rota bazlı kontrolcü yönlendirmesi
switch ($route) {
    // Ana sayfa / Dashboard rotaları
    case 'dashboard':
        $controller = new DashboardController();
        $controller->index();
        break;
    
    case 'dashboard/user':
        $controller = new DashboardController();
        $controller->userDashboard();
        break;
    
    case 'dashboard/technician':
        $controller = new DashboardController();
        $controller->technicianDashboard();
        break;
    
    case 'dashboard/reports':
        $controller = new DashboardController();
        $controller->reports();
        break;
    
    // Kimlik doğrulama rotaları
    case 'login':
        $controller = new AuthController();
        $controller->showLoginForm();
        break;
    
    case 'login_process':
        $controller = new AuthController();
        $controller->login();
        break;
    
    case 'logout':
        $controller = new AuthController();
        $controller->logout();
        break;
    
    case 'profile':
        $controller = new AuthController();
        $controller->showProfile();
        break;
    
    case 'profile_update':
        $controller = new AuthController();
        $controller->updateProfile();
        break;
    
    case 'forgot_password':
        $controller = new AuthController();
        $controller->showForgotPasswordForm();
        break;
    
    case 'forgot_password_process':
        $controller = new AuthController();
        $controller->forgotPassword();
        break;
    
    // Ticket rotaları
    case 'tickets':
        $controller = new TicketController();
        $controller->index();
        break;
    
    case 'ticket/create':
        $controller = new TicketController();
        $controller->create();
        break;
    
    case 'ticket/store':
        $controller = new TicketController();
        $controller->store();
        break;
    
    case 'ticket/view':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $controller = new TicketController();
        $controller->viewTicket($id);
        break;
    
    case 'ticket/edit':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $controller = new TicketController();
        $controller->edit($id);
        break;
    
    case 'ticket/update':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $controller = new TicketController();
        $controller->update($id);
        break;
    
    case 'ticket/comment':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $controller = new TicketController();
        $controller->addComment($id);
        break;
    
    case 'ticket/close':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $controller = new TicketController();
        $controller->close($id);
        break;
    
    case 'ticket/attachment':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $controller = new TicketController();
        $controller->addAttachment($id);
        break;
    
    // API rotaları
    case 'api/tickets/subcategories':
        $categoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
        $controller = new TicketController();
        $controller->getSubcategories($categoryId);
        break;
    
    case 'api/tickets/status':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $controller = new TicketController();
        $controller->updateStatus($id);
        break;
    
    case 'api/tickets/assign':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $controller = new TicketController();
        $controller->assignTechnician($id);
        break;
    
    case 'api/auth/login':
        $controller = new AuthController();
        $controller->ajaxLogin();
        break;
    
    // Varsayılan sayfa - 404 hatası
    default:
        header("HTTP/1.0 404 Not Found");
        echo '<h1>404 - Sayfa Bulunamadı</h1>';
        echo '<p>İstediğiniz sayfa bulunamadı. <a href="'.BASE_URL.'">Ana Sayfa</a>\'ya dönün.</p>';
        break;
}
