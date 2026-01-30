<?php
// Get pagination parameters from URL or use defaults
$itemsPerPage = isset($_GET['ref_per_page']) ? (int)$_GET['ref_per_page'] : 10;
$currentPage = isset($_GET['ref_page']) ? max(1, (int)$_GET['ref_page']) : 1;
$totalPages = ceil($referralCount / $itemsPerPage);
$offset = ($currentPage - 1) * $itemsPerPage;
?>


<!-- Modal -->
<div class="modal fade" id="referralsModal" tabindex="-1" role="dialog" aria-labelledby="referralsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="referralsModalLabel">
                    <i class="fa fa-users mr-2"></i>Рефералы первого уровня
                    <?php if($referralCount > 0): ?>
                    <span class="badge badge-light ml-2"><?= $referralCount ?></span>
                    <?php endif; ?>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="modal-body" id="referralsModalBody">
                <!-- Content will be loaded dynamically -->
                <div class="text-center py-3">
                    <div class="spinner-border text-info" role="status">
                        <span class="sr-only">Загрузка...</span>
                    </div>
                    <p class="mt-2 text-muted">Загрузка данных...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
$(document).ready(function() {
    // Load referrals data when modal opens
    $('#referralsModal').on('show.bs.modal', function() {
        loadReferralsData(1, 10); // Load first page with 10 items
    });
});

// Function to load referrals data via AJAX
function loadReferralsData(page = 1, perPage = 10) {
    const modalBody = document.getElementById('referralsModalBody');
    
    // Show loading spinner
    modalBody.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-info" role="status">
                <span class="sr-only">Загрузка...</span>
            </div>
            <p class="mt-2 text-muted">Загрузка данных...</p>
        </div>
    `;
    
    // Make AJAX request
    $.ajax({
        url: 'cabinet2_referals.ajax.php', // You'll need to create this file
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            klid: '<?= $klid ?>' // Pass the affiliate ID
        },
success: function(response) {
    modalBody.innerHTML = response;
    
    // Re-attach event listeners
    attachReferralEventListeners();
    initializeReferralsView();
    
    // ADD THIS: Reinitialize view toggle for the loaded content
    setTimeout(function() {
        const savedView = localStorage.getItem('referralsView') || (window.innerWidth < 992 ? 'cards' : 'table');
        toggleView(savedView);
    }, 100);
},        
        error: function() {
            modalBody.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fa fa-exclamation-triangle mr-2"></i>
                    Ошибка загрузки данных. Попробуйте обновить страницу.
                </div>
            `;
        }
    });
}

// Function to change page via AJAX
function changeReferralsPage(page) {
    const perPage = document.getElementById('itemsPerPage')?.value || 10;
    loadReferralsData(page, perPage);
    
    // Scroll to top of modal content
    const modalBody = document.getElementById('referralsModalBody');
    modalBody.scrollTop = 0;
}

// Function to change items per page
function changeItemsPerPage(count) {
    loadReferralsData(1, count);
}

// Function to attach event listeners after AJAX load
function attachReferralEventListeners() {
    // View toggle buttons
    document.querySelectorAll('[data-view]').forEach(btn => {
        btn.addEventListener('click', function() {
            const viewType = this.getAttribute('data-view');
            toggleView(viewType);
        });
    });
    
    // Items per page selector
    const itemsPerPageSelect = document.getElementById('itemsPerPage');
    if (itemsPerPageSelect) {
        itemsPerPageSelect.addEventListener('change', function() {
            changeItemsPerPage(this.value);
        });
    }
}

// View toggle functionality
function toggleView(viewType) {
    const modalBody = document.getElementById('referralsModalBody');
    if (!modalBody) return;
    
    // Update buttons
    modalBody.querySelectorAll('[data-view]').forEach(btn => {
        btn.classList.toggle('active', btn.getAttribute('data-view') === viewType);
    });
    
    const tableView = modalBody.querySelector('#tableView');
    const cardsView = modalBody.querySelector('#cardsView');
    
    if (tableView && cardsView) {
        // Hide both first
        tableView.style.display = 'none';
        cardsView.style.display = 'none';
        
        // Show selected view
        if (viewType === 'table') {
            tableView.style.display = 'block';
        } else {
            cardsView.style.display = 'block';
        }
        
        console.log('View set to:', viewType);
    }
    
    // Save preference
    localStorage.setItem('referralsView', viewType);
}

