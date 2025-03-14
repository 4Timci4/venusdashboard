/**
 * Venus IT Help Desk - Ana JavaScript Dosyası
 */

// DOM yüklendikten sonra çalıştır
document.addEventListener('DOMContentLoaded', function() {
    // Mobil menü açma/kapama
    setupMobileMenu();
    
    // Kullanıcı dropdown menüsü açma/kapama
    setupUserMenu();
    
    // Bildirim kapatma
    setupAlertDismiss();
    
    // Form doğrulama
    validateForms();
    
    // Ticket detayları için sekmeler
    setupTicketTabs();
    
    // Otomatik kapanan bildirimler
    setupAutoClosingAlerts();

    // Tooltip'leri etkinleştir
    setupTooltips();
});

/**
 * Mobil menü açma/kapama fonksiyonu
 */
function setupMobileMenu() {
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
        
        // Dışarı tıklanınca menüyü kapat
        document.addEventListener('click', function(event) {
            if (!mobileMenuButton.contains(event.target) && !mobileMenu.contains(event.target)) {
                mobileMenu.classList.add('hidden');
            }
        });
    }
}

/**
 * Kullanıcı menüsü açma/kapama fonksiyonu
 */
function setupUserMenu() {
    const userMenuButton = document.getElementById('user-menu-button');
    const userMenu = document.getElementById('user-menu');
    
    if (userMenuButton && userMenu) {
        userMenuButton.addEventListener('click', function() {
            userMenu.classList.toggle('hidden');
        });
        
        // Dışarı tıklanınca menüyü kapat
        document.addEventListener('click', function(event) {
            if (!userMenuButton.contains(event.target) && !userMenu.contains(event.target)) {
                userMenu.classList.add('hidden');
            }
        });
    }
}

/**
 * Kapatılabilir bildirimleri ayarlar
 */
function setupAlertDismiss() {
    const alertCloseButtons = document.querySelectorAll('.alert-close');
    
    alertCloseButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const alert = button.closest('.alert');
            if (alert) {
                alert.classList.add('opacity-0');
                setTimeout(function() {
                    alert.remove();
                }, 300);
            }
        });
    });
}

/**
 * Form doğrulama fonksiyonu
 */
function validateForms() {
    const forms = document.querySelectorAll('form[data-validate="true"]');
    
    forms.forEach(function(form) {
        form.addEventListener('submit', function(event) {
            let isValid = true;
            
            // Tüm zorunlu alanları kontrol et
            const requiredFields = form.querySelectorAll('[required]');
            requiredFields.forEach(function(field) {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('border-red-500');
                    
                    // Hata mesajı göster
                    const errorSpan = field.nextElementSibling;
                    if (errorSpan && errorSpan.classList.contains('error-message')) {
                        errorSpan.textContent = 'Bu alan zorunludur.';
                        errorSpan.classList.remove('hidden');
                    } else {
                        const span = document.createElement('span');
                        span.textContent = 'Bu alan zorunludur.';
                        span.className = 'text-red-500 text-xs mt-1 error-message';
                        field.parentNode.insertBefore(span, field.nextSibling);
                    }
                } else {
                    field.classList.remove('border-red-500');
                    
                    // Hata mesajını gizle
                    const errorSpan = field.nextElementSibling;
                    if (errorSpan && errorSpan.classList.contains('error-message')) {
                        errorSpan.classList.add('hidden');
                    }
                }
            });
            
            // E-posta doğrulama
            const emailFields = form.querySelectorAll('input[type="email"]');
            emailFields.forEach(function(field) {
                if (field.value.trim() && !isValidEmail(field.value)) {
                    isValid = false;
                    field.classList.add('border-red-500');
                    
                    // Hata mesajı göster
                    const errorSpan = field.nextElementSibling;
                    if (errorSpan && errorSpan.classList.contains('error-message')) {
                        errorSpan.textContent = 'Geçerli bir e-posta adresi giriniz.';
                        errorSpan.classList.remove('hidden');
                    } else {
                        const span = document.createElement('span');
                        span.textContent = 'Geçerli bir e-posta adresi giriniz.';
                        span.className = 'text-red-500 text-xs mt-1 error-message';
                        field.parentNode.insertBefore(span, field.nextSibling);
                    }
                }
            });
            
            // Şifre doğrulama
            const passwordFields = form.querySelectorAll('input[data-password-validate="true"]');
            passwordFields.forEach(function(field) {
                if (field.value.trim() && field.value.length < 8) {
                    isValid = false;
                    field.classList.add('border-red-500');
                    
                    // Hata mesajı göster
                    const errorSpan = field.nextElementSibling;
                    if (errorSpan && errorSpan.classList.contains('error-message')) {
                        errorSpan.textContent = 'Şifre en az 8 karakter olmalıdır.';
                        errorSpan.classList.remove('hidden');
                    } else {
                        const span = document.createElement('span');
                        span.textContent = 'Şifre en az 8 karakter olmalıdır.';
                        span.className = 'text-red-500 text-xs mt-1 error-message';
                        field.parentNode.insertBefore(span, field.nextSibling);
                    }
                }
            });
            
            // Şifre eşleşme kontrolü
            const passwordConfirmFields = form.querySelectorAll('input[data-password-confirm]');
            passwordConfirmFields.forEach(function(field) {
                const passwordField = document.getElementById(field.getAttribute('data-password-confirm'));
                
                if (passwordField && field.value !== passwordField.value) {
                    isValid = false;
                    field.classList.add('border-red-500');
                    
                    // Hata mesajı göster
                    const errorSpan = field.nextElementSibling;
                    if (errorSpan && errorSpan.classList.contains('error-message')) {
                        errorSpan.textContent = 'Şifreler eşleşmiyor.';
                        errorSpan.classList.remove('hidden');
                    } else {
                        const span = document.createElement('span');
                        span.textContent = 'Şifreler eşleşmiyor.';
                        span.className = 'text-red-500 text-xs mt-1 error-message';
                        field.parentNode.insertBefore(span, field.nextSibling);
                    }
                }
            });
            
            if (!isValid) {
                event.preventDefault();
            }
        });
    });
}

