<!-- Dashboard Sayfası -->
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-2">Dashboard</h1>
    <p class="text-gray-600">IT Help Desk sistemi istatistikleri ve özet bilgiler</p>
</div>

<!-- Özet İstatistikler -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Toplam Ticket -->
    <div class="bg-white rounded-lg shadow p-6 border-t-4 border-blue-500">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-sm font-medium text-gray-600">Toplam Ticket</h2>
                <p class="text-3xl font-bold text-gray-800"><?= $stats['total'] ?></p>
            </div>
            <div class="rounded-full bg-blue-100 p-3">
                <i class="fas fa-ticket-alt text-xl text-blue-500"></i>
            </div>
        </div>
        <div class="mt-4 text-sm text-gray-600">
            <span class="text-green-500 font-medium">
                <i class="fas fa-arrow-up"></i> 
                <?= round(($stats['total'] > 0 ? 100 : 0), 1) ?>%
            </span>
            <span>geçen haftaya göre</span>
        </div>
    </div>
    
    <!-- Açık Ticket -->
    <div class="bg-white rounded-lg shadow p-6 border-t-4 border-yellow-500">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-sm font-medium text-gray-600">Açık Ticket</h2>
                <p class="text-3xl font-bold text-gray-800"><?= $stats['open'] ?></p>
            </div>
            <div class="rounded-full bg-yellow-100 p-3">
                <i class="fas fa-clipboard-list text-xl text-yellow-500"></i>
            </div>
        </div>
        <div class="mt-4 text-sm text-gray-600">
            <span class="<?= $stats['open'] > $stats['total'] / 2 ? 'text-red-500' : 'text-green-500' ?> font-medium">
                <i class="fas <?= $stats['open'] > $stats['total'] / 2 ? 'fa-arrow-up' : 'fa-arrow-down' ?>"></i> 
                <?= $stats['total'] > 0 ? round(($stats['open'] / $stats['total']) * 100, 1) : 0 ?>%
            </span>
            <span>açık ticket oranı</span>
        </div>
    </div>
    
    <!-- Çözülen Ticket -->
    <div class="bg-white rounded-lg shadow p-6 border-t-4 border-green-500">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-sm font-medium text-gray-600">Çözülen Ticket</h2>
                <p class="text-3xl font-bold text-gray-800"><?= $stats['total'] - $stats['open'] ?></p>
            </div>
            <div class="rounded-full bg-green-100 p-3">
                <i class="fas fa-check-circle text-xl text-green-500"></i>
            </div>
        </div>
        <div class="mt-4 text-sm text-gray-600">
            <span class="text-green-500 font-medium">
                <i class="fas fa-check"></i> 
                <?= $stats['total'] > 0 ? round((($stats['total'] - $stats['open']) / $stats['total']) * 100, 1) : 0 ?>%
            </span>
            <span>çözüm oranı</span>
        </div>
    </div>
    
    <!-- Teknisyenler -->
    <div class="bg-white rounded-lg shadow p-6 border-t-4 border-purple-500">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-sm font-medium text-gray-600">Aktif Teknisyenler</h2>
                <p class="text-3xl font-bold text-gray-800"><?= count($technicians) ?></p>
            </div>
            <div class="rounded-full bg-purple-100 p-3">
                <i class="fas fa-user-cog text-xl text-purple-500"></i>
            </div>
        </div>
        <div class="mt-4">
            <a href="<?= BASE_URL ?>/technicians.php" class="text-sm text-purple-500 hover:text-purple-700 font-medium">
                <i class="fas fa-arrow-right mr-1"></i> Teknisyenleri görüntüle
            </a>
        </div>
    </div>
</div>

