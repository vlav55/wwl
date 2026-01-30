<? include "cab_top.inc.php"; ?>
<?
?>
<div class="mt-5">
	<h2 title='вернуться'>
		<a href='cab3.php' class='' target=''>
			<img src='https://winwinland.ru/img/out.svg' alt=''>
		</a>
	</h2>
</div>
<?include "cab3_client_info.inc.php";?>

<div class="container-fluid">
    <div class="row mt-5">
        <div class="col-lg-8 mx-auto">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">Настройка</h1>
            </div>

            <!-- Settings Form -->
            <form id="settingsForm">
                <!-- Promo Code Prefix -->
                <div class="form-group">
                    <label for="promoPrefix" class="mb-2">
                        Префикс промокода
                    </label>
                    <input type="text" class="form-control" id="promoPrefix" value="promo" 
                           placeholder="Введите префикс">
                </div>

                <!-- Cashback Settings -->
                <div class="form-group">
                    <label class="mb-2">
                        Кэшбек
                    </label>
                    <div class="row align-items-center">
                        <div class="col-md-6 mb-2">
                            <input type="number" class="form-control" value="1000" 
                                   placeholder="Сумма">
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="form-check mr-3">
                                    <input  class="form-check-input" type="radio" name="cashbackType" id="cashbackPercent" value="percent" checked>
                                    <label class="form-check-label" for="cashbackPercent">%</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="cashbackType" id="cashbackRub" value="rub">
                                    <label class="form-check-label" for="cashbackRub">₽</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Promo Code Discount -->
                <div class="form-group">
                    <label class="mb-2">
                        Скидка по промокоду
                    </label>
                    <div class="row align-items-center">
                        <div class="col-md-6 mb-2">
                            <input type="number" class="form-control" value="10" 
                                   placeholder="Сумма">
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="form-check mr-3">
                                    <input class="form-check-input" type="radio" name="discountType" id="discountPercent" value="percent" checked>
                                    <label class="form-check-label" for="discountPercent">%</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="discountType" id="discountRub" value="rub">
                                    <label class="form-check-label" for="discountRub">₽</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cashback Notification -->
                <div class="form-group">
                    <label class="mb-2">
                        Сообщение о начислении кэшбека
                    </label>
                    <textarea class="form-control" rows="3" 
                              placeholder="Текст сообщения...">
                    </textarea>
                </div>

                <!-- New Card Notification -->
                <div class="form-group">
                    <label class="mb-2">
                        Сообщение о выдаче карты новому клиенту
                    </label>
                    <textarea class="form-control" rows="3" 
                              placeholder="Текст сообщения...">
                    </textarea>
                </div>
                
				<div class="card p-3 form-group ">
					<div class='row' >
						<div class='col-md-6' >
							<label class="mb-2">
								Как отправлять сообщения
							</label>
							<div class="mt-2 d-flex align-items-center">
								<div class="form-check mr-3">
									<input class="form-check-input" type="radio" name="messageType" id="whatsappOption" value="whatsapp" checked>
									<label class="form-check-label" for="whatsappOption">WhatsApp</label>
								</div>
								<div class="form-check">
									<input class="form-check-input" type="radio" name="messageType" id="smsOption" value="sms">
									<label class="form-check-label" for="smsOption">SMS</label>
								</div>
							</div>
						</div>
						<div class='col-md-6 d-flex align-items-end mt-3' >
							<button class='btn btn-outline-primary w-100'>
								<i class="fa fa-cog mr-2"></i>Настроить
							</button>
						</div>
					</div>
				</div>

                <!-- Action Buttons -->
				<div class="form-group">
					<button type="submit" class="btn btn-outline-primary p-2">
						<i class="fa fa-check mr-2"></i>
						<span class="d-none d-sm-inline">Применить</span>
					</button>
					<button type="button" class="btn btn-outline-secondary ml-2" data-toggle="modal" data-target="#shareLinkModal">
						<i class="fa fa-user-plus mr-2"></i>
						<span class="d-none_ d-sm-inline_">Добавить кассира</span>
					</button>
				</div>
            </form>
        </div>
    </div>
</div>

<!-- Share Link Modal -->
<div class="modal fade" id="shareLinkModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-user-plus mr-2"></i>Добавить кассира
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="mb-3">Отправьте эту ссылку кассиру для работы в системе:</p>
                
                <!-- Link Input -->
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="shareLink" 
                           value="https://for16.ru/d/1000/lk/cab3.php?u=b8efd9d93feaa9610788e4bca1e41209" 
                           readonly>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" onclick="copyLink()">
                            <i class="fa fa-copy"></i>
                        </button>
                    </div>
                </div>
                
                <small class="text-muted">Кассир перейдет по ссылке и будет работать в системе</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-primary" onclick="shareLink()">
                    <i class="fa fa-share mr-2"></i>Поделиться
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function copyLink() {
    const linkInput = document.getElementById('shareLink');
    linkInput.select();
    linkInput.setSelectionRange(0, 99999);
    document.execCommand('copy');
    
    // Show feedback
    const copyBtn = event.target.closest('button');
    const originalText = copyBtn.innerHTML;
    
    copyBtn.innerHTML = '<i class="fa fa-check"></i>';
    copyBtn.classList.replace('btn-outline-secondary', 'btn-success');
    
    setTimeout(() => {
        copyBtn.innerHTML = originalText;
        copyBtn.classList.replace('btn-success', 'btn-outline-secondary');
    }, 3000);
}
function shareLink() {
    const link = document.getElementById('shareLink').value;
    
    if (navigator.share) {
        // Use Web Share API if available
        navigator.share({
            title: 'Регистрация кассира',
            text: 'Перейдите по ссылке для регистрации в системе',
            url: link
        });
    } else {
        // Fallback - copy to clipboard
        copyLink();
        alert('Ссылка скопирована в буфер обмена. Отправьте ее кассиру.');
    }
}
</script>


<style>
.form-check-input[type="radio"] {
    -webkit-appearance: none !important;
    appearance: none !important;
    width: 20px !important;
    height: 20px !important;
    border: 1px solid #ddd !important; /* Thinner border like other inputs */
    border-radius: 4px !important;
    background: white !important;
    position: relative !important;
    margin-top: 0 !important;
    margin-left: 0 !important;
    margin-right: 8px !important;
    cursor: pointer;
}

.form-check-input[type="radio"]:checked {
    background-color: #007bff !important;
    border-color: #007bff !important; /* Same border color when checked */
}

.form-check-input[type="radio"]:checked::before {
    content: "✓";
    position: absolute;
    color: white;
    font-size: 14px;
    font-weight: bold;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

/* Focus state to match other inputs */
.form-check-input[type="radio"]:focus {
    border-color: #007bff !important;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
    outline: none !important;
}
</style>

<? include "cab_bottom.inc.php"; ?>