// Initialize view based on screen size or saved preference
function initializeReferralsView() {
    const savedView = localStorage.getItem('referralsView');
    const isMobile = window.innerWidth < 992;
    
    if (savedView) {
        toggleView(savedView);
    } else {
        // Default: table on desktop, cards on mobile
        toggleView(isMobile ? 'cards' : 'table');
    }
}

// Function to refresh referrals data
function refreshReferrals() {
    const currentPage = document.getElementById('currentPage')?.value || 1;
    const perPage = document.getElementById('itemsPerPage')?.value || 10;
    loadReferralsData(currentPage, perPage);
    
    showNotification('Данные обновлены', 'success');
}

// Function to show notification
function showNotification(message, type = 'info') {
    // Remove existing notifications
    $('.notification-alert').remove();
    
    // Create new notification
    const notification = document.createElement('div');
    notification.className = `notification-alert alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1060;
        min-width: 300px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    `;
    notification.innerHTML = `
        ${message}
        <button type="button" class="close" onclick="$(this).parent().fadeOut(300, function() { $(this).remove(); })">
            <span>&times;</span>
        </button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remove after 3 seconds
    setTimeout(() => {
        $(notification).fadeOut(300, function() {
            $(this).remove();
        });
    }, 3000);
}

// Function to print table
function printReferralsTable() {
    const currentView = localStorage.getItem('referralsView') || (window.innerWidth < 992 ? 'cards' : 'table');
    const viewContent = document.getElementById(currentView + 'View')?.innerHTML;
    
    if (!viewContent) {
        showNotification('Нет данных для печати', 'warning');
        return;
    }
    
    const printWindow = window.open('', '_blank', 'width=800,height=600');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Рефералы первого уровня - Печать</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .print-header { text-align: center; margin-bottom: 30px; }
                .print-header h3 { color: #17a2b8; }
                .print-date { text-align: right; margin-bottom: 20px; font-size: 12px; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                th { background: #f8f9fa; padding: 10px; border: 1px solid #dee2e6; font-weight: bold; }
                td { padding: 8px; border: 1px solid #dee2e6; }
                .text-center { text-align: center; }
                .card { border: 1px solid #dee2e6; margin-bottom: 10px; padding: 10px; }
                @media print {
                    .no-print { display: none; }
                    body { margin: 0; }
                }
            </style>
        </head>
        <body>
            <div class="print-header">
                <h3>Рефералы первого уровня</h3>
                <p>Отчет сформирован: ${new Date().toLocaleDateString('ru-RU')}</p>
            </div>
            ${viewContent}
            <div class="print-date">
                <p>Страница распечатана: ${new Date().toLocaleString('ru-RU')}</p>
            </div>
            <script>
                window.onload = function() {
                    window.print();
                    window.onafterprint = function() {
                        window.close();
                    };
                };
            <\/script>
        </body>
        </html>
    `);
    printWindow.document.close();
}

// Function to export to CSV
function exportReferralsToCSV() {
    // You'll need to implement this with server-side CSV generation
    // or extract data from the current view
    showNotification('Экспорт CSV будет реализован позже', 'info');
}

