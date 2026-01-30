<?php
// cabinet2_referrals.ajax.php
include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";
chdir("..");
include_once "init.inc.php";

$klid = intval($_GET['klid']) ?? '';
$user_id=$db->get_user_id($klid);
$page = (int)($_GET['page'] ?? 1);
$perPage = (int)($_GET['per_page'] ?? 10);
$offset = ($page - 1) * $perPage;

// Get filter parameters
$filterName = $_GET['filter_name'] ?? '';
$filterPhone = $_GET['filter_phone'] ?? '';
$filterEmail = $_GET['filter_email'] ?? '';

// Build WHERE conditions for filtering
$whereConditions = ["cards.del=0 AND cards.id!='$klid' AND utm_affiliate = '$klid'"];
$params = [];

// Add name filter (partial match)
if (!empty($filterName)) {
    $filterName = $db->escape($filterName);
    $whereConditions[] = "cards.name LIKE '%$filterName%'";
}

// Add phone filter (partial match)
if (!empty($filterPhone)) {
    $filterPhone = $db->escape($filterPhone);
    $whereConditions[] = "cards.mob_search LIKE '%$filterPhone%'";
}

// Add email filter (partial match)
if (!empty($filterEmail)) {
    $filterEmail = $db->escape($filterEmail);
    $whereConditions[] = "cards.email LIKE '%$filterEmail%'";
}

// Combine all WHERE conditions
$whereClause = implode(' AND ', $whereConditions);

// Get total count with filters
$countQuery = "SELECT COUNT(*) as total FROM cards 
               WHERE $whereClause";
//$db->notify_me($countQuery);
// Get total count
$res_count = $db->query($countQuery);
$totalCount = $db->fetch_assoc($res_count)['total'];

if(DEMO)
	$totalCount = 224; 

$totalPages = ceil($totalCount / $perPage);

// Get paginated data with filters
$query = "SELECT cards.*, cards.tm AS tm
          FROM cards
          WHERE $whereClause 
          ORDER BY cards.tm DESC
          LIMIT $offset, $perPage";
          
$res = $db->query($query);
//$db->notify_me("$query");
// Generate the HTML response
ob_start();
?>

<?php if($totalCount > 0): ?>
<!-- View Toggle -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="btn-group btn-group-sm" role="group">
        <button type="button" class="btn btn-outline-info active" data-view="table" onclick="toggleView('table')">
            <i class="fa fa-table mr-1"></i>Таблица
        </button>
        <button type="button" class="btn btn-outline-info" data-view="cards" onclick="toggleView('cards')">
            <i class="fa fa-th-large mr-1"></i>Карточки
        </button>
    </div>

<!-- Filter Status Badge -->
<?php if(!empty($filterName) || !empty($filterPhone) || !empty($filterEmail)): ?>
<div class="badge badge-info ml-2" id="filterBadge">
    <i class="fa fa-filter mr-1"></i>Фильтр активен
    <button type="button" class="btn btn-sm btn-link text-white p-0 ml-1" onclick="clearFilters()">
        <i class="fa fa-times"></i>
    </button>
</div>
<?php endif; ?>
    
    <!-- Items per page selector -->
    <div class="d-flex align-items-center">
        <span class="text-muted small mr-2">Показать:</span>
        <select class="form-control form-control-sm w-auto" id="itemsPerPage" onchange="changeItemsPerPage(this.value)">
            <option value="10" <?= $perPage == 10 ? 'selected' : '' ?>>10</option>
            <option value="25" <?= $perPage == 25 ? 'selected' : '' ?>>25</option>
            <option value="50" <?= $perPage == 50 ? 'selected' : '' ?>>50</option>
            <option value="100" <?= $perPage == 100 ? 'selected' : '' ?>>100</option>
        </select>
    </div>
</div>

<!-- Hidden input for current page -->
<input type="hidden" id="currentPage" value="<?= $page ?>">

<!-- Stats Card -->
<div class="card border-info mb-4">
    <div class="card-body py-3">
        <div class="row text-center">
            <div class="col-6 col-md-3 mb-2 mb-md-0">
                <div class="text-muted small">Всего рефералов</div>
                <div class="h4 font-weight-bold text-info"><?= $totalCount ?></div>
            </div>
            <div class="col-6 col-md-3 mb-2 mb-md-0">
                <div class="text-muted small">На странице</div>
                <div class="h4 font-weight-bold text-success"><?= min($perPage, $totalCount - $offset) ?></div>
            </div>
            <div class="col-6 col-md-3">
                <div class="text-muted small">Страница</div>
                <div class="h4 font-weight-bold text-primary"><?= $page ?></div>
            </div>
            <div class="col-6 col-md-3">
                <div class="text-muted small">Всего страниц</div>
                <div class="h4 font-weight-bold text-warning"><?= $totalPages ?></div>
            </div>
        </div>
    </div>