<!-- İki Kolona Bölünmüş Bölüm -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Sol Kolon: Son Ticketlar -->
    <div class="bg-white rounded-lg shadow">
        <div class="border-b border-gray-200 p-4 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-clock mr-2 text-blue-500"></i>Son Ticketlar
            </h2>
            <a href="<?= BASE_URL ?>/tickets.php" class="text-sm text-blue-500 hover:text-blue-700">
                Tümünü görüntüle <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        
        <div class="p-4">
            <?php if (!empty($recentTickets)): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Konu</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($recentTickets as $ticket): ?>
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <a href="<?= BASE_URL ?>/ticket/view.php?id=<?= $ticket['id'] ?>" class="text-blue-600 hover:text-blue-800">
                                            #<?= $ticket['id'] ?>
                                        </a>
                                    </td>
                                    <td class="px-4 py-3">
                                        <a href="<?= BASE_URL ?>/ticket/view.php?id=<?= $ticket['id'] ?>" class="text-gray-900 hover:text-blue-600">
                                            <?= htmlspecialchars($ticket['subject']) ?>
                                        </a>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                              style="background-color: <?= $ticket['status_color'] ?>20; color: <?= $ticket['status_color'] ?>;">
                                            <?= htmlspecialchars($ticket['status']) ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        <?= date('d.m.Y H:i', strtotime($ticket['created_at'])) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="p-6 text-center text-gray-500">
                    <i class="fas fa-ticket-alt text-3xl mb-3"></i>
                    <p>Henüz ticket bulunmuyor.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Sağ Kolon: Durumlara Göre Ticketlar (Pasta Grafik) -->
    <div class="bg-white rounded-lg shadow">
        <div class="border-b border-gray-200 p-4">
            <h2 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-chart-pie mr-2 text-blue-500"></i>Durumlara Göre Ticketlar
            </h2>
        </div>
        
        <div class="p-4">
            <?php if (isset($statusStats) && !empty($statusStats)): ?>
                <div class="flex flex-wrap -mx-2">
                    <?php foreach($statusStats as $status): ?>
                        <div class="w-1/2 px-2 mb-4">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full mr-2" style="background-color: <?= $status['color'] ?>"></div>
                                <div class="flex-1">
                                    <div class="flex justify-between mb-1">
                                        <span class="text-sm font-medium text-gray-700"><?= htmlspecialchars($status['name']) ?></span>
                                        <span class="text-sm font-medium text-gray-700"><?= $status['count'] ?></span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="h-2 rounded-full" style="width: <?= $stats['total'] > 0 ? ($status['count'] / $stats['total'] * 100) : 0 ?>%; background-color: <?= $status['color'] ?>"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pasta Grafik -->
                <div class="mt-4">
                    <canvas id="statusChart" height="200"></canvas>
                </div>
            <?php else: ?>
                <div class="p-6 text-center text-gray-500">
                    <i class="fas fa-chart-pie text-3xl mb-3"></i>
                    <p>Durum istatistikleri mevcut değil.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Alt Bölüm: İki Kolon -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Sol Kolon: Kategorilere Göre Dağılım -->
    <div class="bg-white rounded-lg shadow">
        <div class="border-b border-gray-200 p-4">
            <h2 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-folder mr-2 text-blue-500"></i>Kategorilere Göre Ticketlar
            </h2>
        </div>
        
        <div class="p-4">
            <?php if (isset($categoryStats) && !empty($categoryStats)): ?>
                <div class="space-y-4">
                    <?php foreach($categoryStats as $category): ?>
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm font-medium text-gray-700"><?= htmlspecialchars($category['name']) ?></span>
                                <span class="text-sm font-medium text-gray-700"><?= $category['count'] ?></span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full bg-blue-600" style="width: <?= $stats['total'] > 0 ? ($category['count'] / $stats['total'] * 100) : 0 ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Bar Grafik -->
                <div class="mt-6">
                    <canvas id="categoryChart" height="250"></canvas>
                </div>
            <?php else: ?>
                <div class="p-6 text-center text-gray-500">
                    <i class="fas fa-folder text-3xl mb-3"></i>
                    <p>Kategori istatistikleri mevcut değil.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Sağ Kolon: Son Aktiviteler -->
    <div class="bg-white rounded-lg shadow">
        <div class="border-b border-gray-200 p-4">
            <h2 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-history mr-2 text-blue-500"></i>Son Aktiviteler
            </h2>
        </div>
        
        <div class="p-4">
            <?php if (isset($recentLogs) && !empty($recentLogs)): ?>
                <div class="space-y-4">
                    <?php foreach($recentLogs as $log): ?>
                        <div class="flex">
                            <div class="flex-shrink-0 mr-3">
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                                    <i class="fas <?= getActivityIcon($log['action']) ?> text-blue-500"></i>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm text-gray-800">
                                    <span class="font-medium"><?= htmlspecialchars($log['username'] ?? 'Sistem') ?></span> 
                                    <?= getActivityDescription($log['action']) ?> 
                                    <a href="<?= BASE_URL ?>/ticket/view.php?id=<?= $log['ticket_id'] ?>" class="text-blue-600 hover:text-blue-800">
                                        #<?= $log['ticket_id'] ?>
                                    </a>
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    <?= date('d.m.Y H:i', strtotime($log['created_at'])) ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="p-6 text-center text-gray-500">
                    <i class="fas fa-history text-3xl mb-3"></i>
                    <p>Henüz aktivite kaydı bulunmuyor.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Grafik verilerini hazırla
