<!-- Ticket Oluşturma Formu -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-2">Yeni Ticket Oluştur</h1>
    <p class="text-gray-600">IT destek talebi oluşturmak için aşağıdaki formu doldurun</p>
</div>

<!-- Form Kartı -->
<div class="bg-white rounded-lg shadow-lg overflow-hidden">
    <div class="border-b border-gray-200 bg-gray-50 p-4">
        <h2 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-ticket-alt mr-2 text-blue-500"></i>Ticket Bilgileri
        </h2>
    </div>
    
    <div class="p-6">
        <!-- Hata mesajları -->
        <?php if (isset($errors) && !empty($errors)): ?>
            <div class="mb-6 bg-red-100 border border-red-200 text-red-700 p-4 rounded-lg">
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
        <form id="ticket-form" action="<?= BASE_URL ?>/ticket/store.php" method="POST" enctype="multipart/form-data">
            <!-- CSRF token -->
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <!-- Sol kolon -->
                <div>
                    <!-- Kullanıcı seçimi -->
                    <div class="mb-5">
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Talep Sahibi <span class="text-red-500">*</span>
                        </label>
                        <?php if (AuthHelper::isAdmin()): ?>
                            <select id="user_id" name="user_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">-- Kullanıcı Seçin --</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= $user['id'] ?>" <?= (AuthHelper::getUserId() == $user['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($user['full_name']) ?> (<?= htmlspecialchars($user['username']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <input type="hidden" name="user_id" value="<?= AuthHelper::getUserId() ?>">
                            <p class="py-2 px-3 bg-gray-100 rounded-lg">
                                <?= htmlspecialchars(AuthHelper::getUsername()) ?>
                            </p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- İstek türü -->
                    <div class="mb-5">
                        <label for="request_type_id" class="block text-sm font-medium text-gray-700 mb-1">
                            İstek Türü
                        </label>
                        <select id="request_type_id" name="request_type_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">-- İstek Türü Seçin --</option>
                            <?php foreach ($requestTypes as $type): ?>
                                <option value="<?= $type['id'] ?>" style="color: <?= $type['color'] ?>">
                                    <?= htmlspecialchars($type['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Durum -->
                    <div class="mb-5">
                        <label for="status_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Durum
                        </label>
                        <select id="status_id" name="status_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <?php foreach ($statuses as $status): ?>
                                <option value="<?= $status['id'] ?>" 
                                        <?= $status['id'] == 1 ? 'selected' : '' ?> 
                                        style="color: <?= $status['color'] ?>">
                                    <?= htmlspecialchars($status['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Servis -->
                    <div class="mb-5">
                        <label for="service_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Servis
                        </label>
                        <select id="service_id" name="service_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">-- Servis Seçin --</option>
                            <?php foreach ($services as $service): ?>
                                <option value="<?= $service['id'] ?>">
                                    <?= htmlspecialchars($service['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Teknisyen -->
                    <?php if (AuthHelper::isAdmin() || AuthHelper::isTechnician()): ?>
                    <div class="mb-5">
                        <label for="technician_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Teknisyen
                        </label>
                        <select id="technician_id" name="technician_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">-- Teknisyen Seçin --</option>
                            <?php foreach ($technicians as $tech): ?>
                                <option value="<?= $tech['technician_id'] ?>">
                                    <?= htmlspecialchars($tech['full_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Sağ kolon -->
                <div>
                    <!-- Kategori -->
                    <div class="mb-5">
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Hizmet Kategorisi
                        </label>
                        <select id="category_id" name="category_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
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
                        <select id="subcategory_id" name="subcategory_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" disabled>
                            <option value="">-- Önce Kategori Seçin --</option>
                        </select>
                    </div>
                    
                    <!-- Etki -->
                    <div class="mb-5">
                        <label for="impact" class="block text-sm font-medium text-gray-700 mb-1">
                            Etki
                        </label>
                        <select id="impact" name="impact" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
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
                        <input type="text" id="activity" name="activity" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Aktivite bilgisi">
                    </div>
                    
                    <!-- Öncelik -->
                    <div class="mb-5">
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">
                            Öncelik
                        </label>
                        <select id="priority" name="priority" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="1">Düşük</option>
                            <option value="2">Orta</option>
                            <option value="3" selected>Yüksek</option>
                            <option value="4">Kritik</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Konu başlığı -->
            <div class="mb-5">
                <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">
                    Konu <span class="text-red-500">*</span>
                </label>
                <input type="text" id="subject" name="subject" required 
                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" 
                       placeholder="Ticket konusunu girin">
            </div>
            
            <!-- Etki detayları -->
            <div class="mb-5">
                <label for="impact_details" class="block text-sm font-medium text-gray-700 mb-1">
                    Etki Detayları
                </label>
                <textarea id="impact_details" name="impact_details" rows="3" 
                          class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" 
                          placeholder="Etkinin detaylarını açıklayın"></textarea>
            </div>
            
            <!-- Açıklama - Quill Editor -->
            <div class="mb-5">
                <label for="description-container" class="block text-sm font-medium text-gray-700 mb-1">
                    Açıklama <span class="text-red-500">*</span>
                </label>
                <div id="description-container" class="border border-gray-300 rounded-lg overflow-hidden">
                    <div id="editor" style="height: 250px;"></div>
                </div>
                <input type="hidden" id="description" name="description">
            </div>
            
            <!-- Dosya ekleme -->
            <div class="mb-5">
                <label for="attachments" class="block text-sm font-medium text-gray-700 mb-1">
                    Dosya Ekle
                </label>
                <div class="flex items-center justify-center w-full">
                    <label class="flex flex-col w-full h-32 border-2 border-gray-300 border-dashed hover:bg-gray-50 hover:border-gray-400 rounded-lg">
                        <div class="flex flex-col items-center justify-center pt-7">
                            <i class="fas fa-cloud-upload-alt text-gray-400 text-3xl mb-3"></i>
                            <p class="text-sm text-gray-600 mb-1">
                                Dosyaları sürükle bırak ya da <span class="text-blue-600">gözat</span>
                            </p>
                            <p class="text-xs text-gray-500">
                                Maximum 10MB - PNG, JPG, PDF, DOC, XLS
                            </p>
                        </div>
                        <input type="file" id="attachments" name="attachments[]" multiple class="opacity-0" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar">
                    </label>
                </div>
                <div id="file-list" class="mt-2 space-y-2"></div>
            </div>
            
            <!-- Form butonları -->
            <div class="flex justify-end space-x-3 mt-8">
                <a href="<?= BASE_URL ?>/tickets.php" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    <i class="fas fa-times mr-2"></i>İptal
                </a>
                <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <i class="fas fa-save mr-2"></i>Ticket Oluştur
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Quill ve Form Script -->
<?php $extraScripts = '
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
    // Quill editör ayarları
    var quill = new Quill("#editor", {
        theme: "snow",
        modules: {
            toolbar: [
                [{ "header": [1, 2, 3, false] }],
                ["bold", "italic", "underline", "strike"],
                [{ "list": "ordered" }, { "list": "bullet" }],
                [{ "color": [] }, { "background": [] }],
                ["link", "image"],
                ["clean"]
            ]
        },
        placeholder: "Ticket açıklamasını buraya yazın..."
    });
    
    // Form submit edildiğinde Quill içeriğini gizli alana aktar
    document.getElementById("ticket-form").addEventListener("submit", function() {
        var description = document.getElementById("description");
        description.value = quill.root.innerHTML;
    });
    
    // Dosya yükleme önizleme
    document.getElementById("attachments").addEventListener("change", function(e) {
        var fileList = document.getElementById("file-list");
        fileList.innerHTML = "";
        
        if (this.files.length > 0) {
            for (var i = 0; i < this.files.length; i++) {
                var file = this.files[i];
                var fileSize = (file.size / 1024).toFixed(2) + " KB";
                
                var fileItem = document.createElement("div");
                fileItem.className = "flex items-center p-2 bg-gray-50 rounded";
                
                var fileIcon = document.createElement("i");
                fileIcon.className = "fas " + getFileIcon(file.name) + " text-gray-500 mr-2";
                
                var fileName = document.createElement("span");
                fileName.className = "flex-1 truncate text-sm text-gray-700";
                fileName.textContent = file.name;
                
                var fileSizeSpan = document.createElement("span");
                fileSizeSpan.className = "text-xs text-gray-500 ml-2";
                fileSizeSpan.textContent = fileSize;
                
                fileItem.appendChild(fileIcon);
                fileItem.appendChild(fileName);
                fileItem.appendChild(fileSizeSpan);
                
                fileList.appendChild(fileItem);
            }
        }
    });
    
    // Dosya tipine göre ikon belirle
    function getFileIcon(filename) {
        var ext = filename.split(".").pop().toLowerCase();
        
        switch (ext) {
            case "pdf":
                return "fa-file-pdf";
            case "doc":
            case "docx":
                return "fa-file-word";
            case "xls":
            case "xlsx":
                return "fa-file-excel";
            case "zip":
            case "rar":
                return "fa-file-archive";
            case "jpg":
            case "jpeg":
            case "png":
            case "gif":
                return "fa-file-image";
            default:
                return "fa-file";
        }
    }
    
    // Kategori değişince alt kategorileri getir
    document.getElementById("category_id").addEventListener("change", function() {
        var categoryId = this.value;
        var subcategoryDropdown = document.getElementById("subcategory_id");
        
        // Dropdown\'u temizle
        subcategoryDropdown.innerHTML = "";
        subcategoryDropdown.disabled = true;
        
        // Kategori seçilmediyse işlemi durdur
        if (!categoryId) {
            subcategoryDropdown.innerHTML = "<option value=\"\">-- Önce Kategori Seçin --</option>";
            return;
        }
        
        // AJAX ile alt kategorileri getir
        $.ajax({
            url: "' . BASE_URL . '/api/tickets/subcategories.php",
            method: "GET",
            data: { category_id: categoryId },
            dataType: "json",
            success: function(response) {
                if (response.success && response.subcategories.length > 0) {
                    // Alt kategorileri dropdown\'a ekle
                    subcategoryDropdown.innerHTML = "<option value=\"\">-- Alt Kategori Seçin --</option>";
                    
                    response.subcategories.forEach(function(subcategory) {
                        var option = document.createElement("option");
                        option.value = subcategory.id;
                        option.textContent = subcategory.name;
                        subcategoryDropdown.appendChild(option);
                    });
                    
                    subcategoryDropdown.disabled = false;
                } else {
                    subcategoryDropdown.innerHTML = "<option value=\"\">-- Alt Kategori Bulunamadı --</option>";
                }
            },
            error: function() {
                subcategoryDropdown.innerHTML = "<option value=\"\">-- Alt Kategori Yüklenemedi --</option>";
            }
        });
    });
</script>
'; ?>
