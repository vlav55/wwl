<!-- Payments Details Modal -->
<div class="modal fade" id="paymentsDetailsModal" tabindex="-1" role="dialog" aria-labelledby="paymentsDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="paymentsDetailsModalLabel">
                    <i class="fa fa-money mr-2"></i>Выплаты
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th class='border-top border-bottom border-left-0 border-right-0'>Дата</th>
                                <th class='border-top border-bottom border-left-0 border-right-0'>Сумма</th>
                                <th class='border-top border-bottom border-left-0 border-right-0'>Вид</th>
                                <th class='border-top border-bottom border-left-0 border-right-0'>Комментарий</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $res = $db->query("SELECT * FROM partnerka_pay WHERE klid=$klid AND sum_pay>0 ORDER BY tm DESC");
                            $paymentCount = $db->num_rows($res);
                            $totalPaid = 0;
                            
                            if($paymentCount > 0) {
                                while($r = $db->fetch_assoc($res)) {
                                    $dt = date("d.m.Y", $r['tm']);
                                    $vid = ($r['vid'] == 1) ? "банк" : "зачет";
                                    $totalPaid += $r['sum_pay'];
                            ?>
                                <tr>
                                    <td><?= $dt ?></td>
                                    <td><strong><?= number_format($r['sum_pay'], 0, '', ' ') ?> ₽</strong></td>
                                    <td>
                                        <?php if($r['vid'] == 1): ?>
                                            <span class="badge badge-primary">банк</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">зачет</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($r['comm']) ?></td>
                                </tr>
                            <?php
                                }
                            } else {
                            ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="fa fa-inbox fa-2x mb-3"></i><br>
                                        Выплаты пока отсутствуют
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if($paymentCount > 0) { ?>
                <div class="mt-4 p-3 bg-light rounded">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fa fa-chart-bar mr-2"></i>Сводка по выплатам</h6>
                            <p class="mb-1"><strong>Всего выплат:</strong> <?= $paymentCount ?></p>
                            <p class="mb-1"><strong>Общая сумма выплат:</strong> <span class="text-success font-weight-bold"><?= number_format($totalPaid, 0, '', ' ') ?> ₽</span></p>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fa fa-info-circle mr-2"></i>Информация</h6>
                            <p class="small text-muted mb-0">
                                <span class="badge badge-primary mr-1">банк</span> - перевод на банковский счет<br>
                                <span class="badge badge-secondary mr-1">зачет</span> - зачет в счет других услуг
                            </p>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                    <i class="fa fa-times mr-1"></i>Закрыть
                </button>
                <button type="button" class="btn btn-primary" onclick="printPaymentsDetails()">
                    <i class="fa fa-print mr-1"></i>Печать
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function printPaymentsDetails() {
    // Create print window
    var printWindow = window.open('', '_blank', 'width=1000,height=700');
    
    // Get data for print
    var partnerName = '<?= htmlspecialchars($r["name"] . " " . $r["surname"]) ?>';
    var currentDate = new Date().toLocaleDateString('ru-RU');
    var currentTime = new Date().toLocaleTimeString('ru-RU');
    
    // Write the print document
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Детали выплат</title>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
            <style>
                body { padding: 20px; font-family: Arial, sans-serif; }
                .print-header { border-bottom: 2px solid #28a745; padding-bottom: 15px; margin-bottom: 20px; }
                .print-footer { border-top: 1px solid #dee2e6; padding-top: 15px; margin-top: 30px; font-size: 12px; color: #6c757d; }
                table { font-size: 14px; }
                .badge { font-size: 12px; padding: 4px 8px; }
                @media print {
                    body { padding: 10px; margin: 0; }
                    table { font-size: 12px; }
                }
            </style>
        </head>
        <body>
            <div class="print-header">
                <h4><i class="fa fa-money mr-2"></i>Детали выплат</h4>
                <p class="mb-1">Партнер: <strong>${partnerName}</strong></p>
                <p class="mb-0 text-muted">Дата отчета: ${currentDate}</p>
            </div>
            
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>Дата</th>
                            <th>Сумма</th>
                            <th>Вид</th>
                            <th>Комментарий</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $res = $db->query("SELECT * FROM partnerka_pay WHERE klid=$klid AND sum_pay>0 ORDER BY tm DESC");
                        $paymentCount = $db->num_rows($res);
                        $totalPaid = 0;
                        
                        if($paymentCount > 0) {
                            while($r = $db->fetch_assoc($res)) {
                                $dt = date("d.m.Y", $r['tm']);
                                $vid = ($r['vid'] == 1) ? "банк" : "зачет";
                                $totalPaid += $r['sum_pay'];
                        ?>
                        <tr>
                            <td><?= $dt ?></td>
                            <td><strong><?= number_format($r['sum_pay'], 0, '', ' ') ?> ₽</strong></td>
                            <td>
                                <?php if($r['vid'] == 1): ?>
                                    <span class="badge badge-primary">банк</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">зачет</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($r['comm']) ?></td>
                        </tr>
                        <?php
                            }
                        } else {
                        ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                <i class="fa fa-inbox fa-2x mb-3"></i><br>
                                Выплаты пока отсутствуют
                            </td>
                        </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            
            <?php if($paymentCount > 0) { ?>
            <div class="print-footer">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Всего выплат:</strong> <?= $paymentCount ?></p>
                        <p class="mb-1"><strong>Общая сумма выплат:</strong> <span class="text-success font-weight-bold"><?= number_format($totalPaid, 0, '', ' ') ?> ₽</span></p>
                    </div>
                    <div class="col-md-6 text-right">
                        <p class="mb-0">Распечатано: ${currentDate} ${currentTime}</p>
                    </div>
                </div>
            </div>
            <?php } ?>
        </body>
        </html>
    `);
    
    printWindow.document.close();
    
    // Print after content loads
    setTimeout(function() {
        printWindow.print();
    }, 500);
}
</script>
