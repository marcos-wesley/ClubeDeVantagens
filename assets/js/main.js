/**
 * ANETI Clube de Vantagens - Main JavaScript
 */

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

/**
 * Initialize the application
 */
function initializeApp() {
    // Initialize tooltips
    initializeTooltips();
    
    // Initialize form validations
    initializeFormValidations();
    
    // Initialize search functionality
    initializeSearch();
    
    // Initialize animations
    initializeAnimations();
    
    // Initialize notifications
    initializeNotifications();
}

/**
 * Initialize Bootstrap tooltips
 */
function initializeTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

/**
 * Initialize form validations
 */
function initializeFormValidations() {
    // Bootstrap form validation
    const forms = document.querySelectorAll('.needs-validation');
    
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
    
    // Custom validations
    initializeCustomValidations();
}

/**
 * Initialize custom form validations
 */
function initializeCustomValidations() {
    // CNPJ validation
    const cnpjInputs = document.querySelectorAll('input[name="cnpj"]');
    cnpjInputs.forEach(function(input) {
        input.addEventListener('blur', function() {
            validateCNPJ(this);
        });
    });
    
    // Email validation
    const emailInputs = document.querySelectorAll('input[type="email"]');
    emailInputs.forEach(function(input) {
        input.addEventListener('blur', function() {
            validateEmail(this);
        });
    });
    
    // Phone validation
    const phoneInputs = document.querySelectorAll('input[name="telefone"]');
    phoneInputs.forEach(function(input) {
        input.addEventListener('blur', function() {
            validatePhone(this);
        });
    });
}

/**
 * Validate CNPJ
 */
function validateCNPJ(input) {
    const cnpj = input.value.replace(/\D/g, '');
    
    if (cnpj.length !== 14) {
        setFieldError(input, 'CNPJ deve ter 14 dígitos');
        return false;
    }
    
    // Basic CNPJ validation (simplified)
    if (!/^\d{14}$/.test(cnpj)) {
        setFieldError(input, 'CNPJ inválido');
        return false;
    }
    
    clearFieldError(input);
    return true;
}

/**
 * Validate email
 */
function validateEmail(input) {
    const email = input.value;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (email && !emailRegex.test(email)) {
        setFieldError(input, 'E-mail inválido');
        return false;
    }
    
    clearFieldError(input);
    return true;
}

/**
 * Validate phone
 */
function validatePhone(input) {
    const phone = input.value.replace(/\D/g, '');
    
    if (phone && phone.length < 10) {
        setFieldError(input, 'Telefone deve ter pelo menos 10 dígitos');
        return false;
    }
    
    clearFieldError(input);
    return true;
}

/**
 * Set field error
 */
function setFieldError(input, message) {
    input.classList.add('is-invalid');
    input.classList.remove('is-valid');
    
    let feedback = input.parentNode.querySelector('.invalid-feedback');
    if (!feedback) {
        feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        input.parentNode.appendChild(feedback);
    }
    feedback.textContent = message;
}

/**
 * Clear field error
 */
function clearFieldError(input) {
    input.classList.remove('is-invalid');
    input.classList.add('is-valid');
    
    const feedback = input.parentNode.querySelector('.invalid-feedback');
    if (feedback) {
        feedback.remove();
    }
}

/**
 * Initialize search functionality
 */
function initializeSearch() {
    const searchForms = document.querySelectorAll('.search-form');
    
    searchForms.forEach(function(form) {
        const input = form.querySelector('input[name="q"]');
        if (input) {
            // Add search suggestions (could be enhanced with AJAX)
            input.addEventListener('input', function() {
                handleSearchInput(this);
            });
        }
    });
}

/**
 * Handle search input
 */
function handleSearchInput(input) {
    const query = input.value.trim();
    
    if (query.length < 2) {
        hideSearchSuggestions();
        return;
    }
    
    // In a real implementation, this would make an AJAX call
    // For now, just show a simple message
    console.log('Searching for:', query);
}

/**
 * Hide search suggestions
 */
function hideSearchSuggestions() {
    const suggestions = document.querySelector('.search-suggestions');
    if (suggestions) {
        suggestions.style.display = 'none';
    }
}

/**
 * Initialize animations
 */
function initializeAnimations() {
    // Fade in animation for cards
    const cards = document.querySelectorAll('.company-card, .company-card-featured');
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, {
        threshold: 0.1
    });
    
    cards.forEach(function(card, index) {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
        observer.observe(card);
    });
}

/**
 * Initialize notifications
 */
function initializeNotifications() {
    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert-dismissible');
    
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const closeBtn = alert.querySelector('.btn-close');
            if (closeBtn) {
                closeBtn.click();
            }
        }, 5000);
    });
}

/**
 * Show loading state
 */
function showLoading(element) {
    element.classList.add('loading');
    element.style.pointerEvents = 'none';
    
    // Add spinner if not exists
    let spinner = element.querySelector('.spinner-border');
    if (!spinner) {
        spinner = document.createElement('div');
        spinner.className = 'spinner-border spinner-border-sm me-2';
        spinner.setAttribute('role', 'status');
        element.insertBefore(spinner, element.firstChild);
    }
}

/**
 * Hide loading state
 */
