<!-- Buy License Modal -->
<div class="modal fade" id="buyLicenseModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Покупка лицензии</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Client Info -->
<!--
                <div class="alert alert-info">
                    <h6 class="mb-0">Клиент: <strong><?= htmlspecialchars($arr['contact_person'] ?? '') ?></strong></h6>
                </div>
-->

                <!-- Current Balance -->
                <div class="current-balance mb-4">
                    <h6>Текущий баланс:</h6>
                    <div class="card bg-light">
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Лояльность 2.0</span>
                                <span class="badge badge-outline-primary"><?= $billing_rest ?> мес</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Period Selector -->
                <div class="period-selector mb-4">
                    <h6>Выберите период:</h6>
                    <div class="row text-center">
                        <!-- Basic Plans -->
                        <div class="col-6 mb-3">
                            <input type="radio" class="btn-check" name="license_period" id="period1" value="30" autocomplete="off" checked>
                            <label class="btn btn-outline-primary w-100 py-3" for="period1">
                                <div class="h5 mb-1">1 месяц</div>
                                <small class="text-muted"><?=$db->format_money($base_prices[30][2])?></small>
							</label>
                        </div>
                        <div class="col-6 mb-3">
                            <input type="radio" class="btn-check" name="license_period" id="period3" value="31" autocomplete="off">
                            <label class="btn btn-outline-primary w-100 py-3" for="period3">
                                <div class="h5 mb-1">3 месяца</div>
                                <small class="text-muted"><?=$db->format_money($base_prices[31][2])?></small>
                            </label>
                        </div>
                        <div class="col-6 mb-3">
                            <input type="radio" class="btn-check" name="license_period" id="period6" value="35" autocomplete="off">
                            <label class="btn btn-outline-primary w-100 py-3" for="period6">
                                <div class="h5 mb-1">6 месяцев</div>
                                <small class="text-muted"><?=$db->format_money($base_prices[35][2])?></small>
                            </label>
                        </div>
                        <div class="col-6 mb-3">
                            <input type="radio" class="btn-check" name="license_period" id="period12" value="32" autocomplete="off">
                            <label class="btn btn-outline-primary w-100 py-3" for="period12">
                                <div class="h5 mb-1">12 месяцев</div>
                                <small class="text-muted"><?=$db->format_money($base_prices[32][2])?></small>
                            </label>
                        </div>

                        <!-- Premium Plans -->
                        <div class="col-6 mb-3">
                            <input type="radio" class="btn-check" name="license_period" id="period36" value="120" autocomplete="off">
                            <label class="btn btn-outline-primary w-100 py-3" for="period36">
                                <div class="h5 mb-1">36 месяцев</div>
                                <small class="text-muted"><?=$db->format_money($base_prices[120][2])?></small>
<!--
                                <div class="mt-1">
                                    <small class="text-muted">+ обучение и менторинг, нет рассрочки </small>
                                </div>
-->
                            </label>
                        </div>
                        <div class="col-6 mb-3">
                            <input type="radio" class="btn-check" name="license_period" id="period36_installment" value="123" autocomplete="off">
                            <label class="btn btn-outline-success w-100 py-3" for="period36_installment">
                                <div class="h5 mb-1">36 месяцев</div>
                                <small class="text-muted"><?=$db->format_money($base_prices[123][2])?></small>
                                <div class="mt-1">
                                    <small class="text-muted">+ обучение, менторинг, рассрочка</small>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Summary -->
                <div class="purchase-summary card border-primary">
                    <div class="card-body">
                        <h6 class="card-title">Итого к оплате:</h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <span id="selectedPeriod">1 месяц</span>
                            <strong id="totalAmount" class="h5 text-primary">500 руб</strong>
                        </div>
                        <div id="premiumFeatures" class="mt-2"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                <a href="https://winwinland.ru/order.php?product_id=30" id="buyLink" class="btn btn-success" target="_blank">
                    <i class="fa fa-credit-card mr-2"></i>Перейти к оплате
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function buy_license() {
    // Set the purchase link
    updatePurchaseLink(30); // Default to 1 month (product_id=30)
    
    // Show the modal
    $('#buyLicenseModal').modal('show');
}

function updatePurchaseLink(productId) {
    // Update the purchase link with selected product_id only
    const baseUrl = 'https://winwinland.ru/order.php';
    const link = `${baseUrl}?c=partner&product_id=${productId}&uid=<?=$partner_uid_md5?>`;
    
    document.getElementById('buyLink').href = link;
    
    // Update summary based on product_id
    updateSummary(productId);
}

function updateSummary(productId) {
    const prices = {
        30: { period: '1 месяц', amount: '<?=$db->format_money($base_prices[30][2])?>', premium: false },
        31: { period: '3 месяца', amount: '<?=$db->format_money($base_prices[31][2])?>', premium: false },
        35: { period: '6 месяцев', amount: '<?=$db->format_money($base_prices[35][2])?>', premium: false },
        32: { period: '12 месяцев', amount: '<?=$db->format_money($base_prices[32][2])?>', premium: false },
        120: { period: '36 месяцев', amount: '<?=$db->format_money($base_prices[120][2])?>', premium: false },
        123: { period: '36 месяцев', amount: '<?=$db->format_money($base_prices[123][2])?>', premium: true, features: 'Обучение + Менторинг + Рассрочка' }
    };
    
    const selected = prices[productId];
    if (selected) {
        document.getElementById('selectedPeriod').textContent = selected.period;
        document.getElementById('totalAmount').textContent = selected.amount;
        
        // Show premium features if applicable
        const featuresDiv = document.getElementById('premiumFeatures');
        if (selected.premium && selected.features) {
            featuresDiv.innerHTML = `<small class="text-success"><i class="fa fa-star mr-1"></i>${selected.features}</small>`;
        } else {
            featuresDiv.innerHTML = '';
        }
    }
}

// Event listeners for period selection
document.querySelectorAll('input[name="license_period"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const productId = parseInt(this.value);
        updatePurchaseLink(productId);
    });
});
</script>