</div>


<!-- Filter Panel -->
<div class="card mb-4">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Фильтр</h5>
        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearFilters()">
            <i class="fa fa-times"></i> Сбросить
        </button>
    </div>
    <div class="card-body">
        <form id="userFilterForm" onsubmit="applyFilters(event)">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="filterName" class="form-label">Имя или часть имени</label>
                    <input type="text" class="form-control" id="filterName" name="filter_name" 
                           value="<?= htmlspecialchars($filterName) ?>" 
                           placeholder="Введите имя или часть имени">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="filterPhone" class="form-label">Телефон или часть</label>
                    <input type="text" class="form-control" id="filterPhone" name="filter_phone" 
                           value="<?= htmlspecialchars($filterPhone) ?>" 
                           placeholder="или телефон">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="filterEmail" class="form-label">Email или часть</label>
                    <input type="email" class="form-control" id="filterEmail" name="filter_email" 
                           value="<?= htmlspecialchars($filterEmail) ?>" 
                           placeholder="или email">
                </div>
            </div>
            <div class="d-flex justify-content-between">
<!--
                <button type="button" class="btn btn-outline-secondary" onclick="clearFilters()">
                    <i class="fa fa-times"></i> Очистить
                </button>
-->
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-search"></i> Применить фильтр
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Table View -->
<div id="tableView" class="view-content" style="display: none;">
    <div class="table-responsive">
        <table class='table table-hover table-sm'>
            <thead class="thead-light">
                <tr>
                    <th class="text-center">№</th>
                    <th>Дата регистрации</th>
                    <th>Имя</th>
                    <th>Город</th>
                    <th>Источник</th>
                    <th class="text-center">Статус</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $n = $offset + 1;
                while($r = $db->fetch_assoc($res)):
                    $date = $r['tm_user_id'] ? date("d.m.Y", $r['tm_user_id']) :'';
                    $time = $r['tm_user_id'] ? date("H:i", $r['tm_user_id']) :'';
                    $city = htmlspecialchars($r['city']);
                    $cityDisplay = strlen($city) > 20 ? substr($city, 0, 20) . '...' : $city;
                ?>
                <tr>
                    <td class="text-center align-middle">
                        <span class="badge badge-secondary"><?= $n ?></span>
                    </td>
                    <td class="align-middle">
                        <div class="font-weight-bold"><?= $date ?></div>
                        <small class="text-muted"><?= $time ?></small>
<!--
                        <small class="text-muted"><?= $r['tm_user_id'] ?></small>
