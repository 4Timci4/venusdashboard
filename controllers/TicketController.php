<?php
/**
 * Venus IT Help Desk - Ticket Kontrolcüsü
 * 
 * Ticket yönetimi işlemleri: oluşturma, listeleme, detay görüntüleme, güncelleme
 */

require_once 'Controller.php';
require_once BASE_PATH . '/models/Ticket.php';
require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/helpers/AuthHelper.php';
require_once BASE_PATH . '/helpers/ValidationHelper.php';
require_once BASE_PATH . '/helpers/FileHelper.php';

class TicketController extends Controller {
    private $ticketModel;
    private $userModel;
    
    public function __construct() {
        $this->ticketModel = new Ticket();
        $this->userModel = new User();
    }
    
    /**
     * Ticket listesini gösterir
     */
    public function index() {
        // Giriş kontrolü
        AuthHelper::requireLogin();
        
        // Sayfalama için parametreleri al
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;
        
        // Arama ve filtreleme parametrelerini al
        $search = isset($_GET['search']) ? trim($_GET['search']) : null;
        $filters = [];
        
        $filterFields = ['status_id', 'request_type_id', 'service_id', 'technician_id', 'category_id', 'priority', 'date_from', 'date_to'];
        
        foreach ($filterFields as $field) {
            if (isset($_GET[$field]) && !empty($_GET[$field])) {
                $filters[$field] = $_GET[$field];
            }
        }
        
        // Kullanıcı rolüne göre ticketları getir
        if (AuthHelper::isAdmin() || AuthHelper::isTechnician()) {
            // Yönetici veya teknisyen tüm ticketları görebilir
            $tickets = $this->ticketModel->search($search, $filters, 'created_at DESC', $limit, $offset);
            $totalCount = $this->ticketModel->count();
        } else {
            // Normal kullanıcı sadece kendi ticketlarını görebilir
            $filters['user_id'] = AuthHelper::getUserId();
            $tickets = $this->ticketModel->search($search, $filters, 'created_at DESC', $limit, $offset);
            $totalCount = $this->ticketModel->count("user_id = :user_id", [':user_id' => AuthHelper::getUserId()]);
        }
        
        // Toplam sayfa sayısını hesapla
        $totalPages = ceil($totalCount / $limit);
        
        // Gerekli model verilerini al
        $statusModel = new StatusType();
        $requestTypeModel = new RequestType();
        $serviceModel = new Service();
        $categoryModel = new ServiceCategory();
        
        $statuses = $statusModel->getAll();
        $requestTypes = $requestTypeModel->getAll();
        $services = $serviceModel->getAll();
        $categories = $categoryModel->getAll();
        $technicians = $this->userModel->getTechnicians();
        
        // View'a veri gönder
        $this->view('tickets/list', [
            'tickets' => $tickets,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'search' => $search,
            'filters' => $filters,
            'statuses' => $statuses,
            'requestTypes' => $requestTypes,
            'services' => $services,
            'categories' => $categories,
            'technicians' => $technicians,
            'layout' => 'main',
            'messages' => $this->getAllFlashMessages()
        ]);
    }
    
    /**
     * Yeni ticket oluşturma formunu gösterir
     */
    public function create() {
        // Giriş kontrolü
        AuthHelper::requireLogin();
        
        // Gerekli model verilerini al
        $userModel = new User();
        $statusModel = new StatusType();
        $requestTypeModel = new RequestType();
        $serviceModel = new Service();
        $categoryModel = new ServiceCategory();
        
        $users = $userModel->getAll();
        $statuses = $statusModel->getAll();
        $requestTypes = $requestTypeModel->getAll();
        $services = $serviceModel->getAll();
        $categories = $categoryModel->getAll();
        $technicians = $userModel->getTechnicians();
        
        // View'a veri gönder
        $this->view('tickets/create', [
            'users' => $users,
            'statuses' => $statuses,
            'requestTypes' => $requestTypes,
            'services' => $services,
            'categories' => $categories,
            'technicians' => $technicians,
            'csrf_token' => $this->getCsrfToken(),
            'layout' => 'main',
            'errors' => $this->getFlashMessage('ticket_errors')
        ]);
    }
    
