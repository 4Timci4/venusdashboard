<?php
require_once __DIR__ . '/../../config/init.php';
require_once __DIR__ . '/../../models/Ticket.php';
require_once __DIR__ . '/../../helpers/AuthHelper.php';
require_once __DIR__ . '/../../helpers/ValidationHelper.php';

// Oturum kontrolü
if (!AuthHelper::isLoggedIn()) {
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}

// CSRF token kontrolü
if (!isset($_POST['csrf_token']) || !ValidationHelper::validateCSRFToken($_POST['csrf_token'])) {
    $_SESSION['error'] = 'Güvenlik doğrulaması başarısız oldu. Lütfen tekrar deneyin.';
    header('Location: ' . BASE_URL . '/ticket/create.php');
    exit;
}

// POST verilerini al
$ticketData = [
    'user_id' => $_POST['user_id'] ?? AuthHelper::getUserId(),
    'subject' => $_POST['subject'] ?? '',
    'description' => $_POST['description'] ?? '',
    'priority' => $_POST['priority'] ?? 1,
    'status_id' => $_POST['status_id'] ?? 1,
    'request_type_id' => $_POST['request_type_id'] ?? null,
    'service_id' => $_POST['service_id'] ?? null,
    'category_id' => $_POST['category_id'] ?? null,
    'subcategory_id' => $_POST['subcategory_id'] ?? null,
    'technician_id' => $_POST['technician_id'] ?? null,
    'impact' => $_POST['impact'] ?? null,
    'impact_details' => $_POST['impact_details'] ?? null,
    'activity' => $_POST['activity'] ?? null
];

// Validasyon kuralları
$rules = [
    'subject' => 'required|min:3|max:255',
    'description' => 'required|min:10',
    'priority' => 'required|numeric|min:1|max:4',
    'status_id' => 'required|numeric',
    'request_type_id' => 'required|numeric'
];

// Validasyon yap
$validator = new ValidationHelper($ticketData, $rules);
if (!$validator->validate()) {
    $_SESSION['errors'] = $validator->getErrors();
    $_SESSION['old_input'] = $ticketData;
    header('Location: ' . BASE_URL . '/ticket/create.php');
    exit;
}

try {
    // Ticket modelini oluştur
    $ticket = new Ticket();
    
    // Dosya yükleme işlemi
    $attachments = [];
    if (isset($_FILES['attachments']) && !empty($_FILES['attachments']['name'][0])) {
        $uploadDir = __DIR__ . '/../../uploads/tickets/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        foreach ($_FILES['attachments']['tmp_name'] as $key => $tmp_name) {
            $fileName = $_FILES['attachments']['name'][$key];
            $fileSize = $_FILES['attachments']['size'][$key];
            $fileType = $_FILES['attachments']['type'][$key];
            
            // Dosya boyutu kontrolü (10MB)
            if ($fileSize > 10 * 1024 * 1024) {
                throw new Exception('Dosya boyutu 10MB\'ı geçemez: ' . $fileName);
            }
            
            // Dosya türü kontrolü
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 
                           'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                           'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                           'text/plain', 'application/zip', 'application/x-rar-compressed'];
            
            if (!in_array($fileType, $allowedTypes)) {
                throw new Exception('Geçersiz dosya türü: ' . $fileName);
            }
            
            // Dosyayı yükle
            $newFileName = uniqid() . '_' . $fileName;
            if (move_uploaded_file($tmp_name, $uploadDir . $newFileName)) {
                $attachments[] = [
                    'name' => $fileName,
                    'path' => 'uploads/tickets/' . $newFileName,
                    'type' => $fileType,
                    'size' => $fileSize
                ];
            }
        }
    }
    
    // Ticket'ı kaydet
    $ticketId = $ticket->create($ticketData);
    
    // Ekleri kaydet
    if (!empty($attachments)) {
        foreach ($attachments as $attachment) {
            $ticket->addAttachment($ticketId, $attachment);
        }
    }
    
    $_SESSION['success'] = 'Ticket başarıyla oluşturuldu.';
    header('Location: ' . BASE_URL . '/ticket/view.php?id=' . $ticketId);
    exit;
    
} catch (Exception $e) {
    $_SESSION['error'] = 'Ticket oluşturulurken bir hata oluştu: ' . $e->getMessage();
    $_SESSION['old_input'] = $ticketData;
    header('Location: ' . BASE_URL . '/ticket/create.php');
    exit;
} 