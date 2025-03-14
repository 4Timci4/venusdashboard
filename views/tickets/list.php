<!-- Ticket Listesi Sayfası -->
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-2">Ticket Listesi</h1>
    <p class="text-gray-600">Tüm destek taleplerini görüntüleyin ve yönetin</p>
</div>

<!-- Flash Mesajları -->
<?php if (!empty($messages)): ?>
    <?php foreach ($messages as $type => $message): ?>
        <div class="mb-4 p-4 rounded-lg shadow-sm transform transition duration-300 hover:scale-[1.01] <?= $type === 'success' ? 'bg-green-100 text-green-700 border-l-4 border-green-500' : ($type === 'error' ? 'bg-red-100 text-red-700 border-l-4 border-red-500' : 'bg-blue-100 text-blue-700 border-l-4 border-blue-500') ?>">
            <div class="flex items-center">
                <i class="fas <?= $type === 'success' ? 'fa-check-circle' : ($type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle') ?> mr-3"></i>
                <span><?= $message ?></span>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<!-- İşlem Butonları -->
<div class="flex flex-wrap items-center justify-between mb-6 gap-3">
    <div class="flex items-center space-x-2">
        <a href="<?= BASE_URL ?>/ticket/create.php" class="px-5 py-2.5 rounded-lg bg-gradient-to-r from-green-500 to-green-600 text-white hover:from-green-600 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 shadow-md transition-all duration-300 flex items-center">
            <i class="fas fa-plus mr-2"></i>Yeni Ticket Oluştur
        </a>
    </div>
    
                    <div class="relative">
        <form action="<?= BASE_URL ?>/tickets.php" method="GET" class="flex">
                        <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>" 
                   class="pl-10 py-2.5 w-72 rounded-lg border-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" 
                   placeholder="Ticket ara...">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
            <button type="submit" class="ml-2 px-4 py-2.5 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 shadow-md transition-all duration-300">
                Ara
            </button>
        </form>
                    </div>
                </div>

