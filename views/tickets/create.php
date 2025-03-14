<!-- CSS Stil Tanımlamaları -->
<style>
    .placeholder-indent::placeholder {
        text-indent: 10px; /* Placeholder metinlerini 20px sağa kaydır */
    }
</style>

<!-- Ticket Oluşturma Formu -->
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-2">Yeni Ticket Oluştur</h1>
    <p class="text-gray-600">IT destek talebi oluşturmak için aşağıdaki formu doldurun</p>
</div>

<!-- Ana İçerik Bölümü -->
<div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
    <!-- Başlık Kısmı -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-4 text-white border-b">
        <h2 class="text-lg font-semibold flex items-center">
            <i class="fas fa-ticket-alt mr-2"></i>Ticket Bilgileri
        </h2>
    </div>
    
    <div class="p-6">
        <!-- Hata mesajları -->
        <?php if (isset($errors) && !empty($errors)): ?>
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg animate__animated animate__fadeIn">
                <h3 class="font-bold flex items-center mb-2">
                    <i class="fas fa-exclamation-circle mr-2"></i>Formu gönderirken hatalar oluştu
                </h3>
                <ul class="list-disc list-inside pl-4">
                    <?php foreach ($errors as $field => $message): ?>
                        <li><?= $message ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Ticket formu -->
        <form id="ticket-form" action="<?= BASE_URL ?>/ticket/store" method="POST" enctype="multipart/form-data" class="divide-y divide-gray-200">
            <!-- CSRF token -->
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            
            <!-- Form Bölüm 1: Temel Bilgiler -->
            <div class="pb-6">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Temel Bilgiler</h3>
                
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- Sol kolon -->
                    <div>
                        <!-- Kullanıcı seçimi -->
                        <div class="mb-5">
                            <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                Talep Sahibi 
                                <span class="ml-1 text-red-500">*</span>
                                <span class="ml-1 group relative">
                                    <i class="fas fa-info-circle text-gray-400 cursor-help"></i>
                                    <div class="hidden group-hover:block absolute z-10 w-48 p-2 mt-1 text-xs bg-gray-800 text-white rounded shadow-lg">
                                        Ticket'ı oluşturan kullanıcı
                                    </div>
                                </span>
                            </label>
                            <?php if (AuthHelper::isAdmin()): ?>
                                <select id="user_id" name="user_id" class="w-full rounded-lg border-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-colors">
                                    <option value="">-- Kullanıcı Seçin --</option>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?= $user['id'] ?>" <?= (AuthHelper::getUserId() == $user['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($user['full_name']) ?> (<?= htmlspecialchars($user['username']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else: ?>
                                <input type="hidden" name="user_id" value="<?= AuthHelper::getUserId() ?>">
                                <div class="py-2 px-3 bg-gray-100 rounded-lg border border-gray-200 text-gray-700">
                                    <?= htmlspecialchars(AuthHelper::getUsername()) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- İstek türü -->
                        <div class="mb-5">
                            <label for="request_type_id" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                İstek Türü 
                                <span class="ml-1 text-red-500">*</span>
                            </label>
                            <select id="request_type_id" name="request_type_id" required class="w-full rounded-lg border-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-colors">
                                <option value="">-- İstek Türü Seçin --</option>
                                <?php foreach ($requestTypes as $type): ?>
                                    <option value="<?= $type['id'] ?>">
                                        <?= htmlspecialchars($type['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Durum -->
                        <div class="mb-5">
                            <label for="status_id" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                Durum
                                <span class="ml-1 text-red-500">*</span>
                            </label>
                            <select id="status_id" name="status_id" required class="w-full rounded-lg border-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-colors">
                                <?php foreach ($statuses as $status): ?>
                                    <option value="<?= $status['id'] ?>" 
                                            <?= $status['id'] == 1 ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($status['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Sağ kolon -->
                    <div>
                        <!-- Öncelik -->
                        <div class="mb-5">
                            <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">
                                Öncelik
                            </label>
                            <select id="priority" name="priority" class="w-full rounded-lg border-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-colors">
                                <option value="1" <?= !isset($_POST['priority']) || $_POST['priority'] == '1' ? 'selected' : '' ?>>Düşük</option>
                                <option value="2" <?= isset($_POST['priority']) && $_POST['priority'] == '2' ? 'selected' : '' ?>>Orta</option>
                                <option value="3" <?= isset($_POST['priority']) && $_POST['priority'] == '3' ? 'selected' : '' ?>>Yüksek</option>
                                <option value="4" <?= isset($_POST['priority']) && $_POST['priority'] == '4' ? 'selected' : '' ?>>Kritik</option>
                            </select>
                        </div>
                        
                        <!-- Konu başlığı -->
                        <div class="mb-5">
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                Konu <span class="ml-1 text-red-500">*</span>
                            </label>
                            <input type="text" id="subject" name="subject" required 
                                class="w-full rounded-lg border-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-colors placeholder-indent" 
                                placeholder="Ticket konusunu girin">
                        </div>
                        
                        <!-- Servis -->
                        <div class="mb-5">
                            <label for="service_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Servis
                            </label>
                            <select id="service_id" name="service_id" class="w-full rounded-lg border-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-colors">
                                <option value="">-- Servis Seçin --</option>
                                <?php foreach ($services as $service): ?>
                                    <option value="<?= $service['id'] ?>">
                                        <?= htmlspecialchars($service['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Form Bölüm 2: Kategori Bilgileri -->
            <div class="py-6">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Kategori Bilgileri</h3>
                
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- Sol kolon -->
                    <div>
                        <!-- Kategori -->
                        <div class="mb-5">
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Hizmet Kategorisi
                            </label>
                            <select id="category_id" name="category_id" class="w-full rounded-lg border-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-colors">
                                <option value="">-- Kategori Seçin --</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>">
                                        <?= htmlspecialchars($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Alt Kategori -->
                        <div class="mb-5">
                            <label for="subcategory_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Alt Kategori
                            </label>
                            <select id="subcategory_id" name="subcategory_id" class="w-full rounded-lg border-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-colors" disabled>
                                <option value="">-- Önce Kategori Seçin --</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Sağ kolon -->
                    <div>
                        <!-- Etki -->
                        <div class="mb-5">
                            <label for="impact" class="block text-sm font-medium text-gray-700 mb-1">
                                Etki
                            </label>
                            <select id="impact" name="impact" class="w-full rounded-lg border-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-colors">
                                <option value="">-- Etki Seçin --</option>
                                <option value="Düşük">Düşük</option>
                                <option value="Orta">Orta</option>
                                <option value="Yüksek">Yüksek</option>
                                <option value="Kritik">Kritik</option>
                            </select>
                        </div>
                        
                        <!-- Aktivite -->
                        <div class="mb-5">
                            <label for="activity" class="block text-sm font-medium text-gray-700 mb-1">
                                Aktivite
                            </label>
                            <input type="text" id="activity" name="activity" class="w-full rounded-lg border-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-colors placeholder-indent" placeholder="Aktivite bilgisi">
                        </div>
                    </div>
                </div>
                
                <!-- Etki detayları -->
                <div class="mb-5">
                    <label for="impact_details" class="block text-sm font-medium text-gray-700 mb-1">
                        Etki Detayları
                    </label>
                    <textarea id="impact_details" name="impact_details" rows="3" 
                            class="w-full rounded-lg border-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-colors" 
                            placeholder="Etkinin detaylarını açıklayın"></textarea>
                </div>
            </div>
            
            <!-- Form Bölüm 3: Atama Bilgileri -->
            <?php if (AuthHelper::isAdmin() || AuthHelper::isTechnician()): ?>
            <div class="py-6">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Atama</h3>
                
                <!-- Teknisyen -->
                <div class="mb-5">
                    <label for="technician_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Teknisyen
                    </label>
                    <select id="technician_id" name="technician_id" class="w-full rounded-lg border-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-colors">
                        <option value="">-- Teknisyen Seçin --</option>
                        <?php foreach ($technicians as $tech): ?>
                            <option value="<?= $tech['technician_id'] ?>">
                                <?= htmlspecialchars($tech['full_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Form Bölüm 4: Açıklama ve Ek Dosyalar -->
            <div class="py-6">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Açıklama ve Ekler</h3>
                
                <!-- Açıklama - Quill Editor -->
                <div class="mb-5">
                    <label for="description-container" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        Açıklama <span class="ml-1 text-red-500">*</span>
                    </label>
                    <div id="description-container" class="border border-gray-300 rounded-lg overflow-hidden shadow-sm">
                        <div id="editor" style="height: 250px;"></div>
                    </div>
                    <input type="hidden" id="description" name="description">
                    <p class="mt-1 text-xs text-gray-500">
                        Problemi detaylı bir şekilde açıklayın. Karşılaştığınız hataları, adımları ve bulgularınızı belirtin.
                    </p>
                </div>
                
                <!-- Dosya ekleme -->
                <div class="mb-5">
                    <label for="attachments" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        Dosya Ekle
                        <span class="ml-1 group relative">
                            <i class="fas fa-info-circle text-gray-400 cursor-help"></i>
                            <div class="hidden group-hover:block absolute z-10 w-48 p-2 mt-1 text-xs bg-gray-800 text-white rounded shadow-lg">
                                PNG, JPG, PDF, DOC, XLS, TXT, ZIP dosyaları kabul edilir (Max: 10MB)
                            </div>
                        </span>
                    </label>
                    <div class="flex items-center justify-center w-full">
                        <label class="flex flex-col w-full h-32 border-2 border-gray-300 border-dashed hover:bg-gray-50 hover:border-indigo-400 rounded-lg cursor-pointer transition-all duration-200">
                            <div class="flex flex-col items-center justify-center pt-7">
                                <i class="fas fa-cloud-upload-alt text-gray-400 text-3xl mb-3"></i>
                                <p class="text-sm text-gray-600 mb-1">
                                    Dosyaları sürükle bırak ya da <span class="text-indigo-600 font-medium">gözat</span>
                                </p>
                                <p class="text-xs text-gray-500">
                                    Maximum 10MB - PNG, JPG, PDF, DOC, XLS
                                </p>
                            </div>
                            <input type="file" id="attachments" name="attachments[]" multiple class="opacity-0" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar">
                        </label>
                    </div>
                    <div id="file-list" class="mt-2 space-y-2"></div>
                    <p class="mt-2 text-xs text-gray-500 flex items-center">
                        <i class="fas fa-info-circle mr-1 text-indigo-500"></i>
                        Dosyalarınızın toplam boyutu 10MB'ı geçmemelidir. Yalnızca izin verilen dosya türleri kabul edilecektir.
                    </p>
                </div>
            </div>
            
            <!-- Form butonları -->
            <div class="pt-6 flex justify-end space-x-3">
                <a href="<?= BASE_URL ?>/tickets.php" class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-300 flex items-center">
                    <i class="fas fa-times mr-2"></i>İptal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-lg bg-gradient-to-r from-indigo-600 to-purple-600 text-white hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 shadow-md transition-all duration-300 flex items-center">
                    <i class="fas fa-save mr-2"></i>Ticket Oluştur
                </button>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript kodu -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quill Editor başlatma
    var quill = new Quill('#editor', {
        theme: 'snow',
        placeholder: 'Ticket açıklamanızı buraya yazın...',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline', 'strike'],
                ['blockquote', 'code-block'],
                [{ 'header': 1 }, { 'header': 2 }],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'color': [] }, { 'background': [] }],
                ['clean']
            ]
        }
    });
    
    // Form gönderilmeden önce Quill içeriğini gizli alana aktar
    var form = document.getElementById('ticket-form');
    var descriptionInput = document.getElementById('description');
    
    form.onsubmit = function() {
        descriptionInput.value = quill.root.innerHTML;
        return true;
    };
    
    // Dosya yükleme önizlemesi
    var attachmentInput = document.getElementById('attachments');
    var fileList = document.getElementById('file-list');
    
    attachmentInput.addEventListener('change', function(e) {
        fileList.innerHTML = '';
        
        for (var i = 0; i < this.files.length; i++) {
            var file = this.files[i];
            var fileSize = (file.size / 1024).toFixed(2) + ' KB';
            
            var fileItem = document.createElement('div');
            fileItem.className = 'flex items-center p-2 rounded-lg bg-gray-50 border border-gray-200';
            
            var icon = '';
            if (file.type.match('image.*')) {
                icon = '<i class="fas fa-file-image text-blue-500 mr-2"></i>';
            } else if (file.type.match('application/pdf')) {
                icon = '<i class="fas fa-file-pdf text-red-500 mr-2"></i>';
            } else if (file.type.match('application/msword') || file.type.match('application/vnd.openxmlformats-officedocument.wordprocessingml.document')) {
                icon = '<i class="fas fa-file-word text-blue-600 mr-2"></i>';
            } else if (file.type.match('application/vnd.ms-excel') || file.type.match('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')) {
                icon = '<i class="fas fa-file-excel text-green-600 mr-2"></i>';
            } else {
                icon = '<i class="fas fa-file text-gray-500 mr-2"></i>';
            }
            
            fileItem.innerHTML = `
                <div class="flex-1 flex items-center">
                    ${icon}
                    <span class="text-sm truncate">${file.name}</span>
                </div>
                <div class="text-xs text-gray-500 ml-2">${fileSize}</div>
                <button type="button" class="ml-2 text-gray-400 hover:text-red-500" onclick="this.parentNode.remove();">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            fileList.appendChild(fileItem);
        }
    });
    
    // Kategori değiştiğinde alt kategorileri getir
    var categorySelect = document.getElementById('category_id');
    var subcategorySelect = document.getElementById('subcategory_id');
    
    categorySelect.addEventListener('change', function() {
        var categoryId = this.value;
        
        if (!categoryId) {
            subcategorySelect.innerHTML = '<option value="">-- Önce Kategori Seçin --</option>';
            subcategorySelect.disabled = true;
            return;
        }
        
        // AJAX ile alt kategorileri getir
        subcategorySelect.disabled = true;
        subcategorySelect.innerHTML = '<option value="">Yükleniyor...</option>';
        
        fetch(BASE_URL + '/api/tickets/subcategories.php?category_id=' + categoryId)
            .then(response => response.json())
            .then(data => {
                subcategorySelect.innerHTML = '<option value="">-- Alt Kategori Seçin --</option>';
                
                if (data.length === 0) {
                    subcategorySelect.innerHTML = '<option value="">Alt kategori bulunamadı</option>';
                    subcategorySelect.disabled = true;
                    return;
                }
                
                data.forEach(function(subcategory) {
                    var option = document.createElement('option');
                    option.value = subcategory.id;
                    option.textContent = subcategory.name;
                    subcategorySelect.appendChild(option);
                });
                
                subcategorySelect.disabled = false;
            })
            .catch(error => {
                console.error('Alt kategoriler alınırken hata oluştu:', error);
                subcategorySelect.innerHTML = '<option value="">Hata oluştu</option>';
                subcategorySelect.disabled = true;
            });
    });
});
</script>
