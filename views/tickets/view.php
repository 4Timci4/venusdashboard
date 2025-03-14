<!-- Ticket Detay Sayfası -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Ticket #<?= $ticket['id'] ?></h1>
            <p class="text-gray-600"><?= date('d.m.Y H:i', strtotime($ticket['created_at'])) ?> tarihinde oluşturuldu</p>
        </div>
        <!-- İşlem butonları -->
        <div class="flex space-x-2 mt-4 md:mt-0">
            <?php if (AuthHelper::isAdmin() || AuthHelper::isTechnician()): ?>
                <a href="<?= BASE_URL ?>/ticket/edit.php?id=<?= $ticket['id'] ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                    <i class="fas fa-edit mr-2"></i> Düzenle
                </a>
            <?php endif; ?>
            
            <!-- Ticket Kapatma/Açma Butonu -->
            <?php if ($ticket['status_id'] == 6): ?>
                <form action="<?= BASE_URL ?>/ticket/open.php" method="POST" class="inline" onsubmit="return confirm('Bu ticketı tekrar açmak istediğinize emin misiniz?');">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <input type="hidden" name="id" value="<?= $ticket['id'] ?>">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                        <i class="fas fa-folder-open mr-2"></i> Aç
                    </button>
                </form>
            <?php else: ?>
                <form action="<?= BASE_URL ?>/ticket/close.php" method="POST" class="inline" onsubmit="return confirm('Bu ticketı kapatmak istediğinize emin misiniz?');">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <input type="hidden" name="id" value="<?= $ticket['id'] ?>">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                        <i class="fas fa-times-circle mr-2"></i> Kapat
                    </button>
                </form>
            <?php endif; ?>

            <a href="<?= BASE_URL ?>/tickets.php" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Listeye Dön
            </a>
        </div>
    </div>
</div>

