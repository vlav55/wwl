<?
	$arr=[];
	$client_uid=intval($_GET['client_uid']);
	$r=$db->fetch_assoc($db->query("SELECT * FROM cards WHERE del=0 AND (user_id='$user_id' OR id='$klid') AND uid='$client_uid'"));
	$add=$db->cards_read_par($r['uid']);
	$arr=['uid'=>$r['uid'],
		'contact_person'=>$r['name']." ".$r['surname'],
		'email'=>$r['email'],
		'phone'=>$r['mob_search'],
		'inn'=>$add['inn'],
		'company'=>$add['company'],
		'city'=>$add['city'],
		'addr'=>$add['addr'],
		'vid'=>$add['vid'],
		'has_wwl_acc'=>$client_ctrl_id,
		'payed_till'=>$client_tm_end,
		];
?>
<div class='mt-4' ></div>
<div class="card client-card shadow-sm border-0">
	<div class="card-header border-primary text-primary">
		<div class="d-flex justify-content-between align-items-center">
			<h5 class="card-title mb-0">
				<i class="fa fa-user-circle mr-2"></i>Информация о клиенте
			</h5>
			<div class="d-flex align-items-center">
				<!-- Account Status Badge -->
				<?php if ($arr['has_wwl_acc']): ?>
					<span class="badge badge-outline-success mr-2">
						<img src='https://winwinland.ru/img/switcher_on.svg' title=''> Аккаунт Лояльность 2.0
					</span>
				<?php else: ?>
					<span class="badge badge-outline-secondary mr-2">
						<img src='https://winwinland.ru/img/switcher_off.svg' title=''> Нет аккаунта Лояльность 2.0
					</span>
				<?php endif; ?>
				<span class="badge badge-outline-light">ID: <?= htmlspecialchars($arr['has_wwl_acc'] ?? '') ?></span>
			</div>
		</div>
	</div>
	<div class="card-body p-0">
        <div class="row no-gutters">
            <!-- Block 1: Company Info -->
            <div class="col-md-4 p-3 border-right">
                <h6 class="text-muted mb-2"><i class="fa fa-building mr-1"></i>Компания</h6>
                <strong class="d-block mb-2"><?= htmlspecialchars($arr['company'] ?? '') ?></strong>
                <small class="text-muted d-block">ИНН: <?= htmlspecialchars($arr['inn'] ?? '') ?></small>
                <small class="text-muted"><?= htmlspecialchars($arr['vid'] ?? '') ?></small>
            </div>

            <!-- Block 2: Location Info -->
            <div class="col-md-4 p-3 border-right">
                <h6 class="text-muted mb-2"><i class="fa fa-map-marker mr-1"></i>Адрес</h6>
                <strong class="d-block mb-1"><?= htmlspecialchars($arr['city'] ?? '') ?></strong>
                <small class="d-block mb-2"><?= htmlspecialchars($arr['addr'] ?? '') ?></small>
                <small class="text-muted"><?= htmlspecialchars($arr['email'] ?? '') ?></small>
            </div>

            <!-- Block 3: Contact Info + Payment Status -->
            <div class="col-md-4 p-3">
                <h6 class="text-muted mb-2"><i class="fa fa-user mr-1"></i>Контакт</h6>
                <strong class="d-block mb-2"><?= htmlspecialchars($arr['contact_person'] ?? '') ?></strong>
                <small class="text-muted d-block mb-2"><?= htmlspecialchars($arr['phone'] ?? '') ?></small>
                
                <!-- Payment Status -->
                <?php if ($arr['has_wwl_acc'] && !empty($arr['payed_till'])): ?>
                    <div class="mt-2 pt-2 border-top">
                        <small class="text-muted d-block">Оплачено до</small>
                        <strong class="text-success">
                            <?= date('d.m.Y', $arr['payed_till']) ?>
                        </strong>
                    </div>
                <?php elseif ($arr['has_wwl_acc']): ?>
                    <div class="mt-2 pt-2 border-top">
                        <small class="text-warning">Нет данных об оплате</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
	<div class="card-footer bg-light">
		<div class="row align-items-center">
			<div class="col pb-2">
				<small class="text-muted">
					<i class="fa fa-calendar mr-1"></i> 
					Данные актуальны на <?= date('d.m.Y') ?>
				</small>
			</div>
			
			<div class="col-auto">
				<div class="btn-group">
					<button class="btn btn-outline-info btn-sm" onclick="chk_licenses(<?= $arr['uid'] ?>)">
						<i class="fa fa-cog"></i> Лицензии
					</button>
					<a href="cab3_add.php?client_uid=<?= $arr['uid'] ?>" class="btn btn-outline-primary btn-sm">
						<i class="fa fa-pencil"></i> Редактировать
					</a>
					<?if($cashier_setup_link) {?>
					<a href="<?=$cashier_setup_link?>" class="btn btn-outline-success btn-sm" target="_blank">
						<i class="fa fa-cogs"></i> Настройка
					</a>
					<?}?>
				</div>
			</div>
		</div>
	</div>
</div>