<!-- Ana İçerik Bölümü - 2 Kolonlu Layout -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <!-- Sol Kolon - Filtreler -->
    <div class="lg:col-span-3">
        <div class="sticky top-4 bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-4 text-white">
                <h3 class="text-lg font-semibold flex items-center">
                    <i class="fas fa-filter mr-2"></i>Filtreler
                </h3>
            </div>
            
            <div class="p-4 space-y-4">
                <form action="<?= BASE_URL ?>/tickets.php" method="GET" class="space-y-4">
            <!-- Filtreler -->
                    <div class="space-y-4">
                <!-- Durum Filtresi -->
                <div>
                    <label for="status_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Durum
                    </label>
                            <select id="status_id" name="status_id" class="w-full rounded-lg border-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">
                        <option value="">Tüm Durumlar</option>
                        <?php foreach ($statuses as $status): ?>
                            <option value="<?= $status['id'] ?>" 
                                    <?= isset($filters['status_id']) && $filters['status_id'] == $status['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($status['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- İstek Türü Filtresi -->
                <div>
                    <label for="request_type_id" class="block text-sm font-medium text-gray-700 mb-1">
                        İstek Türü
                    </label>
                            <select id="request_type_id" name="request_type_id" class="w-full rounded-lg border-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">
                        <option value="">Tüm İstek Türleri</option>
                        <?php foreach ($requestTypes as $type): ?>
                            <option value="<?= $type['id'] ?>" 
                                    <?= isset($filters['request_type_id']) && $filters['request_type_id'] == $type['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($type['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Servis Filtresi -->
                <div>
                    <label for="service_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Servis
                    </label>
                            <select id="service_id" name="service_id" class="w-full rounded-lg border-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">
                        <option value="">Tüm Servisler</option>
                        <?php foreach ($services as $service): ?>
                            <option value="<?= $service['id'] ?>" 
                                    <?= isset($filters['service_id']) && $filters['service_id'] == $service['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($service['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Teknisyen Filtresi -->
                <?php if (AuthHelper::isAdmin() || AuthHelper::isTechnician()): ?>
                <div>
                    <label for="technician_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Teknisyen
                    </label>
                            <select id="technician_id" name="technician_id" class="w-full rounded-lg border-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">
                        <option value="">Tüm Teknisyenler</option>
                        <option value="0" <?= isset($filters['technician_id']) && $filters['technician_id'] == '0' ? 'selected' : '' ?>>
                            Atanmamış
                        </option>
                        <?php foreach ($technicians as $tech): ?>
                            <option value="<?= $tech['technician_id'] ?>" 
                                    <?= isset($filters['technician_id']) && $filters['technician_id'] == $tech['technician_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($tech['full_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                
                <!-- Kategori Filtresi -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Kategori
                    </label>
                            <select id="category_id" name="category_id" class="w-full rounded-lg border-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">
                        <option value="">Tüm Kategoriler</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>" 
                                    <?= isset($filters['category_id']) && $filters['category_id'] == $category['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Öncelik Filtresi -->
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">
                        Öncelik
                    </label>
                            <select id="priority" name="priority" class="w-full rounded-lg border-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">
                        <option value="">Tüm Öncelikler</option>
                        <option value="1" <?= isset($filters['priority']) && $filters['priority'] == '1' ? 'selected' : '' ?>>
                            Düşük
                        </option>
                        <option value="2" <?= isset($filters['priority']) && $filters['priority'] == '2' ? 'selected' : '' ?>>
                            Orta
                        </option>
                        <option value="3" <?= isset($filters['priority']) && $filters['priority'] == '3' ? 'selected' : '' ?>>
                            Yüksek
                        </option>
                        <option value="4" <?= isset($filters['priority']) && $filters['priority'] == '4' ? 'selected' : '' ?>>
                            Kritik
                        </option>
                    </select>
                </div>
            </div>
            
            <!-- Filtre Butonları -->
                    <div class="pt-4 border-t border-gray-200 flex flex-col gap-2">
                        <button type="submit" class="w-full px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-300 flex justify-center items-center">
                    <i class="fas fa-filter mr-2"></i>Filtrele
                </button>
                        <a href="<?= BASE_URL ?>/tickets.php" class="w-full px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-300 text-center">
                            <i class="fas fa-times mr-2"></i>Temizle
                        </a>
            </div>
        </form>
    </div>
</div>
    </div>
    
    <!-- Sağ Kolon - Tickets Listesi -->
    <div class="lg:col-span-9">
        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 p-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-ticket-alt mr-2 text-indigo-500"></i>Destek Talepleri
                </h2>
                <div class="text-sm text-gray-500">
                    Toplam: <span class="font-semibold"><?= $totalCount ?? count($tickets) ?></span> ticket
                </div>
    </div>
    
    <?php if (empty($tickets)): ?>
        <div class="text-center py-16">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-100 text-indigo-500 mb-4">
                        <i class="fas fa-ticket-alt text-2xl"></i>
                    </div>
                    <h3 class="text-gray-700 text-lg font-medium mb-2">Henüz ticket bulunamadı</h3>
                    <p class="text-gray-500 max-w-md mx-auto mb-6">Yeni bir ticket oluşturarak destek talebinde bulunabilirsiniz.</p>
                    <a href="<?= BASE_URL ?>/ticket/create.php" class="px-6 py-2.5 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 shadow-md transition-all duration-300">
                        <i class="fas fa-plus mr-2"></i>Yeni Ticket Oluştur
            </a>
        </div>
    <?php else: ?>
                <!-- Ticket Kartları -->
                <div class="divide-y divide-gray-200">
                <?php foreach ($tickets as $ticket): ?>
                        <div class="p-4 hover:bg-gray-50 transition-colors duration-200">
                            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                                <!-- Sol Kısım - Ticket Bilgileri -->
                                <div class="flex-grow">
                                    <div class="flex items-start">
                                        <div class="h-10 w-10 flex items-center justify-center rounded-full bg-indigo-100 text-indigo-500 mr-3 flex-shrink-0">
                                            <i class="fas fa-ticket-alt"></i>
                                        </div>
                            <div>
                                            <div class="flex items-center">
                                                <span class="text-xs font-semibold text-gray-500 bg-gray-100 px-2 py-1 rounded-full mr-2">#<?= $ticket['id'] ?></span>
                                                <h3 class="font-medium text-gray-900">
                                                    <a href="<?= BASE_URL ?>/ticket/view.php?id=<?= $ticket['id'] ?>" class="hover:text-indigo-600">
                                    <?= htmlspecialchars($ticket['subject']) ?>
                                                    </a>
                                </h3>
                            </div>
                                            <div class="mt-1 text-sm text-gray-500">
                                                <span><?= date('d.m.Y H:i', strtotime($ticket['created_at'])) ?></span>
                                                <span class="px-2">•</span>
                                                <span><?= htmlspecialchars($ticket['user_name'] ?? 'Bilinmiyor') ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Sağ Kısım - Etiketler ve İşlemler -->
                                <div class="flex flex-wrap gap-2 items-center">
                                    <!-- Durum Etiketi -->
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                          style="background-color: <?= $ticket['status_color'] ?>20; color: <?= $ticket['status_color'] ?>">
                                        <?= htmlspecialchars($ticket['status_name'] ?? 'Bilinmiyor') ?>
                                    </span>
                                    
                                    <!-- Öncelik Etiketi -->
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                <?= ($ticket['priority'] == 1) ? 'bg-gray-100 text-gray-800' : 
                                                    (($ticket['priority'] == 2) ? 'bg-blue-100 text-blue-800' : 
                                                    (($ticket['priority'] == 3) ? 'bg-orange-100 text-orange-800' : 
                                                    'bg-red-100 text-red-800')) ?>">
                                        <?= ($ticket['priority'] == 1) ? 'Düşük' : 
                                            (($ticket['priority'] == 2) ? 'Orta' : 
                                            (($ticket['priority'] == 3) ? 'Yüksek' : 
                                            'Kritik')) ?>
                                    </span>
                                    
                                    <!-- İşlem Butonları -->
                                    <div class="flex space-x-1">
                                        <a href="<?= BASE_URL ?>/ticket/view.php?id=<?= $ticket['id'] ?>" class="flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 hover:bg-indigo-200 transition-colors duration-200" title="Görüntüle">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if (AuthHelper::isAdmin() || AuthHelper::isTechnician()): ?>
                                        <a href="<?= BASE_URL ?>/ticket/edit.php?id=<?= $ticket['id'] ?>" class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-600 hover:bg-blue-200 transition-colors duration-200" title="Düzenle">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
                
                <!-- Sayfalama -->
                <?php if ($totalPages > 1): ?>
                <div class="px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Toplam <span class="font-medium"><?= $totalCount ?></span> kayıttan 
                                <span class="font-medium"><?= ($currentPage - 1) * ITEMS_PER_PAGE + 1 ?></span> - 
                                <span class="font-medium"><?= min($currentPage * ITEMS_PER_PAGE, $totalCount) ?></span> arası gösteriliyor
                            </p>
        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <?php if ($currentPage > 1): ?>
                                <a href="<?= BASE_URL ?>/tickets.php?page=<?= $currentPage - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <a href="<?= BASE_URL ?>/tickets.php?page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
                                   class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium <?= $i === $currentPage ? 'bg-indigo-50 text-indigo-600 z-10 border-indigo-500' : 'text-gray-500 hover:bg-gray-50' ?>">
                                    <?= $i ?>
                                </a>
                                <?php endfor; ?>
                                
                                <?php if ($currentPage < $totalPages): ?>
                                <a href="<?= BASE_URL ?>/tickets.php?page=<?= $currentPage + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                                <?php endif; ?>
                            </nav>
    </div>
                </div>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- JavaScript kodu (sayfanın en alt kısmına) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Ticket üzerine geldiğinde vurgulama efekti
    const ticketItems = document.querySelectorAll('.ticket-item');
    ticketItems.forEach(item => {
        item.addEventListener('mouseenter', () => {
            item.classList.add('bg-gray-50', 'transform', 'scale-[1.01]');
        });
        item.addEventListener('mouseleave', () => {
            item.classList.remove('bg-gray-50', 'transform', 'scale-[1.01]');
        });
    });
    
    // Flash mesajlarını otomatik gizle
    const flashMessages = document.querySelectorAll('.flash-message');
    if (flashMessages.length > 0) {
        setTimeout(() => {
            flashMessages.forEach(message => {
                message.classList.add('opacity-0');
                setTimeout(() => {
                    message.style.display = 'none';
                }, 300);
            });
        }, 5000);
    }
});
</script>