<!-- Flash Mesajları -->
<?php if (isset($messages) && !empty($messages)): ?>
    <?php 
    // Gösterilen mesaj içeriklerini takip et
    $shownMessages = [];
    ?>
    
    <?php foreach ($messages as $key => $data): ?>
        <?php 
        // Mesaj içeriğini ve tipini al
        $messageContent = isset($data['message']) ? $data['message'] : (is_string($data) ? $data : '');
        $type = isset($data['type']) ? $data['type'] : 'info';
        
        // Boş mesajları veya daha önce gösterilenleri atla
        if (empty($messageContent) || in_array($messageContent, $shownMessages)) {
            continue;
        }
        
        // Bu mesajı gösterildi olarak işaretle
        $shownMessages[] = $messageContent;
        ?>
        
        <div class="mb-6 rounded-lg p-4 flex items-center animate__animated animate__fadeIn
            <?= ($type === 'success') ? 'bg-green-100 border border-green-200 text-green-700' : 
                (($type === 'error') ? 'bg-red-100 border border-red-200 text-red-700' : 
                'bg-blue-100 border border-blue-200 text-blue-700') ?>">
            <i class="fas <?= ($type === 'success') ? 'fa-check-circle' : 
                (($type === 'error') ? 'fa-exclamation-circle' : 'fa-info-circle') ?> mr-3 text-xl"></i>
            <span><?= $messageContent ?></span>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Ticket Detayları -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Ana İçerik (Sol Panel) -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-md border border-gray-100 mb-6 overflow-hidden">
            <!-- Başlık Kısmı -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-4 text-white border-b flex justify-between items-center">
                <h2 class="text-lg font-semibold flex items-center">
                    <i class="fas fa-ticket-alt mr-2"></i>Ticket Detayları
                </h2>
                <!-- Durum Göstergesi -->
                <div class="flex items-center">
                    <span class="px-3 py-1 rounded-full text-sm font-medium
                        <?php 
                            switch($ticket['status_id']) {
                                case 1: // Açık
                                    echo 'bg-blue-700 text-white';
                                    break;
                                case 2: // İşlemde
                                    echo 'bg-yellow-500 text-white';
                                    break;
                                case 3: // Beklemede
                                    echo 'bg-orange-500 text-white';
                                    break;
                                case 4: // Yanıtlandı
                                    echo 'bg-green-600 text-white';
                                    break;
                                case 5: // Kapalı
                                    echo 'bg-gray-600 text-white';
                                    break;
                                default:
                                    echo 'bg-gray-200 text-gray-800';
                            }
                        ?>">
                        <?= isset($ticket['status_name']) ? $ticket['status_name'] : 'Durum Belirtilmedi' ?>
                    </span>
                </div>
            </div>
            
            <div class="p-5">
                <!-- Konu -->
                <div class="mb-6">
                    <h3 class="text-xl font-bold text-gray-800"><?= htmlspecialchars($ticket['subject']) ?></h3>
                </div>
                
                <!-- Açıklama -->
                <div class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-100">
                    <h4 class="text-sm font-semibold text-gray-600 mb-2">Açıklama</h4>
                    <div class="prose max-w-none">
                        <?php 
                        // Boş <p></p> etiketlerini temizleyelim
                        $description = trim($ticket['description']);
                        if (!empty($description) && $description !== '<p></p>') {
                            echo $description;
                        } else {
                            echo '<p class="text-gray-500 italic">Açıklama bulunmuyor.</p>';
                        }
                        ?>
                    </div>
                </div>
                
                <!-- Etki Detayları (varsa) -->
                <?php 
                // Etki detaylarını kontrol et
                $impactDetails = trim($ticket['impact_details'] ?? '');
                if (!empty($impactDetails) && $impactDetails !== '<p></p>'): 
                ?>
                <div class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-100">
                    <h4 class="text-sm font-semibold text-gray-600 mb-2">Etki Detayları</h4>
                    <p class="text-gray-700"><?= nl2br(htmlspecialchars($impactDetails)) ?></p>
                </div>
                <?php endif; ?>
                
                <!-- Ekler (varsa) -->
                <?php if (!empty($attachments)): ?>
                <div class="mb-6">
                    <h4 class="text-sm font-semibold text-gray-600 mb-2">Ekler</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <?php foreach ($attachments as $attachment): ?>
                            <a href="<?= BASE_URL ?>/<?= $attachment['file_path'] ?>" target="_blank" 
                               class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                <?php 
                                $fileType = strtolower(pathinfo($attachment['file_name'], PATHINFO_EXTENSION));
                                $icon = 'fa-file';
                                $color = 'text-gray-500';
                                
                                if (in_array($fileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                                    $icon = 'fa-file-image';
                                    $color = 'text-blue-500';
                                } elseif (in_array($fileType, ['pdf'])) {
                                    $icon = 'fa-file-pdf';
                                    $color = 'text-red-500';
                                } elseif (in_array($fileType, ['doc', 'docx'])) {
                                    $icon = 'fa-file-word';
                                    $color = 'text-blue-600';
                                } elseif (in_array($fileType, ['xls', 'xlsx'])) {
                                    $icon = 'fa-file-excel';
                                    $color = 'text-green-600';
                                } elseif (in_array($fileType, ['txt'])) {
                                    $icon = 'fa-file-alt';
                                    $color = 'text-gray-600';
                                } elseif (in_array($fileType, ['zip', 'rar', 'gz'])) {
                                    $icon = 'fa-file-archive';
                                    $color = 'text-yellow-600';
                                }
                                ?>
                                <i class="fas <?= $icon ?> <?= $color ?> text-2xl mr-3"></i>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-900 truncate"><?= htmlspecialchars($attachment['file_name']) ?></div>
                                    <div class="text-xs text-gray-500"><?= number_format($attachment['file_size'] / 1024, 2) ?> KB</div>
                                </div>
                                <i class="fas fa-download text-gray-400"></i>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Dosya Ekleme Formu -->
                <div class="mt-6 border-t pt-6">
                    <h4 class="text-md font-semibold text-gray-700 mb-3">Dosya Ekle</h4>
                    <form action="<?= BASE_URL ?>/ticket/attachment.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        <input type="hidden" name="id" value="<?= $ticket['id'] ?>">
                        
                        <div class="flex items-center justify-center w-full">
                            <label class="flex flex-col w-full h-28 border-2 border-gray-300 border-dashed hover:bg-gray-50 hover:border-indigo-400 rounded-lg cursor-pointer transition-all duration-200">
                                <div class="flex flex-col items-center justify-center pt-5">
                                    <i class="fas fa-cloud-upload-alt text-gray-400 text-2xl mb-2"></i>
                                    <p class="text-sm text-gray-600">
                                        Dosyaları sürükle bırak ya da <span class="text-indigo-600 font-medium">gözat</span>
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        Maximum 10MB
                                    </p>
                                </div>
                                <input type="file" name="attachments[]" multiple class="opacity-0" required>
                            </label>
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors flex items-center">
                                <i class="fas fa-upload mr-2"></i> Yükle
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Yorumlar -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 mb-6 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-4 text-white border-b">
                <h2 class="text-lg font-semibold flex items-center">
                    <i class="fas fa-comments mr-2"></i>Yorumlar
                </h2>
            </div>
            
            <div class="p-5">
                <!-- Yorum Listesi -->
                <?php if (!empty($comments)): ?>
                    <div class="space-y-6 mb-6">
                        <?php foreach ($comments as $comment): ?>
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                                <div class="flex items-center mb-3">
                                    <?php if (!empty($comment['profile_image'])): ?>
                                        <img src="<?= BASE_URL ?>/<?= $comment['profile_image'] ?>" class="w-10 h-10 rounded-full mr-3" alt="<?= htmlspecialchars($comment['username']) ?>">
                                    <?php else: ?>
                                        <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                                            <span class="text-indigo-800 font-medium">
                                                <?= strtoupper(substr($comment['username'], 0, 1)) ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <h4 class="font-medium text-gray-900"><?= htmlspecialchars($comment['full_name']) ?></h4>
                                        <div class="text-xs text-gray-500 flex items-center">
                                            <span><?= date('d.m.Y H:i', strtotime($comment['created_at'])) ?></span>
                                            <span class="px-2 py-1 ml-2 rounded-full text-xs 
                                                <?php 
                                                    if ($comment['role_id'] == 1) {
                                                        echo 'bg-red-100 text-red-800'; // Admin
                                                    } elseif ($comment['role_id'] == 2) {
                                                        echo 'bg-blue-100 text-blue-800'; // Teknisyen
                                                    } else {
                                                        echo 'bg-gray-100 text-gray-800'; // Kullanıcı
                                                    }
                                                ?>">
                                                <?php 
                                                    if ($comment['role_id'] == 1) {
                                                        echo 'Yönetici';
                                                    } elseif ($comment['role_id'] == 2) {
                                                        echo 'Teknisyen';
                                                    } else {
                                                        echo 'Kullanıcı';
                                                    }
                                                ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-gray-700">
                                    <?= nl2br(htmlspecialchars($comment['comment'])) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-yellow-500 mr-3"></i>
                            <div class="text-sm text-yellow-700">Henüz yorum yapılmamış.</div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Yorum Ekleme Formu -->
                <form action="<?= BASE_URL ?>/ticket/comment.php" method="POST" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <input type="hidden" name="id" value="<?= $ticket['id'] ?>">
                    
                    <div>
                        <label for="comment" class="block text-sm font-medium text-gray-700 mb-1">Yeni Yorum Ekle</label>
                        <textarea id="comment" name="comment" rows="4" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Yorumunuzu buraya yazın..." required></textarea>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors flex items-center">
                            <i class="fas fa-paper-plane mr-2"></i> Gönder
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Sağ Panel (Detay Bilgiler ve İşlemler) -->
    <div class="lg:col-span-1">
        <!-- Durum Bilgileri -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 mb-6 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-4 text-white border-b">
                <h2 class="text-lg font-semibold flex items-center">
                    <i class="fas fa-info-circle mr-2"></i>Ticket Bilgileri
                </h2>
            </div>
            
            <div class="p-5">
                <dl class="space-y-4">
                    <!-- Öncelik -->
                    <div class="flex flex-col">
                        <dt class="text-sm font-medium text-gray-500">Öncelik</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                <?php 
                                    switch($ticket['priority']) {
                                        case 1: // Düşük
                                            echo 'bg-gray-100 text-gray-800';
                                            $priorityText = 'Düşük';
                                            $priorityIcon = 'fa-arrow-down text-gray-500';
                                            break;
                                        case 2: // Orta
                                            echo 'bg-blue-100 text-blue-800';
                                            $priorityText = 'Orta';
                                            $priorityIcon = 'fa-minus text-blue-500';
                                            break;
                                        case 3: // Yüksek
                                            echo 'bg-orange-100 text-orange-800';
                                            $priorityText = 'Yüksek';
                                            $priorityIcon = 'fa-arrow-up text-orange-500';
                                            break;
                                        case 4: // Kritik
                                            echo 'bg-red-100 text-red-800';
                                            $priorityText = 'Kritik';
                                            $priorityIcon = 'fa-exclamation-triangle text-red-500';
                                            break;
                                        default:
                                            echo 'bg-gray-100 text-gray-800';
                                            $priorityText = 'Belirtilmedi';
                                            $priorityIcon = 'fa-question text-gray-500';
                                    }
                                ?>">
                                <i class="fas <?= $priorityIcon ?> mr-1"></i>
                                <?= $priorityText ?>
                            </span>
                        </dd>
                    </div>
                    
                    <!-- Kullanıcı -->
                    <div class="flex flex-col">
                        <dt class="text-sm font-medium text-gray-500">Talep Sahibi</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <?= htmlspecialchars($ticket['username']) ?>
                            <?php if (!empty($ticket['full_name'])): ?>
                                <span class="text-gray-500">(<?= htmlspecialchars($ticket['full_name']) ?>)</span>
                            <?php endif; ?>
                        </dd>
                    </div>
                    
                    <!-- Teknisyen -->
                    <div class="flex flex-col">
                        <dt class="text-sm font-medium text-gray-500">Atanan Teknisyen</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <?php if (!empty($ticket['technician_name'])): ?>
                                <?= htmlspecialchars($ticket['technician_name']) ?>
                            <?php else: ?>
                                <span class="text-gray-500">Henüz atanmadı</span>
                            <?php endif; ?>
                        </dd>
                    </div>
                    
                    <!-- İstek Türü -->
                    <div class="flex flex-col">
                        <dt class="text-sm font-medium text-gray-500">İstek Türü</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <?= !empty($ticket['request_type_name']) ? htmlspecialchars($ticket['request_type_name']) : 'Belirtilmedi' ?>
                        </dd>
                    </div>
                    
                    <!-- Servis -->
                    <div class="flex flex-col">
                        <dt class="text-sm font-medium text-gray-500">Servis</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <?= !empty($ticket['service_name']) ? htmlspecialchars($ticket['service_name']) : 'Belirtilmedi' ?>
                        </dd>
                    </div>
                    
                    <!-- Kategori ve Alt Kategori -->
                    <div class="flex flex-col">
                        <dt class="text-sm font-medium text-gray-500">Kategori</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <?= !empty($ticket['category_name']) ? htmlspecialchars($ticket['category_name']) : 'Belirtilmedi' ?>
                            <?php if (!empty($ticket['subcategory_name'])): ?>
                                <span class="text-gray-500"> &raquo; <?= htmlspecialchars($ticket['subcategory_name']) ?></span>
                            <?php endif; ?>
                        </dd>
                    </div>
                    
                    <!-- Tarihler -->
                    <div class="flex flex-col">
                        <dt class="text-sm font-medium text-gray-500">Oluşturulma Tarihi</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <?= date('d.m.Y H:i', strtotime($ticket['created_at'])) ?>
                        </dd>
                    </div>
                    
                    <?php if (!empty($ticket['updated_at'])): ?>
                    <div class="flex flex-col">
                        <dt class="text-sm font-medium text-gray-500">Son Güncelleme</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <?= date('d.m.Y H:i', strtotime($ticket['updated_at'])) ?>
                        </dd>
                    </div>
                    <?php endif; ?>
                </dl>
            </div>
        </div>
        
        <!-- Durum Değiştirme -->
        <?php if (AuthHelper::isAdmin() || AuthHelper::isTechnician()): ?>
        <div class="bg-white rounded-xl shadow-md border border-gray-100 mb-6 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-4 text-white border-b">
                <h2 class="text-lg font-semibold flex items-center">
                    <i class="fas fa-tasks mr-2"></i>İşlemler
                </h2>
            </div>
            
            <div class="p-5">
                <!-- Durum Değiştirme -->
                <form id="status-form" class="mb-6">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <input type="hidden" name="id" value="<?= $ticket['id'] ?>">
                    
                    <div class="mb-4">
                        <label for="status_id" class="block text-sm font-medium text-gray-700 mb-1">Durum Değiştir</label>
                        <select id="status_id" name="status_id" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <?php foreach ($statuses as $status): ?>
                                <option value="<?= $status['id'] ?>" 
                                    <?= ($ticket['status_id'] == $status['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($status['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors flex items-center justify-center">
                        <i class="fas fa-save mr-2"></i> Durumu Güncelle
                    </button>
                </form>
                
                <!-- Teknisyen Atama -->
                <form id="technician-form">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <input type="hidden" name="id" value="<?= $ticket['id'] ?>">
                    
                    <div class="mb-4">
                        <label for="technician_id" class="block text-sm font-medium text-gray-700 mb-1">Teknisyen Ata</label>
                        <select id="technician_id" name="technician_id" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">-- Teknisyen Seçin --</option>
                            <?php foreach ($technicians as $tech): ?>
                                <option value="<?= $tech['technician_id'] ?>" 
                                    <?= (!empty($ticket['technician_id']) && $ticket['technician_id'] == $tech['technician_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($tech['full_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors flex items-center justify-center">
                        <i class="fas fa-user-check mr-2"></i> Teknisyeni Ata
                    </button>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- JavaScript kodu -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Durum güncelleme için AJAX
    const statusForm = document.getElementById('status-form');
    if (statusForm) {
        statusForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(statusForm);
            
            fetch(BASE_URL + '/api/tickets/status.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Başarılı mesajı göster
                    alert(data.message);
                    // Sayfayı yenile
                    window.location.reload();
                } else {
                    // Hata mesajı göster
                    alert('Hata: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Hata:', error);
                alert('İstek gönderilirken bir hata oluştu.');
            });
        });
    }
    
    // Teknisyen atama için AJAX
    const technicianForm = document.getElementById('technician-form');
    if (technicianForm) {
        technicianForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(technicianForm);
            
            fetch(BASE_URL + '/api/tickets/assign.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Başarılı mesajı göster
                    alert(data.message);
                    // Sayfayı yenile
                    window.location.reload();
                } else {
                    // Hata mesajı göster
                    alert('Hata: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Hata:', error);
                alert('İstek gönderilirken bir hata oluştu.');
            });
        });
    }
});
</script>