-->
                    </td>
                    <td class="align-middle">
                        <div class="font-weight-bold"><?= htmlspecialchars($db->disp_name_cp($r['name'])) ?></div>
                        <small class="text-muted d-block">ID: <?= $r['uid'] ?></small>
                    </td>
                    <td class="align-middle">
                        <span class="d-inline-block text-truncate" style="max-width: 120px;" title="<?= htmlspecialchars($r['city']) ?>">
                            <?= $cityDisplay ?>
                        </span>
                    </td>
                    <td class="align-middle">
                        <div class="d-flex align-items-center">
							<?
								if($msg=$db->dlookup("msg","msgs","uid='{$r['uid']}'
											AND tm BETWEEN ({$r['tm_user_id']} - (5 * 60)) AND ({$r['tm_user_id']} + (5 * 60))
											")) {
									$land_name=$msg;
									?>
<!--
									<a href='<?=$land_url?>' class='' target='_blank'><i class="fa fa-link mr-2"></i></a>
-->
									<span class="text-truncate d-inline-block" style="max-width: 150px;" title="<?= htmlspecialchars($land_name) ?>">
										<?= htmlspecialchars($land_name) ?>
									</span>
									<?
								} else { ?>
									<span class="text-truncate d-inline-block" style="max-width: 150px;">
										не определен
									</span>
								<?}
							?>
                        </div>
                    </td>

					<td class="text-center align-middle">
						<?php if(isset($lk_display_referal_contacts) && $lk_display_referal_contacts) { ?>
							<button type="button" class="btn btn-sm btn-outline-info w-100" 
									onclick="showContacts('<?= htmlspecialchars($r['mob_search'] ?? '', ENT_QUOTES) ?>', 
														  '<?= htmlspecialchars($r['email'] ?? '', ENT_QUOTES) ?>', 
														  '<?= htmlspecialchars($r['telegram_nic'] ?? '', ENT_QUOTES) ?>', 
														  '<?= htmlspecialchars($r['vk_id'] ? 'https://vk.com/'.$r['vk_id'] : '', ENT_QUOTES) ?>')">
								<i class="fa fa-user-circle mr-1"></i> Контакты
							</button>
						<?php } else { ?>
						<span class="badge badge-success mt-1">
							<i class="fa fa-check"></i>
						</span>
						<?}?>
					</td>


                </tr>
                <?php 
                    $n++;
                endwhile;
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Cards View -->
<div id="cardsView" class="view-content" style="display: none;">
    <div class="row">
        <?php
        $n = $offset + 1;
        //$res->data_seek(0); // Reset result pointer
        $res_cards = $db->query($query);
        while($r = $db->fetch_assoc($res_cards)):
			$date = $r['tm_user_id'] ? date("d.m.Y", $r['tm_user_id']) :'';
			$time = $r['tm_user_id'] ? date("H:i", $r['tm_user_id']) :'';
            $city = htmlspecialchars($r['city']);
            $cityDisplay = strlen($city) > 30 ? substr($city, 0, 30) . '...' : $city;
        ?>
        <div class="col-12 mb-3">
            <div class="card border-left-info border-left-3 shadow-sm">
                <div class="card-body py-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge badge-secondary mr-2"><?= $n ?></span>
                                <h6 class="mb-0 font-weight-bold text-dark"><?= htmlspecialchars($db->disp_name_cp($r['name'])) ?></h6>
                            </div>
                            <div class="mb-1">
                                <small class="text-muted">
                                    <i class="fa fa-calendar mr-1"></i><?= $date ?>
                                </small>
                            </div>
                            <div class="mb-1">
                                <small class="text-muted">
                                    <i class="fa fa-map-marker-alt mr-1"></i><?= $cityDisplay ?>
                                </small>
                            </div>
                            <div class="mb-2">
							<?
								if($msg=$db->dlookup("msg","msgs","uid='{$r['uid']}'
											AND tm BETWEEN ({$r['tm_user_id']} - (5 * 60)) AND ({$r['tm_user_id']} + (5 * 60))
											")) {
									$land_name=$msg;
									?>
<!--
									<a href='<?=$land_url?>' class='' target='_blank'><i class="fa fa-link mr-2"></i></a>
-->
									<small class="text-muted">
<!--
										<a href='<?=$r['land_url']?>' class='' target='_blank'><i class="fa fa-link mr-2"></i></a>
-->
										<?= htmlspecialchars($land_name) ?>
									</small>
									<?
								} else { ?>
									<small class="text-muted">
										не определен
									</small>
								<?}
							?>
                            </div>
                        </div>
						<div class="mb-2">
							<?php if(isset($lk_display_referal_contacts) && $lk_display_referal_contacts): ?>
								<button type="button" class="btn btn-sm btn-outline-info w-100" 
										onclick="showContacts('<?= htmlspecialchars($r['mob_search'] ?? '', ENT_QUOTES) ?>', 
															  '<?= htmlspecialchars($r['email'] ?? '', ENT_QUOTES) ?>', 
															  '<?= htmlspecialchars($r['telegram_nic'] ?? '', ENT_QUOTES) ?>', 
															  '<?= htmlspecialchars($r['vk_id'] ? 'https://vk.com/'.$r['vk_id'] : '', ENT_QUOTES) ?>')">
									<i class="fa fa-user-circle mr-1"></i> Контакты
								</button>
							<?php else: ?>
								<span class="badge badge-success">
									<i class="fa fa-check"></i>
								</span>
							<?php endif; ?>
						</div>

                    </div>
                </div>
            </div>
        </div>
        <?php 
            $n++;
        endwhile;
        ?>
    </div>
</div>

<!-- Pagination -->
<?php if($totalPages > 1): ?>
<nav aria-label="Page navigation" class="mt-4">
    <ul class="pagination justify-content-center">
        <!-- Previous button -->
        <li class="page-item <?= $page == 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="#" onclick="changeReferralsPage(<?= $page - 1 ?>); return false;" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>
        
        <!-- Page numbers -->
        <?php
        $startPage = max(1, $page - 2);
        $endPage = min($totalPages, $page + 2);
        
        if($startPage > 1):
        ?>
        <li class="page-item">
            <a class="page-link" href="#" onclick="changeReferralsPage(1); return false;">1</a>
        </li>
        <?php if($startPage > 2): ?>
        <li class="page-item disabled"><span class="page-link">...</span></li>
        <?php endif; ?>
        <?php endif; ?>
        
        <?php for($i = $startPage; $i <= $endPage; $i++): ?>
        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
            <a class="page-link" href="#" onclick="changeReferralsPage(<?= $i ?>); return false;"><?= $i ?></a>
        </li>
        <?php endfor; ?>
        
        <?php if($endPage < $totalPages): ?>
        <?php if($endPage < $totalPages - 1): ?>
        <li class="page-item disabled"><span class="page-link">...</span></li>
        <?php endif; ?>
        <li class="page-item">
            <a class="page-link" href="#" onclick="changeReferralsPage(<?= $totalPages ?>); return false;"><?= $totalPages ?></a>
        </li>
        <?php endif; ?>
        
        <!-- Next button -->
        <li class="page-item <?= $page == $totalPages ? 'disabled' : '' ?>">
            <a class="page-link" href="#" onclick="changeReferralsPage(<?= $page + 1 ?>); return false;" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    </ul>
    
    <!-- Page info -->
    <div class="text-center text-muted small mt-2">
        Показано <?= $offset + 1 ?> - <?= min($offset + $perPage, $totalCount) ?> из <?= $totalCount ?> рефералов
        (Страница <?= $page ?> из <?= $totalPages ?>)
    </div>
</nav>
<?php endif; ?>

<!-- Summary -->
<div class="mt-4 pt-3 border-top">
    <div class="row">
        <div class="col-md-6">
            <div class="d-flex align-items-center">
                <i class="fa fa-info-circle text-info fa-lg mr-3"></i>
                <div>
                    <h6 class="mb-1">Информация о рефералах</h6>
                    <p class="small text-muted mb-0">
                        Это пользователи, зарегистрированные по вашим партнерским ссылкам
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-6 text-md-right mt-3 mt-md-0">
            <div class="btn-group">
                <button class="btn btn-outline-secondary btn-sm" onclick="printReferralsTable()">
                    <i class="fa fa-print mr-1"></i>Печать
                </button>
                <button class="btn btn-outline-secondary btn-sm" onclick="exportReferralsToCSV()">
                    <i class="fa fa-download mr-1"></i>CSV
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Footer buttons -->
<div class="modal-footer mt-4 pt-3 border-top">
    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Закрыть</button>
    <button type="button" class="btn btn-outline-info" onclick="refreshReferrals()">
        <i class="fa fa-sync-alt mr-1"></i>Обновить
    </button>
</div>

<script>
// Initialize view after content loads
document.addEventListener('DOMContentLoaded', function() {
    // Check screen size and set default view
    const isMobile = window.innerWidth < 992; // Bootstrap lg breakpoint
    const savedView = localStorage.getItem('referralsView');
    
    // Use saved preference if exists, otherwise use responsive default
    const viewToShow = savedView || (isMobile ? 'cards' : 'table');
    
    // Apply the view
    setTimeout(function() {
        toggleView(viewToShow);
    }, 100);
});
</script>

<?php else: ?>
<!-- Empty State -->
<div class="text-center py-5">
    <div class="mb-4">
        <i class="fa fa-users fa-4x text-muted mb-3"></i>
        <h5 class="text-muted">Нет рефералов первого уровня</h5>
        <p class="text-muted small mb-4">
            Пользователи, зарегистрированные по вашим партнерским ссылкам, появятся здесь
        </p>
        <div class="alert alert-info">
            <h6><i class="fa fa-lightbulb mr-2"></i>Как привлечь рефералов?</h6>
            <ul class="mb-0 pl-3 small">
                <li>Используйте партнерские ссылки из раздела "Партнерские ссылки"</li>
                <li>Делитесь ссылками в социальных сетях</li>
                <li>Рекомендуйте продукты друзьям и знакомым</li>
                <li>Используйте промокоды для привлечения клиентов</li>
            </ul>
        </div>
    </div>
    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Закрыть</button>
</div>
<?php endif; ?>



<?php
echo ob_get_clean();
?>