function hideLoading(element) {
    element.classList.remove('loading');
    element.style.pointerEvents = 'auto';
    
    const spinner = element.querySelector('.spinner-border');
    if (spinner) {
        spinner.remove();
    }
}

/**
 * Show notification
 */
function showNotification(message, type = 'info', duration = 3000) {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '9999';
    notification.style.minWidth = '300px';
    
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after duration
    setTimeout(function() {
        if (notification.parentNode) {
            notification.remove();
        }
    }, duration);
}

/**
 * Confirm action
 */
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

/**
 * Format currency (Brazilian Real)
 */
function formatCurrency(value) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value);
}

/**
 * Format date (Brazilian format)
 */
function formatDate(date) {
    return new Intl.DateTimeFormat('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(new Date(date));
}

/**
 * Copy text to clipboard
 */
function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(function() {
            showNotification('Texto copiado para a área de transferência!', 'success');
        }).catch(function() {
            fallbackCopyToClipboard(text);
        });
    } else {
        fallbackCopyToClipboard(text);
    }
}

/**
 * Fallback copy to clipboard
 */
function fallbackCopyToClipboard(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.top = '0';
    textArea.style.left = '0';
    textArea.style.width = '2em';
    textArea.style.height = '2em';
    textArea.style.padding = '0';
    textArea.style.border = 'none';
    textArea.style.outline = 'none';
    textArea.style.boxShadow = 'none';
    textArea.style.background = 'transparent';
    
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        document.execCommand('copy');
        showNotification('Texto copiado para a área de transferência!', 'success');
    } catch (err) {
        showNotification('Erro ao copiar texto', 'danger');
    }
    
    document.body.removeChild(textArea);
}

/**
 * Debounce function
 */
function debounce(func, wait, immediate) {
    let timeout;
    return function executedFunction() {
        const context = this;
        const args = arguments;
        const later = function() {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        const callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
}

/**
 * Throttle function
 */
function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    }
}

/**
 * Smooth scroll to element
 */
function scrollToElement(elementId, offset = 0) {
    const element = document.getElementById(elementId);
    if (element) {
        const elementPosition = element.getBoundingClientRect().top;
        const offsetPosition = elementPosition + window.pageYOffset - offset;
        
        window.scrollTo({
            top: offsetPosition,
            behavior: 'smooth'
        });
    }
}

/**
 * Check if element is in viewport
 */
function isInViewport(element) {
    const rect = element.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

/**
 * Generate QR Code (simplified version)
 * In production, use a proper QR code library
 */
function generateQRCode(text, container) {
    // This is a placeholder - in production, use QRCode.js or similar
    container.innerHTML = `
        <div class="qr-placeholder">
            <i class="fas fa-qrcode fa-3x"></i>
            <div>QR Code</div>
            <small>${text}</small>
        </div>
    `;
}

/**
 * Lazy load images
 */
function initializeLazyLoading() {
    const images = document.querySelectorAll('img[data-src]');
    
    const imageObserver = new IntersectionObserver(function(entries, observer) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                imageObserver.unobserve(img);
            }
        });
    });
    
    images.forEach(function(img) {
        imageObserver.observe(img);
    });
}

/**
 * Handle file upload preview
 */
function handleFileUpload(input, previewContainer) {
    input.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            showNotification('Tipo de arquivo não permitido. Use JPG, PNG ou GIF.', 'danger');
            input.value = '';
            return;
        }
        
        // Validate file size (5MB max)
        const maxSize = 5 * 1024 * 1024;
        if (file.size > maxSize) {
            showNotification('Arquivo muito grande. Tamanho máximo: 5MB.', 'danger');
            input.value = '';
            return;
        }
        
        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            if (previewContainer) {
                previewContainer.innerHTML = `
                    <img src="${e.target.result}" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                    <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="clearFilePreview('${input.id}', '${previewContainer.id}')">
                        <i class="fas fa-times"></i> Remover
                    </button>
                `;
            }
        };
        reader.readAsDataURL(file);
    });
}

/**
 * Clear file preview
 */
function clearFilePreview(inputId, previewId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    
    if (input) input.value = '';
    if (preview) preview.innerHTML = '';
}

/**
 * Initialize file upload handlers
 */
function initializeFileUploads() {
    const fileInputs = document.querySelectorAll('input[type="file"]');
    
    fileInputs.forEach(function(input) {
        const previewId = input.dataset.preview;
        if (previewId) {
            const previewContainer = document.getElementById(previewId);
            if (previewContainer) {
                handleFileUpload(input, previewContainer);
            }
        }
    });
}

/**
 * Mobile menu toggle
 */
function initializeMobileMenu() {
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('.navbar-collapse');
    
    if (navbarToggler && navbarCollapse) {
        navbarToggler.addEventListener('click', function() {
            navbarCollapse.classList.toggle('show');
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!navbarToggler.contains(e.target) && !navbarCollapse.contains(e.target)) {
                navbarCollapse.classList.remove('show');
            }
        });
    }
}

/**
 * Initialize on page load
 */
window.addEventListener('load', function() {
    initializeLazyLoading();
    initializeFileUploads();
    initializeMobileMenu();
});

// Export functions for global use
window.ANETI = {
    showLoading,
    hideLoading,
    showNotification,
    confirmAction,
    formatCurrency,
    formatDate,
    copyToClipboard,
    scrollToElement,
    generateQRCode,
    debounce,
    throttle
};
