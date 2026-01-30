<?
$title="DASHBOARD";
include "/var/www/vlav/data/www/wwl/inc/top.class.php";
include "/var/www/vlav/data/www/wwl/inc/cp.class.php";
include "init.inc.php";
$t=new top($database,"DASHBOARD",true);
$db=$t;

$cp=new cp;
$query_new=$cp->query_new();
$len=strpos($query_new,"LIMIT");
if($len===false)
	$len=strlen($query_new);
$cnt_new=$cp->num_rows($cp->cp_query(substr($query_new,0,$len)));

$res=$cp->cp_query("SELECT razdel.id AS razdel_id,razdel_name,COUNT(uid) AS cnt FROM cards
		JOIN razdel ON cards.razdel=razdel.id
		WHERE cards.del=0 GROUP BY razdel ORDER BY razdel_num,razdel_name");
?>
<div class='container' > 
	<?
	$array = array();
	while($r=$db->fetch_assoc($res)) {
		$array[] = $r;
	}

	// Разделение массива на группы по 3 значения для каждой колонки на большом экране
	$columns = array_chunk($array, 1);
	?>
	<div class='card p-3 my-5' style='border-radius: 20px; border-color:#555;' >
		<div class='alert alert-primary' >
			Требующие внимания задачи: <a href='cp.php?view=yes&filter=tasks' class='' target=''><?=$cnt_new?></a>
		</div>
<!--
		<h2 class='text-center possibilities__title' >Этапы</h2>
-->
		<div class="row">
		  <? foreach ($columns as $column) { ?>
			<div class="col-md-4" style="position: relative;">
				  <? foreach ($column as $row) {
						$s=$db->get_style_by_razdel($row['razdel_id']);
						//print "HERE_$s";
					  ?>
					<div class='card p-2 my-3 mx-1 bg-info_ text-white' style="<?=$s?> border-radius: 15px; border-color:#FFA500; border-width:1px;">
						<div class="card-body  d-flex flex-column" style="position: relative; min-height: 100%; display: flex; flex-direction: column; <?=$s?>">
							<h3 class='text-center' ><?=$row['razdel_name']?></h3>
							<h4 class='text-center font-weight-bold' >
								<?=$row['cnt']?>
<!--
								<span class='badge bg-white text-secondary p-1' ><?=$row['cnt']?></span>
-->
							</h4>
							<br><br>
							<p class='mt-5 text-center'  style="position: absolute; bottom: 0; left: 0; right: 0; text-align: center;">
								<a href='cp.php?view=yes&filter=<?=$row['razdel_name']?>' class='btn btn-warning_ font-weight-bold py-2 px-3' style='border-radius: 20px; background-color:#ffd600;' target=''>Перейти</a>
							</p>
						</div>
					</div>
				  <? } ?>
			</div>
		  <? } ?>
		</div>
	</div>
</div>
<?
	$t->bottom();
?>
