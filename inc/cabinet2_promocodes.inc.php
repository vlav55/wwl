<?php
?>


<!-- Bootstrap Modal -->
<div class="modal fade" id="promocodesModal" tabindex="-1" role="dialog" aria-labelledby="promocodesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="promocodesModalLabel">
                    <i class="fa fa-ticket mr-2"></i>Реферальные промокоды
                    <?php if($promoCount > 0): ?>
                    <span class="badge badge-light ml-2"><?=$promoCount?></span>
                    <?php endif; ?>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="modal-body">
                <?php if($promoCount > 0): ?>
                <div class='row'>
                    <?php
                    while ($r = $db->fetch_assoc($res_promocodes)) {
                        $promocode = htmlspecialchars($r['promocode']);
                        $dt2 = date("d.m.Y H:i", $r['tm2']);
                        $cnt = $r['cnt'] == -1 ? "без огр." : $r['cnt'];
                        $descr = htmlspecialchars($base_prices[$r['product_id']]['descr']);
                        $extraProducts = ($r['cnt_pid'] > 1) ? "и еще ".($r['cnt_pid']-1)." продукта(ов)" : "";
                        
                        // Determine price/discount display
                        if($r['price'] > 0) {
                            $priceDisplay = number_format($r['price'], 0, '', ' ').' ₽';
                            $discountDisplay = '-';
                        } elseif($r['discount'] > 0) {
                            $priceDisplay = '-';
                            $discountDisplay = $r['discount'] . ($r['discount'] <= 100 ? '%' : ' ₽');
                        } else {
                            $priceDisplay = '-';
                            $discountDisplay = '-';
                        }
                        
                        $fee_1 = $r['fee_1'] . ($r['fee_1'] > 100 ? ' ₽' : '%');
                        $fee_2 = $r['fee_2'] . ($r['fee_2'] > 100 ? ' ₽' : '%');
                        
                        // Determine card color based on time remaining
                        $daysLeft = floor(($r['tm2'] - time()) / (60 * 60 * 24));
                        $cardClass = 'border-primary'; // default
                        
                        if($daysLeft < 3) {
                            $cardClass = 'border-danger'; // expiring soon
                        } elseif($daysLeft < 7) {
                            $cardClass = 'border-warning'; // expiring in a week
                        }
                        
                        // Expiration badge text
                        $expirationBadge = '';
                        if($daysLeft < 3) {
                            $expirationBadge = '<span class="badge badge-danger ml-2"><i class="fa fa-clock mr-1"></i>Скоро истекает</span>';
                        }
                    ?>
                    <div class='col-md-6 col-lg-4 mb-4'>
                        <div class='card h-100 <?=$cardClass?> shadow-sm'>
                            <div class='card-header bg-white'>
                                <div class='d-flex align-items-center justify-content-between'>
                                    <div class='d-flex align-items-center'>
                                        <i class='fa fa-ticket text-primary mr-2'></i>
                                        <h5 class='mb-0 mr-3'>
                                            <strong class='text-primary'><?= $promocode ?></strong>
                                        </h5>
                                        <button class='btn btn-sm btn-outline-secondary' onclick="copyPromoCode('<?= $promocode ?>', this)" 
                                                title="Скопировать промокод <?= $promocode ?>">
                                            <i class='fa fa-copy'></i>
                                        </button>
                                        <?= $expirationBadge ?>
                                    </div>
                                </div>
                                <div class='copy-feedback text-success small mt-1 ml-4' style='display: none;'>
                                    <i class='fa fa-check'></i> Скопировано
                                </div>
                            </div>
                            
                            <div class='card-body'>
                                <!-- Validity Section -->
                                <div class='mb-3'>
                                    <div class='text-muted small mb-1'><i class='fa fa-calendar mr-1'></i>Действует до:</div>
                                    <div class='d-flex justify-content-between align-items-center'>
                                        <span class='font-weight-bold'><?= $dt2 ?></span>
                                        <span class='badge badge-light'><?= $daysLeft ?> дн.</span>
                                    </div>
                                </div>
                                
                                <!-- Limits Section -->
                                <div class='mb-3'>
                                    <div class='text-muted small mb-1'><i class='fa fa-repeat mr-1'></i>Осталось активаций:</div>
                                    <div class='font-weight-bold'><?= $cnt ?></div>
                                </div>
                                
                                <!-- Products Section -->
                                <div class='mb-3'>
                                    <div class='text-muted small mb-1'><i class='fa fa-cube mr-1'></i>Для продукта:</div>
                                    <div class='font-weight-bold'><?= $descr ?></div>
                                    <?php if($extraProducts): ?>
                                    <small class='text-muted d-block mt-1'><?= $extraProducts ?></small>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Benefits Grid -->
                                <div class='row'>
                                    <div class='col-6 mb-2'>
                                        <div class='text-muted small'>Спеццена:</div>
                                        <div class='font-weight-bold text-success'><?= $priceDisplay ?></div>
                                    </div>
                                    <div class='col-6 mb-2'>
                                        <div class='text-muted small'>Скидка:</div>
                                        <div class='font-weight-bold text-warning'><?= $discountDisplay ?></div>
                                    </div>
                                    <div class='col-6'>
                                        <div class='text-muted small'>Вознаграждение 1:</div>
                                        <div class='font-weight-bold text-info'><?= $fee_1 ?></div>
                                    </div>
                                    <div class='col-6'>
                                        <div class='text-muted small'>Вознаграждение 2:</div>
                                        <div class='font-weight-bold text-secondary'><?= $fee_2 ?></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class='card-footer bg-white border-top-0 pt-0'>
                                <small class='text-muted'>
                                    <i class='fa fa-info-circle'></i> Промокод активен
                                </small>
                            </div>
                        </div>
                    </div>
                    <?php
                    }
                    ?>
                </div>
                
                <!-- Information Section -->
                <div class='row mt-4'>
                    <div class='col-12'>
                        <div class='alert alert-info'>
                            <h6><i class='fa fa-info-circle mr-2'></i>Как использовать промокоды</h6>
                            <ul class='mb-0 pl-3'>
                                <li>Передайте промокод друзьям и знакомым при рекомендации</li>
                                <li>Клиент вводит промокод при оформлении заказа</li>
                                <li>Промокод дает скидку или специальную цену</li>
                                <li>Вы получаете вознаграждение после оплаты заказа</li>
                                <li>Ограниченное количество активаций или время действия</li>
                            </ul>
                        </div>
                        
                        <div class='alert alert-warning'>
                            <h6><i class='fa fa-exclamation-triangle mr-2'></i>Важная информация</h6>
                            <p class='mb-0'>
                                <span class='badge badge-danger mr-1'><i class='fa fa-clock'></i></span> Промокоды с красной рамкой истекают в ближайшие 3 дня.
                                <span class='badge badge-warning mr-1'><i class='fa fa-clock'></i></span> Желтая рамка означает истечение в течение недели.
                                Используйте их в первую очередь!
                            </p>
                        </div>
                    </div>
                </div>
                
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="fa fa-ticket fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Нет активных промокодов</h5>
                    <p class="text-muted small">Создайте промокоды для привлечения клиентов</p>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Закрыть</button>
                <?php if($promoCount > 0): ?>
                <button type="button" class="btn btn-warning" onclick="copyAllPromocodes()">
                    <i class="fa fa-copy mr-2"></i>Копировать все промокоды
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- CSS for modal -->
<style>
    .modal-xl {
        max-width: 95%;
    }
    @media (max-width: 767px) {
        .modal-xl {
            max-width: 100%;
            margin: 10px;
        }
        .card h5 {
            font-size: 1.1rem;
        }
        .card .row > div {
            margin-bottom: 8px;
        }
        .card-header .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }
    }
    @media (max-width: 575px) {
        .modal-body {
            padding: 15px;
        }
        .card-header h5 {
            font-size: 1rem;
        }
        .card-body .text-muted.small {
            font-size: 0.8rem;
        }
        .col-md-6.col-lg-4.mb-4 {
            padding-left: 8px;
            padding-right: 8px;
        }
    }
    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
    }
    .border-danger {
        border-width: 2px !important;
        animation: pulse-danger 2s infinite;
    }
    .border-warning {
        border-width: 2px !important;
    }
    .copy-feedback {
        font-size: 0.8rem;
    }
    .btn-outline-secondary {
        border-color: #6c757d;
        color: #6c757d;
    }
    .btn-outline-secondary:hover {
        background-color: #6c757d;
        color: white;
    }
    /* Add subtle pulse animation for expiring promocodes */
    @keyframes pulse-danger {
        0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4); }
        70% { box-shadow: 0 0 0 5px rgba(220, 53, 69, 0); }
        100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
    }