/**
 * E-posta geçerliliğini kontrol eder
 */
function isValidEmail(email) {
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}

/**
 * Ticket detay sekmelerini ayarlar
 */
function setupTicketTabs() {
    const tabButtons = document.querySelectorAll('[data-tab-target]');
    const tabContents = document.querySelectorAll('[data-tab-content]');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const target = button.dataset.tabTarget;
            
            // Sekmeleri gizle
            tabContents.forEach(tabContent => {
                tabContent.classList.add('hidden');
            });
            
            // Sekme butonlarının aktif durumunu güncelle
            tabButtons.forEach(btn => {
                btn.classList.remove('border-blue-500', 'text-blue-600');
                btn.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
            });
            
            // Seçili sekmeyi göster
            document.querySelector(`[data-tab-content="${target}"]`).classList.remove('hidden');
            
            // Seçili sekme butonunu aktifleştir
            button.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
            button.classList.add('border-blue-500', 'text-blue-600');
        });
    });
}

/**
 * Otomatik kapanan bildirimleri ayarlar
 */
function setupAutoClosingAlerts() {
    const autoCloseAlerts = document.querySelectorAll('.alert[data-auto-close]');
    
    autoCloseAlerts.forEach(function(alert) {
        const delay = parseInt(alert.dataset.autoClose) || 5000;
        
        setTimeout(function() {
            alert.classList.add('opacity-0');
            setTimeout(function() {
                alert.remove();
            }, 300);
        }, delay);
    });
}

/**
 * Tooltip'leri etkinleştirir
 */
function setupTooltips() {
    const tooltips = document.querySelectorAll('.tooltip');
    
    tooltips.forEach(function(tooltip) {
        const tooltipText = tooltip.querySelector('.tooltip-text');
        
        if (tooltipText) {
            // Mobil cihazlarda tooltip gösterme
            tooltip.addEventListener('touchstart', function(e) {
                e.preventDefault();
                tooltipText.style.visibility = 'visible';
                tooltipText.style.opacity = '1';
                
                setTimeout(function() {
                    tooltipText.style.visibility = 'hidden';
                    tooltipText.style.opacity = '0';
                }, 2000);
            });
        }
    });
}

/**
 * AJAX Post isteği gönderir
 */
function ajaxPost(url, data, successCallback, errorCallback) {
    // CSRF token'ı al
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    // CSRF token varsa ekle
    if (csrfToken) {
        data.csrf_token = csrfToken;
    }
    
    // Form verisi oluştur
    const formData = new FormData();
    for (const key in data) {
        formData.append(key, data[key]);
    }
    
    // AJAX isteği gönder
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (typeof successCallback === 'function') {
            successCallback(data);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (typeof errorCallback === 'function') {
            errorCallback(error);
        }
    });
}

/**
 * AJAX Get isteği gönderir
 */
function ajaxGet(url, successCallback, errorCallback) {
    fetch(url, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (typeof successCallback === 'function') {
            successCallback(data);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (typeof errorCallback === 'function') {
            errorCallback(error);
        }
    });
}

/**
 * SweetAlert2 ile bildirim gösterir
 */
function showAlert(type, title, text, confirmButtonText = 'Tamam') {
    if (typeof Swal !== 'undefined') {
        return Swal.fire({
            icon: type,
            title: title,
            text: text,
            confirmButtonText: confirmButtonText
        });
    } else {
        alert(title + '\n\n' + text);
    }
}

/**
 * SweetAlert2 ile onay kutusu gösterir
 */
function showConfirm(title, text, confirmButtonText = 'Evet', cancelButtonText = 'Hayır') {
    if (typeof Swal !== 'undefined') {
        return Swal.fire({
            icon: 'question',
            title: title,
            text: text,
            showCancelButton: true,
            confirmButtonText: confirmButtonText,
            cancelButtonText: cancelButtonText
        });
    } else {
        return { then: (callback) => callback({ isConfirmed: confirm(title + '\n\n' + text) }) };
    }
}

/**
 * Tarih formatını değiştirir (YYYY-MM-DD -> DD.MM.YYYY)
 */
function formatDate(dateString) {
    if (!dateString) return '';
    
    const date = new Date(dateString);
    return [
        date.getDate().toString().padStart(2, '0'),
        (date.getMonth() + 1).toString().padStart(2, '0'),
        date.getFullYear()
    ].join('.');
}

/**
 * Dosya boyutunu formatlar (byte -> KB, MB, GB)
 */
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

/**
 * Metin içeriğini belirli bir uzunlukta keser ve sonuna '...' ekler
 */
function truncateText(text, maxLength) {
    return text.length > maxLength ? text.substr(0, maxLength) + '...' : text;
}
