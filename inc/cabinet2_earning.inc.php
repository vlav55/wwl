<!-- Earnings Details Modal -->
<div class="modal fade" id="earningsDetailsModal" tabindex="-1" role="dialog" aria-labelledby="earningsDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="earningsDetailsModalLabel">
                    <i class="fa fa-info-circle mr-2"></i>Начисления
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
                                <th class='border-top border-bottom border-left-0 border-right-0'>№</th>
                                <th class='border-top border-bottom border-left-0 border-right-0' >Дата</th>
                                <th class='border-top border-bottom border-left-0 border-right-0'>Чья продажа</th>
                                <th class='border-top border-bottom border-left-0 border-right-0'>Имя</th>
                                <th class='border-top border-bottom border-left-0 border-right-0'>ОПЛАТА от клиента</th>
                                <th class='border-top border-bottom border-left-0 border-right-0'>% вознагр.</th>
                                <th class='border-top border-bottom border-left-0 border-right-0'>Начислено партнеру</th>
                                <th class='border-top border-bottom border-left-0 border-right-0'>Продукт</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $n = 1;
                            $res = $db->query("SELECT * FROM partnerka_op WHERE klid_up='$klid' ORDER BY tm DESC LIMIT 50");
                            while($r = $db->fetch_assoc($res)) {
                                $name = $db->dlookup("name", "cards", "uid='{$r['uid']}'") . " " . $db->dlookup("surname", "cards", "uid='{$r['uid']}'");
                                $sum = $r['amount'];
                                $fee = $r['fee'];
                                
                                if($r['avangard_id'] > 0)
                                    $product = $db->dlookup("order_descr", "avangard", "id='{$r['avangard_id']}'");
                                if($r['product_id'] == 1001)
                                    $product = "ПРИВЕТСТВЕННЫЕ БАЛЛЫ";
                                if($r['product_id'] == -1)
                                    $product = "НАЧИСЛЕНО";
                                
                                if($r['level'] == 1) {
                                    $vid = "собств";
                                } else {
                                    $vid = $db->dlookup("real_user_name", "users", "klid='{$r['klid']}'");
                                }
                            ?>
                                <tr>
                                    <td><?= $n ?></td>
                                    <td><?= date("d.m.Y", $r['tm']) ?></td>
                                    <td><?= htmlspecialchars($vid) ?></td>
                                    <td><?= htmlspecialchars($name) ?></td>
                                    <td><?= number_format($sum, 0, '', ' ') ?> ₽</td>
                                    <td><?= $fee ?>%</td>
                                    <td><strong><?= number_format($r['fee_sum'], 0, '', ' ') ?> ₽</strong></td>
                                    <td><?= htmlspecialchars($product) ?></td>
                                </tr>
                            <?php
                                $n++;
                            }
                            
                            if($n == 1) {
                            ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="fa fa-inbox fa-2x mb-3"></i><br>
                                        Начисления пока отсутствуют
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if($n > 1) { ?>
                <div class="alert alert-info mt-3">
                    <i class="fa fa-info-circle mr-2"></i>
                    Показаны последние 50 начислений. Общее количество: <strong><?= $db->num_rows($db->query("SELECT * FROM partnerka_op WHERE klid_up='$klid'")) ?></strong>
                </div>
                <?php } ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                    <i class="fa fa-times mr-1"></i>Закрыть
                </button>
                <button type="button" class="btn btn-primary" onclick="printEarningsDetails()">
                    <i class="fa fa-print mr-1"></i>Печать
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function printEarningsDetails() {
    // Create a print stylesheet
    var printStyle = document.createElement('style');
    printStyle.innerHTML = `
        @media print {
            .modal-footer { display: none !important; }
            .modal-header .close { display: none !important; }
        }
    `;
    document.head.appendChild(printStyle);
    
    // Print
    window.print();
    
    // Remove the style after printing
    setTimeout(function() {
        document.head.removeChild(printStyle);
    }, 100);
}
</script>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #earningsDetailsModal,
    #earningsDetailsModal * {
        visibility: visible;
    }
    #earningsDetailsModal {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .modal-footer {
        display: none;
    }
}
</style>