</style>

<!-- JavaScript for copying promocodes -->
<script>
function copyAllPromocodes() {
    // Get all promocode text elements
    const promoElements = document.querySelectorAll('#promocodesModal .card-header h5 strong.text-primary');
    let allPromocodes = '';
    
    promoElements.forEach((element, index) => {
        allPromocodes += element.textContent + '\n';
    });
    
    // Copy to clipboard
    navigator.clipboard.writeText(allPromocodes).then(() => {
        alert('Все промокоды скопированы в буфер обмена!');
    }).catch(err => {
        console.error('Ошибка при копировании: ', err);
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = allPromocodes;
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            alert('Все промокоды скопированы в буфер обмена!');
        } catch (err) {
            console.error('Fallback ошибка: ', err);
            alert('Не удалось скопировать промокоды');
        }
        document.body.removeChild(textArea);
    });
}

// Modified copyPromoCode function for modal
function copyPromoCode(text, button) {
    // Get the button icon and feedback element
    var buttonIcon = $(button).find('i');
    var feedback = $(button).closest('.card-header').find('.copy-feedback');
    
    // Hide any existing feedback and reset other buttons in this modal
    $('#promocodesModal .copy-feedback').hide();
    $('#promocodesModal .btn-outline-secondary i').removeClass('fa-check').addClass('fa-copy');
    $('#promocodesModal .btn-outline-secondary').removeClass('btn-success').addClass('btn-outline-secondary');
    
    navigator.clipboard.writeText(text).then(function() {
        // Change icon to checkmark
        buttonIcon.removeClass('fa-copy').addClass('fa-check');
        $(button).removeClass('btn-outline-secondary').addClass('btn-success');
        
        // Show feedback message
        feedback.fadeIn(300);
        
        // Revert after 2 seconds
        setTimeout(function() {
            buttonIcon.removeClass('fa-check').addClass('fa-copy');
            $(button).removeClass('btn-success').addClass('btn-outline-secondary');
            feedback.fadeOut(300);
        }, 2000);
        
    }, function(err) {
        // Fallback for older browsers
        var tempInput = document.createElement("input");
        tempInput.value = text;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand("copy");
        document.body.removeChild(tempInput);
        
        // Change icon to checkmark
        buttonIcon.removeClass('fa-copy').addClass('fa-check');
        $(button).removeClass('btn-outline-secondary').addClass('btn-success');
        
        // Show feedback message
        feedback.fadeIn(300);
        
        // Revert after 2 seconds
        setTimeout(function() {
            buttonIcon.removeClass('fa-check').addClass('fa-copy');
            $(button).removeClass('btn-success').addClass('btn-outline-secondary');
            feedback.fadeOut(300);
        }, 2000);
    });
}

// Also allow clicking on the promocode itself to copy
$(document).ready(function() {
    $('#promocodesModal').on('shown.bs.modal', function() {
        // Attach click event to promocode text
        $('#promocodesModal .card-header h5 strong.text-primary').off('click').on('click', function() {
            var promoCode = $(this).text();
            var button = $(this).closest('.card-header').find('.btn-outline-secondary');
            copyPromoCode(promoCode, button[0]);
        });
        
        // Add hover effect
        $('#promocodesModal .card-header h5 strong.text-primary').css('cursor', 'pointer').hover(
            function() {
                $(this).css('opacity', '0.8');
            },
            function() {
                $(this).css('opacity', '1');
            }
        );
    });
});
</script>