$statusLabels = [];
$statusData = [];
$statusColors = [];

foreach ($statusStats as $status) {
    $statusLabels[] = $status['name'];
    $statusData[] = $status['count'];
    $statusColors[] = $status['color'];
}

$categoryLabels = [];
$categoryData = [];

foreach ($categoryStats as $category) {
    $categoryLabels[] = $category['name'];
    $categoryData[] = $category['count'];
}
?>

<!-- Grafik oluşturmak için ChartJS ekleyeceğiz -->
<?php $extraScripts = '
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Durum pasta grafiği
    const statusCtx = document.getElementById("statusChart").getContext("2d");
    const statusChart = new Chart(statusCtx, {
        type: "doughnut",
        data: {
            labels: ' . json_encode($statusLabels) . ',
            datasets: [{
                data: ' . json_encode($statusData) . ',
                backgroundColor: ' . json_encode($statusColors) . ',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: "right",
                    labels: {
                        padding: 20,
                        boxWidth: 10
                    }
                }
            }
        }
    });
    
    // Kategori çubuk grafiği
    const categoryCtx = document.getElementById("categoryChart").getContext("2d");
    const categoryChart = new Chart(categoryCtx, {
        type: "bar",
        data: {
            labels: ' . json_encode($categoryLabels) . ',
            datasets: [{
                label: "Ticket Sayısı",
                data: ' . json_encode($categoryData) . ',
                backgroundColor: "rgba(59, 130, 246, 0.8)",
                borderColor: "rgba(59, 130, 246, 1)",
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    precision: 0
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>
'; ?>

<?php
// Aktivite ikonunu döndürür
function getActivityIcon($action) {
    switch ($action) {
        case 'create_ticket':
            return 'fa-plus-circle';
        case 'update_ticket':
            return 'fa-edit';
        case 'status_update':
            return 'fa-sync';
        case 'assign_technician':
            return 'fa-user-cog';
        case 'add_comment':
            return 'fa-comment';
        case 'add_attachment':
            return 'fa-paperclip';
        case 'close_ticket':
            return 'fa-check-circle';
        default:
            return 'fa-history';
    }
}

// Aktivite açıklamasını döndürür
function getActivityDescription($action) {
    switch ($action) {
        case 'create_ticket':
            return 'yeni bir ticket oluşturdu:';
        case 'update_ticket':
            return 'ticket bilgilerini güncelledi:';
        case 'status_update':
            return 'durum güncellemesi yaptı:';
        case 'assign_technician':
            return 'teknisyen atadı:';
        case 'add_comment':
            return 'yorum ekledi:';
        case 'add_attachment':
            return 'dosya ekledi:';
        case 'close_ticket':
            return 'ticket\'ı kapattı:';
        default:
            return 'bir işlem gerçekleştirdi:';
    }
}
?>