    /**
     * Yeni ticket oluşturur
     */
    public function store() {
        // Giriş kontrolü
        AuthHelper::requireLogin();
        
        // POST metoduyla gelmemişse ticket oluşturma sayfasına yönlendir
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . '/ticket/create.php');
            return;
        }
        
        // CSRF token kontrolü
        if (!isset($_POST['csrf_token']) || !$this->validateCsrfToken($_POST['csrf_token'])) {
            $this->setFlashMessage('ticket_errors', ['csrf' => 'Güvenlik doğrulaması başarısız oldu. Lütfen tekrar deneyin.']);
            $this->redirect(BASE_URL . '/ticket/create.php');
            return;
        }
        
        // Form verilerini al
        $data = $this->getFormData([
            'user_id', 'request_type_id', 'status_id', 'service_id', 'technician_id',
            'category_id', 'subcategory_id', 'subject', 'description', 'impact',
            'impact_details', 'activity', 'priority'
        ]);
        
        // Eğer kullanıcı belirtilmemişse, mevcut kullanıcıyı kullan
        if (empty($data['user_id'])) {
            $data['user_id'] = AuthHelper::getUserId();
        }
        
        // Eğer durum belirtilmemişse, varsayılan olarak "Açık" durumunu kullan
        if (empty($data['status_id'])) {
            $data['status_id'] = 1; // Açık durumu
        }
        
        // Form doğrulama
        $errors = ValidationHelper::validate($data, [
            'user_id' => ['required' => true, 'integer' => true],
            'subject' => ['required' => true, 'maxLength' => 255],
            'description' => ['required' => true]
        ]);
        
        if (!empty($errors)) {
            $this->setFlashMessage('ticket_errors', $errors);
            $this->redirect(BASE_URL . '/ticket/create.php');
            return;
        }
        
        // Ticket oluştur
        $ticketId = $this->ticketModel->create($data);
        
        if (!$ticketId) {
            $this->setFlashMessage('error', 'Ticket oluşturulurken bir hata oluştu.', 'error');
            $this->redirect(BASE_URL . '/ticket/create.php');
            return;
        }
        
        // Dosya yükleme kontrolü
        if (isset($_FILES['attachments']) && !empty($_FILES['attachments']['name'][0])) {
            $uploadDir = UPLOADS_PATH . '/tickets/' . $ticketId;
            $uploadedFiles = FileHelper::uploadMultipleFiles($_FILES['attachments'], $uploadDir, 'ticket_');
            
            if (!empty($uploadedFiles)) {
                // Dosya bilgilerini veritabanına kaydet
                $attachmentModel = new TicketAttachment();
                
                foreach ($uploadedFiles as $file) {
                    $attachmentData = [
                        'ticket_id' => $ticketId,
                        'file_name' => $file['name'],
                        'file_path' => $file['path'],
                        'file_type' => $file['type'],
                        'file_size' => $file['size']
                    ];
                    
                    $attachmentModel->create($attachmentData);
                }
            }
        }
        
        // Aktivite logu oluştur
        $logModel = new ActivityLog();
        $logData = [
            'user_id' => AuthHelper::getUserId(),
            'ticket_id' => $ticketId,
            'action' => 'create_ticket',
            'details' => 'Ticket oluşturuldu'
        ];
        
        $logModel->create($logData);
        
        // Başarılı mesajı göster ve ticket detay sayfasına yönlendir
        $this->setFlashMessage('success', 'Ticket başarıyla oluşturuldu.', 'success');
        $this->redirect(BASE_URL . '/ticket/view.php?id=' . $ticketId);
    }
    
    /**
     * Ticket detaylarını gösterir
     */
    public function viewTicket($id) {
        // Giriş kontrolü
        AuthHelper::requireLogin();
        
        // Ticket detaylarını al
        $ticket = $this->ticketModel->getWithDetails($id);
        
        if (!$ticket) {
            $this->setFlashMessage('error', 'Ticket bulunamadı.', 'error');
            $this->redirect(BASE_URL . '/tickets.php');
            return;
        }
        
        // Kullanıcı kontrolü (admin ve teknisyen hariç)
        if (!AuthHelper::isAdmin() && !AuthHelper::isTechnician() && $ticket['user_id'] != AuthHelper::getUserId()) {
            $this->setFlashMessage('error', 'Bu ticketı görüntüleme yetkiniz yok.', 'error');
            $this->redirect(BASE_URL . '/tickets.php');
            return;
        }
        
        // Ek bilgileri al
        $attachments = $this->ticketModel->getAttachments($id);
        $comments = $this->ticketModel->getComments($id);
        
        // Durum bilgilerini al
        $statusModel = new StatusType();
        $statuses = $statusModel->getAll();
        
        // Teknisyen bilgilerini al
        $technicians = $this->userModel->getTechnicians();
        
        // View'a veri gönder
        $this->view('tickets/view', [
            'ticket' => $ticket,
            'attachments' => $attachments,
            'comments' => $comments,
            'statuses' => $statuses,
            'technicians' => $technicians,
            'csrf_token' => $this->getCsrfToken(),
            'layout' => 'main',
            'messages' => $this->getAllFlashMessages()
        ]);
    }
    
    /**
     * Ticket düzenleme formunu gösterir
     */
    public function edit($id) {
        // Giriş kontrolü
        AuthHelper::requireLogin();
        
        // Admin ve teknisyen kontrolü
        if (!AuthHelper::isAdmin() && !AuthHelper::isTechnician()) {
            $this->setFlashMessage('error', 'Ticket düzenleme yetkiniz yok.', 'error');
            $this->redirect(BASE_URL . '/tickets.php');
            return;
        }
        
        // Ticket verilerini al
        $ticket = $this->ticketModel->getById($id);
        
        if (!$ticket) {
            $this->setFlashMessage('error', 'Ticket bulunamadı.', 'error');
            $this->redirect(BASE_URL . '/tickets.php');
            return;
        }
        
        // Gerekli model verilerini al
        $userModel = new User();
        $statusModel = new StatusType();
        $requestTypeModel = new RequestType();
        $serviceModel = new Service();
        $categoryModel = new ServiceCategory();
        $subcategoryModel = new SubCategory();
        
        $users = $userModel->getAll();
        $statuses = $statusModel->getAll();
        $requestTypes = $requestTypeModel->getAll();
        $services = $serviceModel->getAll();
        $categories = $categoryModel->getAll();
        $technicians = $userModel->getTechnicians();
        
        // Eğer kategori seçilmişse alt kategorileri getir
        $subcategories = [];
        if (!empty($ticket['category_id'])) {
            $subcategories = $subcategoryModel->getByCategoryId($ticket['category_id']);
        }
        
        // View'a veri gönder
        $this->view('tickets/edit', [
            'ticket' => $ticket,
            'users' => $users,
            'statuses' => $statuses,
            'requestTypes' => $requestTypes,
            'services' => $services,
            'categories' => $categories,
            'subcategories' => $subcategories,
            'technicians' => $technicians,
            'csrf_token' => $this->getCsrfToken(),
            'layout' => 'main',
            'errors' => $this->getFlashMessage('ticket_errors')
        ]);
    }
    
    /**
     * Ticket bilgilerini günceller
     */
    public function update($id) {
        // Giriş kontrolü
        AuthHelper::requireLogin();
        
        // Admin ve teknisyen kontrolü
        if (!AuthHelper::isAdmin() && !AuthHelper::isTechnician()) {
            $this->setFlashMessage('error', 'Ticket düzenleme yetkiniz yok.', 'error');
            $this->redirect(BASE_URL . '/tickets.php');
            return;
        }
        
        // POST metoduyla gelmemişse ticket sayfasına yönlendir
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . '/ticket/edit.php?id=' . $id);
            return;
        }
        
        // CSRF token kontrolü
        if (!isset($_POST['csrf_token']) || !$this->validateCsrfToken($_POST['csrf_token'])) {
            $this->setFlashMessage('ticket_errors', ['csrf' => 'Güvenlik doğrulaması başarısız oldu. Lütfen tekrar deneyin.']);
            $this->redirect(BASE_URL . '/ticket/edit.php?id=' . $id);
            return;
        }
        
        // Ticket verilerini al
        $ticket = $this->ticketModel->getById($id);
        
        if (!$ticket) {
            $this->setFlashMessage('error', 'Ticket bulunamadı.', 'error');
            $this->redirect(BASE_URL . '/tickets.php');
            return;
        }
        
        // Form verilerini al
        $data = $this->getFormData([
            'user_id', 'request_type_id', 'status_id', 'service_id', 'technician_id',
            'category_id', 'subcategory_id', 'subject', 'description', 'impact',
            'impact_details', 'activity', 'priority'
        ]);
        
        // Form doğrulama
        $errors = ValidationHelper::validate($data, [
            'user_id' => ['required' => true, 'integer' => true],
            'subject' => ['required' => true, 'maxLength' => 255],
            'description' => ['required' => true]
        ]);
        
        if (!empty($errors)) {
            $this->setFlashMessage('ticket_errors', $errors);
            $this->redirect(BASE_URL . '/ticket/edit.php?id=' . $id);
            return;
        }
        
        // Durumu değişmişse, eski durumu kaydet
        $oldStatus = $ticket['status_id'];
        $newStatus = $data['status_id'];
        
        // Teknisyen değişmişse, eski teknisyeni kaydet
        $oldTechnician = $ticket['technician_id'];
        $newTechnician = $data['technician_id'];
        
        // Ticket güncelle
        $result = $this->ticketModel->update($id, $data);
        
        if (!$result) {
            $this->setFlashMessage('error', 'Ticket güncellenirken bir hata oluştu.', 'error');
            $this->redirect(BASE_URL . '/ticket/edit.php?id=' . $id);
            return;
        }
        
        // Dosya yükleme kontrolü
        if (isset($_FILES['attachments']) && !empty($_FILES['attachments']['name'][0])) {
            $uploadDir = UPLOADS_PATH . '/tickets/' . $id;
            $uploadedFiles = FileHelper::uploadMultipleFiles($_FILES['attachments'], $uploadDir, 'ticket_');
            
            if (!empty($uploadedFiles)) {
                // Dosya bilgilerini veritabanına kaydet
                $attachmentModel = new TicketAttachment();
                
                foreach ($uploadedFiles as $file) {
                    $attachmentData = [
                        'ticket_id' => $id,
                        'file_name' => $file['name'],
                        'file_path' => $file['path'],
                        'file_type' => $file['type'],
                        'file_size' => $file['size']
                    ];
                    
                    $attachmentModel->create($attachmentData);
                }
            }
        }
        
        // Aktivite logu oluştur
        $logModel = new ActivityLog();
        $userId = AuthHelper::getUserId();
        
        // Güncelleme logu
        $logData = [
            'user_id' => $userId,
            'ticket_id' => $id,
            'action' => 'update_ticket',
            'details' => 'Ticket güncellendi'
        ];
        
        $logModel->create($logData);
        
        // Durum değişikliği logu
        if ($oldStatus != $newStatus) {
            $statusModel = new StatusType();
            $status = $statusModel->getById($newStatus);
            
            $logData = [
                'user_id' => $userId,
                'ticket_id' => $id,
                'action' => 'status_update',
                'details' => "Durum '" . $status['name'] . "' olarak güncellendi"
            ];
            
            $logModel->create($logData);
        }
        
        // Teknisyen değişikliği logu
        if ($oldTechnician != $newTechnician && !empty($newTechnician)) {
            $techModel = new Technician();
            $technician = $techModel->getById($newTechnician);
            
            if ($technician) {
                $techUser = $this->userModel->getById($technician['user_id']);
                
                $logData = [
                    'user_id' => $userId,
                    'ticket_id' => $id,
                    'action' => 'assign_technician',
                    'details' => "Ticket '" . $techUser['full_name'] . "' adlı teknisyene atandı"
                ];
                
                $logModel->create($logData);
            }
        }
        
        // Başarılı mesajı göster ve ticket detay sayfasına yönlendir
        $this->setFlashMessage('success', 'Ticket başarıyla güncellendi.', 'success');
        $this->redirect(BASE_URL . '/ticket/view.php?id=' . $id);
    }
    
    /**
     * Ticket durumunu günceller
     */
    public function updateStatus($id) {
        // Giriş kontrolü
        AuthHelper::requireLogin();
        
        // Admin ve teknisyen kontrolü
        if (!AuthHelper::isAdmin() && !AuthHelper::isTechnician()) {
            $this->json(['success' => false, 'message' => 'Durum güncelleme yetkiniz yok.'], 403);
            return;
        }
        
        // Sadece POST ve AJAX istekleri kabul edilir
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
            $this->json(['success' => false, 'message' => 'Geçersiz istek.'], 400);
            return;
        }
        
        // CSRF token kontrolü
        if (!isset($_POST['csrf_token']) || !$this->validateCsrfToken($_POST['csrf_token'])) {
            $this->json(['success' => false, 'message' => 'Güvenlik doğrulaması başarısız oldu.'], 403);
            return;
        }
        
        // Form verilerini al
        $data = $this->getFormData(['status_id']);
        
        // Form doğrulama
        $errors = ValidationHelper::validate($data, [
            'status_id' => ['required' => true, 'integer' => true]
        ]);
        
        if (!empty($errors)) {
            $this->json(['success' => false, 'message' => 'Geçersiz durum seçildi.'], 400);
            return;
        }
        
        // Durum güncelle
        $result = $this->ticketModel->updateStatus($id, $data['status_id'], AuthHelper::getUserId());
        
        if ($result) {
            // Başarılı
            $statusModel = new StatusType();
            $status = $statusModel->getById($data['status_id']);
            
            $this->json([
                'success' => true,
                'message' => 'Durum başarıyla güncellendi.',
                'status' => $status
            ]);
        } else {
            // Başarısız
            $this->json(['success' => false, 'message' => 'Durum güncellenirken bir hata oluştu.'], 500);
        }
    }
    
    /**
     * Ticket'a teknisyen atar
     */
    public function assignTechnician($id) {
        // Giriş kontrolü
        AuthHelper::requireLogin();
        
        // Admin ve teknisyen kontrolü
        if (!AuthHelper::isAdmin() && !AuthHelper::isTechnician()) {
            $this->json(['success' => false, 'message' => 'Teknisyen atama yetkiniz yok.'], 403);
            return;
        }
        
        // Sadece POST ve AJAX istekleri kabul edilir
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
            $this->json(['success' => false, 'message' => 'Geçersiz istek.'], 400);
            return;
        }
        
        // CSRF token kontrolü
        if (!isset($_POST['csrf_token']) || !$this->validateCsrfToken($_POST['csrf_token'])) {
            $this->json(['success' => false, 'message' => 'Güvenlik doğrulaması başarısız oldu.'], 403);
            return;
        }
        
        // Form verilerini al
        $data = $this->getFormData(['technician_id']);
        
        // Form doğrulama
        $errors = ValidationHelper::validate($data, [
            'technician_id' => ['required' => true, 'integer' => true]
        ]);
        
        if (!empty($errors)) {
            $this->json(['success' => false, 'message' => 'Geçersiz teknisyen seçildi.'], 400);
            return;
        }
        
        // Teknisyen ata
        $result = $this->ticketModel->assignToTechnician($id, $data['technician_id'], AuthHelper::getUserId());
        
        if ($result) {
            // Başarılı
            $techModel = new Technician();
            $technician = $techModel->getById($data['technician_id']);
            $techUser = $this->userModel->getById($technician['user_id']);
            
            $this->json([
                'success' => true,
                'message' => 'Teknisyen başarıyla atandı.',
                'technician' => $techUser
            ]);
        } else {
            // Başarısız
            $this->json(['success' => false, 'message' => 'Teknisyen atanırken bir hata oluştu.'], 500);
        }
    }
    
    /**
     * Ticket'a yorum ekler
     */
    public function addComment($id) {
        // Giriş kontrolü
        AuthHelper::requireLogin();
        
        // POST metoduyla gelmemişse ticket sayfasına yönlendir
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . '/ticket/view.php?id=' . $id);
            return;
        }
        
        // CSRF token kontrolü
        if (!isset($_POST['csrf_token']) || !$this->validateCsrfToken($_POST['csrf_token'])) {
            $this->setFlashMessage('error', 'Güvenlik doğrulaması başarısız oldu. Lütfen tekrar deneyin.', 'error');
            $this->redirect(BASE_URL . '/ticket/view.php?id=' . $id);
            return;
        }
        
        // Form verilerini al
        $data = $this->getFormData(['comment']);
        
        // Form doğrulama
        $errors = ValidationHelper::validate($data, [
            'comment' => ['required' => true]
        ]);
        
        if (!empty($errors)) {
            $this->setFlashMessage('error', 'Yorum alanı boş olamaz.', 'error');
            $this->redirect(BASE_URL . '/ticket/view.php?id=' . $id);
            return;
        }
        
        // Yorum ekle
        $commentModel = new TicketComment();
        $commentData = [
            'ticket_id' => $id,
            'user_id' => AuthHelper::getUserId(),
            'comment' => $data['comment']
        ];
        
        $result = $commentModel->create($commentData);
        
        if (!$result) {
            $this->setFlashMessage('error', 'Yorum eklenirken bir hata oluştu.', 'error');
            $this->redirect(BASE_URL . '/ticket/view.php?id=' . $id);
            return;
        }
        
        // Aktivite logu oluştur
        $logModel = new ActivityLog();
        $logData = [
            'user_id' => AuthHelper::getUserId(),
            'ticket_id' => $id,
            'action' => 'add_comment',
            'details' => 'Yorum eklendi'
        ];
        
        $logModel->create($logData);
        
        // Başarılı mesajı göster ve ticket detay sayfasına yönlendir
        $this->setFlashMessage('success', 'Yorum başarıyla eklendi.', 'success');
        $this->redirect(BASE_URL . '/ticket/view.php?id=' . $id);
    }
    
    /**
     * Ticket'ı kapatır
     */
    public function close($id) {
        // Giriş kontrolü
        AuthHelper::requireLogin();
        
        // POST metoduyla gelmemişse ticket sayfasına yönlendir
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . '/ticket/view.php?id=' . $id);
            return;
        }
        
        // CSRF token kontrolü
        if (!isset($_POST['csrf_token']) || !$this->validateCsrfToken($_POST['csrf_token'])) {
            $this->setFlashMessage('error', 'Güvenlik doğrulaması başarısız oldu. Lütfen tekrar deneyin.', 'error');
            $this->redirect(BASE_URL . '/ticket/view.php?id=' . $id);
            return;
        }
        
        // Ticket'ı kapat
        $result = $this->ticketModel->closeTicket($id, AuthHelper::getUserId());
        
        if (!$result) {
            $this->setFlashMessage('error', 'Ticket kapatılırken bir hata oluştu.', 'error');
            $this->redirect(BASE_URL . '/ticket/view.php?id=' . $id);
            return;
        }
        
        // Başarılı mesajı göster ve ticket detay sayfasına yönlendir
        $this->setFlashMessage('success', 'Ticket başarıyla kapatıldı.', 'success');
        $this->redirect(BASE_URL . '/ticket/view.php?id=' . $id);
    }
    
    /**
     * Ticket'a dosya ekler
     */
    public function addAttachment($id) {
        // Giriş kontrolü
        AuthHelper::requireLogin();
        
        // POST metoduyla gelmemişse ticket sayfasına yönlendir
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . '/ticket/view.php?id=' . $id);
            return;
        }
        
        // CSRF token kontrolü
        if (!isset($_POST['csrf_token']) || !$this->validateCsrfToken($_POST['csrf_token'])) {
            $this->setFlashMessage('error', 'Güvenlik doğrulaması başarısız oldu. Lütfen tekrar deneyin.', 'error');
            $this->redirect(BASE_URL . '/ticket/view.php?id=' . $id);
            return;
        }
        
        // Dosya yükleme kontrolü
        if (!isset($_FILES['attachments']) || empty($_FILES['attachments']['name'][0])) {
            $this->setFlashMessage('error', 'Lütfen bir dosya seçin.', 'error');
            $this->redirect(BASE_URL . '/ticket/view.php?id=' . $id);
            return;
        }
        
        // Dosyaları yükle
        $uploadDir = UPLOADS_PATH . '/tickets/' . $id;
        $uploadedFiles = FileHelper::uploadMultipleFiles($_FILES['attachments'], $uploadDir, 'ticket_');
        
        if (empty($uploadedFiles)) {
            $this->setFlashMessage('error', 'Dosya yüklenirken bir hata oluştu. Lütfen izin verilen uzantıları ve dosya boyutunu kontrol edin.', 'error');
            $this->redirect(BASE_URL . '/ticket/view.php?id=' . $id);
            return;
        }
        
        // Dosya bilgilerini veritabanına kaydet
        $attachmentModel = new TicketAttachment();
        $saveCount = 0;
        
        foreach ($uploadedFiles as $file) {
            $attachmentData = [
                'ticket_id' => $id,
                'file_name' => $file['name'],
                'file_path' => $file['path'],
                'file_type' => $file['type'],
                'file_size' => $file['size']
            ];
            
            if ($attachmentModel->create($attachmentData)) {
                $saveCount++;
            }
        }
        
        if ($saveCount > 0) {
            // Aktivite logu oluştur
            $logModel = new ActivityLog();
            $logData = [
                'user_id' => AuthHelper::getUserId(),
                'ticket_id' => $id,
                'action' => 'add_attachment',
                'details' => $saveCount . ' adet dosya eklendi'
            ];
            
            $logModel->create($logData);
            
            $this->setFlashMessage('success', $saveCount . ' adet dosya başarıyla yüklendi.', 'success');
        } else {
            $this->setFlashMessage('error', 'Dosya bilgileri kaydedilirken bir hata oluştu.', 'error');
        }
        
        $this->redirect(BASE_URL . '/ticket/view.php?id=' . $id);
    }
    
    /**
     * Alt kategorileri AJAX ile getirir
     */
    public function getSubcategories() {
        // Sadece AJAX istekleri kabul edilir
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
            $this->json(['success' => false, 'message' => 'Geçersiz istek.'], 400);
            return;
        }
        
        // Kategori ID'sini al
        $categoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
        
        if ($categoryId <= 0) {
            $this->json(['success' => false, 'message' => 'Geçersiz kategori ID.'], 400);
            return;
        }
        
        // Alt kategorileri getir
        $subcategoryModel = new SubCategory();
        $subcategories = $subcategoryModel->getByCategoryId($categoryId);
        
        $this->json([
            'success' => true,
            'subcategories' => $subcategories
        ]);
    }
}
