<!-- Summary Modal with Mobile Stacked Cards -->
<div class="modal fade" id="fee_summary" tabindex="-1" role="dialog" aria-labelledby="feeSummaryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="feeSummaryModalLabel">
                    <i class="fa fa-bar-chart mr-2"></i>Сводка по партнерской программе
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="card border-success h-100">
                            <div class="card-body text-center">
                                <h6 class="card-subtitle mb-2 text-muted">Начислено всего</h6>
                                <h3 class="card-title text-success"><?=formatNumber($sum_fee_all)?>&nbsp;₽</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card border-info h-100">
                            <div class="card-body text-center">
                                <h6 class="card-subtitle mb-2 text-muted">Выплачено всего</h6>
                                <h3 class="card-title text-info"><?=formatNumber($sum_pay_all)?>&nbsp;₽</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card border-warning h-100">
                            <div class="card-body text-center">
                                <h6 class="card-subtitle mb-2 text-muted">Остаток к выплате</h6>
                                <h3 class="card-title text-warning"><?=formatNumber($rest_all)?>&nbsp;₽</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Statistics - Desktop Table (hidden on mobile) -->
                <h4 class="mb-3 text-center">Детальная статистика</h4>
                
                <!-- Desktop Table (md and up) -->
                <div class="d-none d-md-block">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th class="text-nowrap align-middle">Показатель</th>
                                    <th class="text-center align-middle">Сегодня</th>
                                    <th class="text-center align-middle">Вчера</th>
                                    <th class="text-center align-middle">Неделя</th>
                                    <th class="text-center align-middle">Месяц</th>
                                    <th class="text-center align-middle">Прошлый месяц</th>
                                    <th class="text-center align-middle">С начала года</th>
                                    <th class="text-center align-middle bg-light">ВСЕГО</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="row" class="text-nowrap">Количество регистраций</th>
                                    <td class="text-center"><?=$cnt_reg_today?></td>
                                    <td class="text-center"><?=$cnt_reg_yesterday?></td>
                                    <td class="text-center"><?=$cnt_reg_this_week?></td>
                                    <td class="text-center"><?=$cnt_reg_this_month?></td>
                                    <td class="text-center"><?=$cnt_reg_last_month?></td>
                                    <td class="text-center"><?=$cnt_reg_year?></td>
                                    <td class="text-center bg-light font-weight-bold"><?=$cnt_reg_all?></td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-nowrap">Сумма оплат</th>
                                    <td class="text-center"><?=formatNumber($sum_buy_today)?>&nbsp;₽</td>
                                    <td class="text-center"><?=formatNumber($sum_buy_yesterday)?>&nbsp;₽</td>
                                    <td class="text-center"><?=formatNumber($sum_buy_this_week)?>&nbsp;₽</td>
                                    <td class="text-center"><?=formatNumber($sum_buy_this_month)?>&nbsp;₽</td>
                                    <td class="text-center"><?=formatNumber($sum_buy_last_month)?>&nbsp;₽</td>
                                    <td class="text-center"><?=formatNumber($sum_buy_year)?>&nbsp;₽</td>
                                    <td class="text-center bg-light font-weight-bold"><?=formatNumber($sum_buy_all)?>&nbsp;₽</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-nowrap">Сумма комиссий</th>
                                    <td class="text-center"><?=formatNumber($sum_fee_today)?>&nbsp;₽</td>
                                    <td class="text-center"><?=formatNumber($sum_fee_yesterday)?>&nbsp;₽</td>
                                    <td class="text-center"><?=formatNumber($sum_fee_this_week)?>&nbsp;₽</td>
                                    <td class="text-center"><?=formatNumber($sum_fee_this_month)?>&nbsp;₽</td>
                                    <td class="text-center"><?=formatNumber($sum_fee_last_month)?>&nbsp;₽</td>
                                    <td class="text-center"><?=formatNumber($sum_fee_year)?>&nbsp;₽</td>
                                    <td class="text-center bg-light font-weight-bold text-success"><?=formatNumber($sum_fee_all)?>&nbsp;₽</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-nowrap">Выплачено</th>
                                    <td class="text-center"><?=formatNumber($sum_pay_today)?>&nbsp;₽</td>
                                    <td class="text-center"><?=formatNumber($sum_pay_yesterday)?>&nbsp;₽</td>
                                    <td class="text-center"><?=formatNumber($sum_pay_this_week)?>&nbsp;₽</td>
                                    <td class="text-center"><?=formatNumber($sum_pay_this_month)?>&nbsp;₽</td>
                                    <td class="text-center"><?=formatNumber($sum_pay_last_month)?>&nbsp;₽</td>
                                    <td class="text-center"><?=formatNumber($sum_pay_year)?>&nbsp;₽</td>
                                    <td class="text-center bg-light font-weight-bold text-info"><?=formatNumber($sum_pay_all)?>&nbsp;₽</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Cards (sm and below) -->
                <div class="d-md-none">
                    <!-- Today Card -->
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            <strong><i class="fa fa-calendar-day mr-2"></i>Сегодня</strong>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <div class="text-muted small">Регистрации</div>
                                    <div class="h5"><?=$cnt_reg_today?></div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="text-muted small">Оплаты</div>
                                    <div class="h5"><?=formatNumber($sum_buy_today)?>&nbsp;₽</div>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted small">Комиссии</div>
                                    <div class="h5 text-success"><?=formatNumber($sum_fee_today)?>&nbsp;₽</div>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted small">Выплачено</div>
                                    <div class="h5 text-info"><?=formatNumber($sum_pay_today)?>&nbsp;₽</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Yesterday Card -->
                    <div class="card mb-3">
                        <div class="card-header bg-secondary text-white">
                            <strong><i class="fa fa-calendar mr-2"></i>Вчера</strong>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <div class="text-muted small">Регистрации</div>
                                    <div class="h5"><?=$cnt_reg_yesterday?></div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="text-muted small">Оплаты</div>
                                    <div class="h5"><?=formatNumber($sum_buy_yesterday)?>&nbsp;₽</div>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted small">Комиссии</div>
                                    <div class="h5 text-success"><?=formatNumber($sum_fee_yesterday)?>&nbsp;₽</div>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted small">Выплачено</div>
                                    <div class="h5 text-info"><?=formatNumber($sum_pay_yesterday)?>&nbsp;₽</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- This Week Card -->
                    <div class="card mb-3">
                        <div class="card-header bg-info text-white">
                            <strong><i class="fa fa-calendar-week mr-2"></i>Неделя</strong>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <div class="text-muted small">Регистрации</div>
                                    <div class="h5"><?=$cnt_reg_this_week?></div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="text-muted small">Оплаты</div>
                                    <div class="h5"><?=formatNumber($sum_buy_this_week)?>&nbsp;₽</div>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted small">Комиссии</div>
                                    <div class="h5 text-success"><?=formatNumber($sum_fee_this_week)?>&nbsp;₽</div>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted small">Выплачено</div>
                                    <div class="h5 text-info"><?=formatNumber($sum_pay_this_week)?>&nbsp;₽</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- This Month Card -->
                    <div class="card mb-3">
                        <div class="card-header bg-warning text-dark">
                            <strong><i class="fa fa-calendar mr-2"></i>Месяц</strong>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <div class="text-muted small">Регистрации</div>
                                    <div class="h5"><?=$cnt_reg_this_month?></div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="text-muted small">Оплаты</div>
                                    <div class="h5"><?=formatNumber($sum_buy_this_month)?>&nbsp;₽</div>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted small">Комиссии</div>
                                    <div class="h5 text-success"><?=formatNumber($sum_fee_this_month)?>&nbsp;₽</div>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted small">Выплачено</div>
                                    <div class="h5 text-info"><?=formatNumber($sum_pay_this_month)?>&nbsp;₽</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Last Month Card -->
                    <div class="card mb-3">
                        <div class="card-header bg-light text-dark">
                            <strong><i class="fa fa-calendar-minus mr-2"></i>Прошлый месяц</strong>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <div class="text-muted small">Регистрации</div>
                                    <div class="h5"><?=$cnt_reg_last_month?></div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="text-muted small">Оплаты</div>
                                    <div class="h5"><?=formatNumber($sum_buy_last_month)?>&nbsp;₽</div>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted small">Комиссии</div>
                                    <div class="h5 text-success"><?=formatNumber($sum_fee_last_month)?>&nbsp;₽</div>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted small">Выплачено</div>
                                    <div class="h5 text-info"><?=formatNumber($sum_pay_last_month)?>&nbsp;₽</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Year Card -->
                    <div class="card mb-3">
                        <div class="card-header bg-success text-white">
                            <strong><i class="fa fa-calendar-alt mr-2"></i>С начала года</strong>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <div class="text-muted small">Регистрации</div>
                                    <div class="h5"><?=$cnt_reg_year?></div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="text-muted small">Оплаты</div>
                                    <div class="h5"><?=formatNumber($sum_buy_year)?>&nbsp;₽</div>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted small">Комиссии</div>
                                    <div class="h5 text-success"><?=formatNumber($sum_fee_year)?>&nbsp;₽</div>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted small">Выплачено</div>
                                    <div class="h5 text-info"><?=formatNumber($sum_pay_year)?>&nbsp;₽</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Totals Card (for mobile) -->
                    <div class="card mb-3 border-dark">
                        <div class="card-header bg-dark text-white">
                            <strong><i class="fa fa-chart-line mr-2"></i>ВСЕГО</strong>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <div class="text-muted small">Всего регистраций</div>
                                    <div class="h5 font-weight-bold"><?=$cnt_reg_all?></div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="text-muted small">Всего оплат</div>
                                    <div class="h5 font-weight-bold"><?=formatNumber($sum_buy_all)?>&nbsp;₽</div>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted small">Всего комиссий</div>
                                    <div class="h5 font-weight-bold text-success"><?=formatNumber($sum_fee_all)?>&nbsp;₽</div>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted small">Всего выплат</div>
                                    <div class="h5 font-weight-bold text-info"><?=formatNumber($sum_pay_all)?>&nbsp;₽</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Totals Section -->
                <div class="mt-4 p-3 bg-light rounded">
                    <h5 class="mb-3"><i class="fa fa-calculator mr-2"></i>Итоги</h5>
                    <div class="row">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="d-flex justify-content-between align-items-center p-2 bg-white rounded">
                                <span>Начислено:</span>
                                <strong class="text-success h5 mb-0"><?=formatNumber($sum_fee_all)?>&nbsp;₽</strong>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="d-flex justify-content-between align-items-center p-2 bg-white rounded">
                                <span>Выплачено:</span>
                                <strong class="text-info h5 mb-0"><?=formatNumber($sum_pay_all)?>&nbsp;₽</strong>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex justify-content-between align-items-center p-2 bg-white rounded">
                                <span>К выплате:</span>
                                <strong class="text-warning h5 mb-0"><?=formatNumber($rest_all)?>&nbsp;₽</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                    <i class="fa fa-times mr-1"></i>Закрыть
                </button>
                <button type="button" class="btn btn-primary" onclick="printSummaryContent()">
                    <i class="fa fa-print mr-1"></i>Печать
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add this CSS for better mobile display -->
<style>
    @media (max-width: 767px) {
        #fee_summary .modal-dialog {
            margin: 10px;
        }
        #fee_summary .modal-body {
            padding: 15px;
        }
        #fee_summary .card-body .h5 {
            font-size: 1.25rem;
            margin-bottom: 0;
        }
        #fee_summary .card-body .small {
            font-size: 0.85rem;
        }
    }
</style>

<script>
function printSummaryContent() {
    // Close modal if open
    $('#fee_summary').modal('hide');
    
    // Get modal HTML content
    var modalContent = $('#fee_summary .modal-content').html();
    
    // Create new window
    var printWindow = window.open('', '_blank');
    
    // Write print-friendly content
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Сводка по партнерской программе</title>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
            <style>
                body { padding: 20px; }
                .modal-footer, .close { display: none; }
                @media print { 
                    body { padding: 0; margin: 0; }
                    .container-fluid { padding: 0; }
                }
            </style>
        </head>
        <body>
            <div class="container-fluid">
                ${modalContent}
            </div>
        </body>
        </html>
    `);
    
    printWindow.document.close();
    
    // Print after content loads
    printWindow.onload = function() {
        printWindow.print();
    };
}
</script>
