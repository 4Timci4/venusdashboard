<!-- Ticket Listesi Sayfası -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-2">Ticket Listesi</h1>
    <p class="text-gray-600">Tüm destek taleplerini görüntüleyin ve yönetin</p>
</div>

<!-- Flash Mesajları -->
<?php if (!empty($messages)): ?>
    <?php foreach ($messages as $type => $message): ?>
        <div class="mb-4 p-4 rounded-lg <?= $type === 'success' ? 'bg-green-100 text-green-700' : ($type === 'error' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700') ?>">
            <div class="flex items-center">
                <i class="fas <?= $type === 'success' ? 'fa-check-circle' : ($type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle') ?> mr-3"></i>
                <span><?= $message ?></span>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Filtre Kartı -->
<div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
    <div class="border-b border-gray-200 bg-gray-50 p-4">
        <h2 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-filter mr-2 text-blue-500"></i>Filtreleme ve Arama
        </h2>
    </div>
    
    <div class="p-6">
        <form action="<?= BASE_URL ?>/tickets.php" method="GET" class="space-y-4">
            <!-- Arama alanı -->
            <div class="flex flex-wrap md:flex-nowrap gap-4 mb-4">
                <div class="w-full">
                    <div class="relative">
                        <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>" 
                               class="pl-10 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" 
                               placeholder="Ticket ID, konu veya içerik ara...">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>
                <div class="w-full md:w-auto">
                    <button type="submit" class="w-full md:w-auto px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <i class="fas fa-search mr-2"></i>Ara
                    </button>
                </div>
                <div class="w-full md:w-auto">
                    <a href="<?= BASE_URL ?>/tickets.php" class="inline-block text-center w-full md:w-auto px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        <i class="fas fa-times mr-2"></i>Filtreleri Temizle
                    </a>
                </div>
            </div>
            
            <!-- Filtreler -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Durum Filtresi -->
                <div>
                    <label for="status_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Durum
                    </label>
                    <select id="status_id" name="status_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Tüm Durumlar</option>
                        <?php foreach ($statuses as $status): ?>
                            <option value="<?= $status['id'] ?>" 
                                    <?= isset($filters['status_id']) && $filters['status_id'] == $status['id'] ? 'selected' : '' ?> 
                                    style="color: <?= $status['color'] ?>">
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
                    <select id="request_type_id" name="request_type_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Tüm İstek Türleri</option>
                        <?php foreach ($requestTypes as $type): ?>
                            <option value="<?= $type['id'] ?>" 
                                    <?= isset($filters['request_type_id']) && $filters['request_type_id'] == $type['id'] ? 'selected' : '' ?> 
                                    style="color: <?= $type['color'] ?>">
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
                    <select id="service_id" name="service_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
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
                    <select id="technician_id" name="technician_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
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
                    <select id="category_id" name="category_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
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
                    <select id="priority" name="priority" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
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
                
                <!-- Tarih Aralığı (Başlangıç) -->
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">
                        Başlangıç Tarihi
                    </label>
                    <input type="date" id="date_from" name="date_from" 
                           value="<?= isset($filters['date_from']) ? htmlspecialchars($filters['date_from']) : '' ?>" 
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
                
                <!-- Tarih Aralığı (Bitiş) -->
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">
                        Bitiş Tarihi
                    </label>
                    <input type="date" id="date_to" name="date_to" 
                           value="<?= isset($filters['date_to']) ? htmlspecialchars($filters['date_to']) : '' ?>" 
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>
            
            <!-- Filtre Butonları -->
            <div class="flex justify-end space-x-3 mt-4">
                <button type="submit" class="px-4 py-2 rounded-lg bg-gray-600 text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    <i class="fas fa-filter mr-2"></i>Filtrele
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Tickets Listesi -->
<div class="bg-white rounded-lg shadow-lg overflow-hidden">
    <div class="border-b border-gray-200 bg-gray-50 p-4 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-ticket-alt mr-2 text-blue-500"></i>Ticketlar
        </h2>
        <a href="<?= BASE_URL ?>/ticket/create.php" class="px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
            <i class="fas fa-plus mr-2"></i>Yeni Ticket
        </a>
    </div>
    
    <div class="overflow-x-auto">
        <?php if (empty($tickets)): ?>
            <div class="text-center py-16">
                <i class="fas fa-ticket-alt text-gray-300 text-5xl mb-4"></i>
                <p class="text-gray-500 text-lg">Henüz ticket bulunamadı.</p>
                <p class="text-gray-400 mb-6">Yeni bir ticket oluşturarak başlayabilirsiniz.</p>
                <a href="<?= BASE_URL ?>/ticket/create.php" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <i class="fas fa-plus mr-2"></i>Ticket Oluştur
                </a>
            </div>
        <?php else: ?>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            ID
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Konu
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Talep Sahibi
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Durum
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Öncelik
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Oluşturma Tarihi
                        </th>
                        <?php if (AuthHelper::isAdmin() || AuthHelper::isTechnician()): ?>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Teknisyen
                        </th>
                        <?php endif; ?>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            İşlemler
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($tickets as $ticket): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                #<?= $ticket['id'] ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <a href="<?= BASE_URL ?>/ticket/view.php?id=<?= $ticket['id'] ?>" class="text-blue-600 hover:text-blue-900 hover:underline">
                                    <?= htmlspecialchars($ticket['subject']) ?>
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= htmlspecialchars($ticket['user_name'] ?? 'Bilinmiyor') ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                                      style="background-color: <?= $ticket['status_color'] ?>20; color: <?= $ticket['status_color'] ?>">
                                    <?= htmlspecialchars($ticket['status_name'] ?? 'Bilinmiyor') ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php 
                                $priorityLabels = [
                                    1 => ['text' => 'Düşük', 'color' => 'bg-gray-100 text-gray-800'],
                                    2 => ['text' => 'Orta', 'color' => 'bg-blue-100 text-blue-800'],
                                    3 => ['text' => 'Yüksek', 'color' => 'bg-yellow-100 text-yellow-800'],
                                    4 => ['text' => 'Kritik', 'color' => 'bg-red-100 text-red-800']
                                ];
                                $priority = $ticket['priority'] ?? 1;
                                $priorityLabel = $priorityLabels[$priority] ?? $priorityLabels[1];
                                ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $priorityLabel['color'] ?>">
                                    <?= $priorityLabel['text'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= date('d.m.Y H:i', strtotime($ticket['created_at'])) ?>
                            </td>
                            <?php if (AuthHelper::isAdmin() || AuthHelper::isTechnician()): ?>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php if (!empty($ticket['technician_name'])): ?>
                                    <?= htmlspecialchars($ticket['technician_name']) ?>
                                <?php else: ?>
                                    <span class="text-gray-400 italic">Atanmamış</span>
                                <?php endif; ?>
                            </td>
                            <?php endif; ?>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="<?= BASE_URL ?>/ticket/view.php?id=<?= $ticket['id'] ?>" class="text-indigo-600 hover:text-indigo-900 mr-3" title="Görüntüle">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if (AuthHelper::isAdmin() || AuthHelper::isTechnician()): ?>
                                <a href="<?= BASE_URL ?>/ticket/edit.php?id=<?= $ticket['id'] ?>" class="text-blue-600 hover:text-blue-900" title="Düzenle">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    
    <!-- Sayfalama -->
    <?php if ($totalPages > 1): ?>
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Sayfa <span class="font-medium"><?= $currentPage ?></span> / <span class="font-medium"><?= $totalPages ?></span>
                </div>
                <div class="flex items-center space-x-2">
                    <?php
                    $queryParams = $_GET;
                    
                    // Önceki sayfa linki
                    if ($currentPage > 1) {
                        $queryParams['page'] = $currentPage - 1;
                        $prevPageUrl = BASE_URL . '/tickets.php?' . http_build_query($queryParams);
                        echo '<a href="' . $prevPageUrl . '" class="px-3 py-1 rounded-md bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">';
                        echo '<i class="fas fa-chevron-left"></i>';
                        echo '</a>';
                    } else {
                        echo '<span class="px-3 py-1 rounded-md bg-gray-100 border border-gray-300 text-gray-400 cursor-not-allowed">';
                        echo '<i class="fas fa-chevron-left"></i>';
                        echo '</span>';
                    }
                    
                    // Sayfa numaraları
                    $startPage = max(1, $currentPage - 2);
                    $endPage = min($totalPages, $startPage + 4);
                    
                    if ($startPage > 1) {
                        $queryParams['page'] = 1;
                        echo '<a href="' . BASE_URL . '/tickets.php?' . http_build_query($queryParams) . '" class="px-3 py-1 rounded-md bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">1</a>';
                        if ($startPage > 2) {
                            echo '<span class="px-2 py-1 text-gray-500">...</span>';
                        }
                    }
                    
                    for ($i = $startPage; $i <= $endPage; $i++) {
                        $queryParams['page'] = $i;
                        if ($i == $currentPage) {
                            echo '<span class="px-3 py-1 rounded-md bg-blue-600 border border-blue-600 text-white">' . $i . '</span>';
                        } else {
                            echo '<a href="' . BASE_URL . '/tickets.php?' . http_build_query($queryParams) . '" class="px-3 py-1 rounded-md bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">' . $i . '</a>';
                        }
                    }
                    
                    if ($endPage < $totalPages) {
                        if ($endPage < $totalPages - 1) {
                            echo '<span class="px-2 py-1 text-gray-500">...</span>';
                        }
                        $queryParams['page'] = $totalPages;
                        echo '<a href="' . BASE_URL . '/tickets.php?' . http_build_query($queryParams) . '" class="px-3 py-1 rounded-md bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">' . $totalPages . '</a>';
                    }
                    
                    // Sonraki sayfa linki
                    if ($currentPage < $totalPages) {
                        $queryParams['page'] = $currentPage + 1;
                        $nextPageUrl = BASE_URL . '/tickets.php?' . http_build_query($queryParams);
                        echo '<a href="' . $nextPageUrl . '" class="px-3 py-1 rounded-md bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">';
                        echo '<i class="fas fa-chevron-right"></i>';
                        echo '</a>';
                    } else {
                        echo '<span class="px-3 py-1 rounded-md bg-gray-100 border border-gray-300 text-gray-400 cursor-not-allowed">';
                        echo '<i class="fas fa-chevron-right"></i>';
                        echo '</span>';
                    }
                    ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
