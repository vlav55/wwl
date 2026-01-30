<? include "cab_top.inc.php"; ?>		

		<div class='mt-3 d-flex ' >
			<a class="btn btn-outline-primary" href="cab3_add.php"> Новый клиент </a>
		</div>


<?
	$arr=[];
	$res=$db->query("SELECT * FROM cards WHERE del=0 AND (user_id='$user_id' OR id='$klid')");
	$n=1;
	while($r=$db->fetch_assoc($res)) {
		$add=$db->cards_read_par($r['uid']);

		$tm1=$db->avangard_tm_end($r['uid'],$products_tm_pay_end);
		$tm2=$db->dlookup("tm_end","0ctrl","uid='{$r['uid']}'");
		$tm=$tm2>$tm1 ? $tm2 : $tm1;
		$rest_days=intval(($tm-time())/(24*60*60)).' дн';
		if($rest_days<=0)
			$rest_days="";
		$work=($tm>time()) ? 'https://winwinland.ru/img/switcher_on.svg' : 'https://winwinland.ru/img/switcher_off.svg';

		$arr[]=['uid'=>$r['uid'],
			'n'=>$n++,
			'contact_person'=>$r['name']." ".$r['surname'],
			'email'=>$r['email'],
			'phone'=>$r['mob_search'],
			'inn'=>$add['inn'],
			'company'=>$add['company'],
			'city'=>$add['city'],
			'addr'=>$add['addr'],
			'vid'=>$add['vid'],
			'work'=>$work,
			'rest_days'=>$rest_days,
			'self'=>$r['id']==$klid ? "border: 1px solid #F100E9 !important;" : "",
			];
	}
?>
<div class="table-responsive mt-2">
    <table class="table table-striped table-hover">
        <thead class="thead-light">
            <tr>
                <th scope="col">№</th>
                <th scope="col">Business Info</th>
                <th scope="col">Город</th>
                <th scope="col">Конт.лицо</th>
                <th scope="col" class="text-center">Подписка</th>
                <th scope="col" class="text-center">Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($arr as $r): ?>
            <tr class='' style=''>
                <td class="font-weight-bold" style='<?=$r['self']?>'><?= htmlspecialchars($r['n'] ?? '') ?></td>
                <td>
                    <div class="font-weight-bold"><?= htmlspecialchars($r['vid'] ?? '') ?></div>
                    <div class="text-muted small"><?= htmlspecialchars($r['company'] ?? '') ?></div>
                    <div class="text-muted small">ИНН:<?= htmlspecialchars($r['inn'] ?? '') ?></div>
                    <div class="text-muted small"><?= htmlspecialchars($r['email'] ?? '') ?></div>
                </td>
                <td>
					<div class="font-weight-bold">
						<span class="d-none d-md-inline"><?= htmlspecialchars($r['city'] ?? '') ?></span>
						<span class="d-md-none"><?= mb_substr(htmlspecialchars($r['city'] ?? ''), 0, 10) ?></span>
					</div>
					<div class="text-muted small mt-1">
						<span class="d-none d-lg-inline"><?= htmlspecialchars($r['addr'] ?? '') ?></span>
						<span class="d-lg-none"><?= mb_substr(htmlspecialchars($r['addr'] ?? ''), 0, 20) ?>..</span>
					</div>
                </td>
                <td>
                    <div class="font-weight-bold"><?= htmlspecialchars($r['contact_person'] ?? '') ?></div>
                    <?php if (!empty($r['phone'])): ?>
                        <div class="text-muted small"><?= htmlspecialchars($r['phone'] ?? '') ?></div>
                    <?php endif; ?>
                </td>
                <td class="text-center">
					<img src='<?=$r['work']?>' alt='' class='my-2' >
					<div class='small text-muted' ><?=$r['rest_days']?></div>
                </td>
				<td class="text-center td-ctrl">
					<div class="d-flex flex-column gap-1">
						<a href="cab3_add.php?client_uid=<?= $r['uid'] ?>" class="btn btn-outline-primary" title="Edit">
							<i class="fa fa-edit"></i><span class="d-none d-md-inline small"> Редактировать</span>
						</a>
						<a href="cab3_ctrl.php?client_uid=<?= $r['uid'] ?>" class="btn btn-outline-primary my-1" title="Edit">
							<img src='https://winwinland.ru/img/ctrl.svg' alt='' width="16">
							<span class="d-none d-md-inline small"> Управление</span>
						</a>
					</div>
				</td>
			</tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<? include "cab_bottom.inc.php"; ?>		
		