// Function to load referrals data with filters
function loadReferralsData(page = 1, perPage = 10, filters = {}) {
    const modalBody = document.getElementById('referralsModalBody');
    
    // Show loading spinner
    modalBody.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-info" role="status">
                <span class="sr-only">Загрузка...</span>
            </div>
            <p class="mt-2 text-muted">Загрузка данных...</p>
        </div>
    `;
    
    // Prepare data object
    const data = {
        page: page,
        per_page: perPage,
        klid: '<?= $klid ?>'
    };
    
    // Add filters if provided
    if (filters.filter_name) data.filter_name = filters.filter_name;
    if (filters.filter_phone) data.filter_phone = filters.filter_phone;
    if (filters.filter_email) data.filter_email = filters.filter_email;
    
    // Make AJAX request
    $.ajax({
        url: 'cabinet2_referals.ajax.php',
        type: 'GET',
        data: data,
        success: function(response) {
            modalBody.innerHTML = response;
            
            // Re-attach event listeners
            attachReferralEventListeners();
            initializeReferralsView();
            
            // Reinitialize view toggle for the loaded content
            setTimeout(function() {
                const savedView = localStorage.getItem('referralsView') || (window.innerWidth < 992 ? 'cards' : 'table');
                toggleView(savedView);
            }, 100);
        },
        error: function() {
            modalBody.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fa fa-exclamation-triangle mr-2"></i>
                    Ошибка загрузки данных. Попробуйте обновить страницу.
                </div>
            `;
        }
    });
}

// Function to apply filters
function applyFilters(event) {
    if (event) {
        event.preventDefault(); // Prevent form submission
    }
    
    // Get filter values
    const filterName = document.getElementById('filterName')?.value || '';
    const filterPhone = document.getElementById('filterPhone')?.value || '';
    const filterEmail = document.getElementById('filterEmail')?.value || '';
    
    // Load data with filters (start from page 1)
    loadReferralsData(1, 10, {
        filter_name: filterName,
        filter_phone: filterPhone,
        filter_email: filterEmail
    });
    
    // Store filters for future pagination
    storeCurrentFilters({
        filter_name: filterName,
        filter_phone: filterPhone,
        filter_email: filterEmail
    });
}

// Function to clear filters
function clearFilters() {
    // Clear form inputs
    if (document.getElementById('filterName')) document.getElementById('filterName').value = '';
    if (document.getElementById('filterPhone')) document.getElementById('filterPhone').value = '';
    if (document.getElementById('filterEmail')) document.getElementById('filterEmail').value = '';
    
    // Load data without filters
    loadReferralsData(1, 10);
    
    // Clear stored filters
    clearStoredFilters();
}

// Function to store current filters in localStorage
function storeCurrentFilters(filters) {
    localStorage.setItem('referralsFilters', JSON.stringify(filters));
}

// Function to get stored filters from localStorage
function getStoredFilters() {
    const stored = localStorage.getItem('referralsFilters');
    return stored ? JSON.parse(stored) : {};
}

// Function to clear stored filters
function clearStoredFilters() {
    localStorage.removeItem('referralsFilters');
}

// Update the changeReferralsPage function to include filters
function changeReferralsPage(page) {
    const perPage = document.getElementById('itemsPerPage')?.value || 10;
    const filters = getStoredFilters();
    loadReferralsData(page, perPage, filters);
    
    // Scroll to top of modal content
    const modalBody = document.getElementById('referralsModalBody');
    modalBody.scrollTop = 0;
}

// Update the changeItemsPerPage function to include filters
function changeItemsPerPage(count) {
    const filters = getStoredFilters();
    loadReferralsData(1, count, filters);
}

// Update the refreshReferrals function to include filters
function refreshReferrals() {
    const currentPage = document.getElementById('currentPage')?.value || 1;
    const perPage = document.getElementById('itemsPerPage')?.value || 10;
    const filters = getStoredFilters();
    loadReferralsData(currentPage, perPage, filters);
    
    showNotification('Данные обновлены', 'success');
}

// Update the modal show event to load with filters if any
$('#referralsModal').on('show.bs.modal', function() {
    // Check if there are stored filters
    const storedFilters = getStoredFilters();
    
    if (Object.keys(storedFilters).length > 0) {
        // Load with stored filters
        loadReferralsData(1, 10, storedFilters);
    } else {
        // Load without filters
        loadReferralsData(1, 10);
    }
});

</script>

<!-- CSS Styles -->
<style>
    .modal-xl {
        max-width: 1200px;
    }
    .page-item.active .page-link {
        background-color: #17a2b8;
        border-color: #17a2b8;
    }
    .btn-outline-info.active {
        background-color: #17a2b8;
        color: white;
    }
</style>
