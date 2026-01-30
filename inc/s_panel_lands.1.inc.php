<?
include_once "/var/www/vlav/data/www/wwl/inc/top.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/vkt_send.class.php";
include_once "/var/www/vlav/data/www/wwl/inc/vklist_api.class.php";
include_once "init.inc.php";

//$css="<script src='https://cdn.tiny.cloud/1/f2xzffdauodyzgkolvlho4tj9b7wf4iebzjolyv6rl3ihfdw/tinymce/6/tinymce.min.js' referrerpolicy='origin'></script>";
$css="<script src='https://for16.ru/tinymce/tinymce.min.js'></script>";
$css.="";

$top=new top($database,'Лэндинги',false);
$db=new db($database);
include "/var/www/vlav/data/www/wwl/inc/s_panel_upload_file.1.inc.php";

if(isset($_GET['add_new'])) {
	$land_num=$db->fetch_assoc($db->query("SELECT MAX(land_num) AS n FROM lands WHERE del=0 AND land_num>0 "))['n']+1;
	//~ if($land_num<3)
		//~ $land_num=3;
	$cwd=getcwd();
	$land_dir=$cwd."/".$land_num;
	if(!file_exists($land_dir)) {
		if(!mkdir($land_dir)) {
			print "<p class='alert alert-danger' >Ошибка 3. Сообщите в техподдержку.</p>";
			exit;
		}
	}
	if(!file_exists($land_dir."/index.php")) {
		if(!copy("1/index.php", $land_dir.'/index.php')) {
			print "<p class='alert alert-danger' >Ошибка 2. Сообщите в техподдержку.</p>";
			exit;
		}
	}
	if($land_url_last=$db->fetch_assoc($db->query("SELECT land_url FROM lands WHERE del=0 AND land_num>0 ORDER BY id DESC LIMIT 1 "))['land_url']) {
		$r=parse_url($land_url_last);
		$path=!empty($r['path']) ? $r['path'].'' : '';
		$path= preg_replace('/(?<=\/|^)\d+(?=\/?$)/', $land_num, $path);	
		$land_url=$r['scheme']."://".$r['host'].$path;
		if(!check_url($land_url))
			$land_url="https://for16.ru/d/$ctrl_dir/$land_num";
	}
	$db->query("INSERT INTO lands SET tm='".time()."', fl_not_disp_in_cab=1, land_num='$land_num',land_url='".$db->escape($land_url)."',land_name='Новый лэндинг',land_type=1, btn_label='Регистрация'");
	print "<script>location='?saved=yes&section=$land_num#section_$land_num'</script>";
}

function check_url($land_url) {
    // Parse the URL to extract the host
    $parsed_url = parse_url($land_url);

    // Convert the host to Punycode if it contains non-ASCII characters
    if (isset($parsed_url['host'])) {
        $parsed_url['host'] = idn_to_ascii($parsed_url['host']);
    }

    // Rebuild the URL with the Punycode host
    $punycode_url = (isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '') .
                    $parsed_url['host'] .
                    (isset($parsed_url['path']) ? $parsed_url['path'] : '') .
                    (isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '') .
                    (isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '');

    // Get headers
    $headers = @get_headers($punycode_url);
    
    if ($headers === false) {
        return false;
    }
    
    return ( !strpos($headers[0], '200') &&  !strpos($headers[0], '301') ) ? false : true;
}

if(isset($_POST['do_save'])) {
//print_r($_POST);
	$land_id=intval($_POST['land_id']);
	if($land_id) {
		$msg='';
		//$db=new db('vkt');

		list($d,$t)=explode(' ',$_POST['dt_scdl']);
		$tm_scdl=$db->date2tm($_POST['dt_scdl']);
		$tm_scdl+=$db->time2tm($_POST['hi_scdl']);

		$tm_scdl_period=intval($_POST['dt_scdl_period'])*60*60;
		$bizon_zachot=intval($_POST['bizon_zachot']);
		$bizon_duration=intval($_POST['bizon_duration']);
		
		$land_num=intval($_POST['land_num']);
		$fl_partner_land=isset($_POST['fl_partner_land'])?1:0;
		$fl_not_disp_in_cab=isset($_POST['fl_not_disp_in_cab'])?1:0;
		$fl_not_notify=isset($_POST['fl_not_notify'])?1:0;
		$land_razdel=intval($_POST['land_razdel']);
		$land_tag=intval($_POST['land_tag']);
		$land_man_id=intval($_POST['land_man_id']);
		$product_id=$db->dlookup("id","product","del=0 AND id='".intval($_POST['product_id'])."'")?intval($_POST['product_id']):0;

		$land_url=mb_substr($_POST['land_url'],0,255);
		if(empty($land_url)) { 
			$land_url="https://for16.ru/d/$ctrl_dir/$land_num";
		}
		if(!check_url($land_url))
			$land_url="https://for16.ru/d/$ctrl_dir/$land_num";
		$fl_disp_phone=isset($_POST['fl_disp_phone'])?1:0;
		$fl_disp_email=isset($_POST['fl_disp_email'])?1:0;
		$fl_disp_city=isset($_POST['fl_disp_city'])?1:0;
		$fl_disp_comm=isset($_POST['fl_disp_comm'])?1:0;
		$fl_disp_phone_rq=isset($_POST['fl_disp_phone_rq'])?1:0;
		$fl_disp_email_rq=isset($_POST['fl_disp_email_rq'])?1:0;
		$fl_disp_city_rq=isset($_POST['fl_disp_city_rq'])?1:0;

		$land_type=isset($_POST['land_type'])?1:0;

		$db->query("UPDATE lands SET
			land_num='".$land_num."',
			fl_partner_land='$fl_partner_land',
			fl_not_disp_in_cab='$fl_not_disp_in_cab',
			fl_not_notify='$fl_not_notify',
			tm_scdl='$tm_scdl',
			tm_scdl_period='$tm_scdl_period',
			bizon_zachot='$bizon_zachot',
			bizon_duration='$bizon_duration',
			land_razdel='$land_razdel',
			land_tag='$land_tag',
			land_man_id='$land_man_id',
			product_id='$product_id',
			land_url='".$db->escape($land_url)."',
			land_name='".$db->escape((mb_substr($_POST['land_name'],0,255)))."',
			land_type='".$land_type."',
			land_txt='".$db->escape(mb_substr($_POST['land_txt'],0,100000))."',
			thanks_txt='".$db->escape(mb_substr($_POST['thanks_txt'],0,10000))."',
			bot_first_msg='".$db->escape((mb_substr($_POST['bot_first_msg'],0,10000)))."',
			fl_disp_phone='".$fl_disp_phone."',
			fl_disp_email='".$fl_disp_email."',
			fl_disp_city='".$fl_disp_city."',
			fl_disp_comm='".$fl_disp_comm."',
			fl_disp_phone_rq='".$fl_disp_phone_rq."',
			fl_disp_email_rq='".$fl_disp_email_rq."',
			fl_disp_city_rq='".$fl_disp_city_rq."',
			label_disp_comm='".$db->escape(mb_substr($_POST['label_disp_comm'],0,512))."',
			btn_label='".$db->escape((mb_substr($_POST['btn_label'],0,64)))."'
			WHERE id='$land_id'",0);

		$vkt=new vkt_send($database);
		$res=$vkt->query("SELECT * FROM vkt_send_1 WHERE del=0 AND land_num='$land_num'",0);
		while($r=$vkt->fetch_assoc($res)) {
			if(!$vkt->save_vkt_send_tm($r['id'],$ctrl_id)) {
				//print "<p class='alert alert-danger' >Ошибка save_vkt_send_tm. Сообщите техподдержке</p>";
			}
		}

		if($msg=='')
			$msg='ok';
		print "<script>location='?saved=yes&section=$land_num&msg=".urlencode($msg)."#section_$land_num'</script>";
	} else
		print "<p class='alert alert-danger' >Ошибка land_id. Сообщите техподдержке.</p>";

}

if(isset($_GET['do_del'])) {
	$land_id=intval($_GET['land_id']);
	if($land_name=$db->dlookup("land_name","lands","id='$land_id'")) {
		print "<p class='alert alert-warning' >Лэндинг: $land_name - удален.</p>";
		$db->query("UPDATE lands SET land_num=0,del=1 WHERE id='$land_id'");
	} else
		print "<p class='alert alert-danger' >Ошибка del_2 обратитесь к техподдержке</p>";
}
if(isset($_GET['del'])) {
	$land_id=intval($_GET['land_id']);
	if($land_name=$db->dlookup("land_name","lands","id='$land_id'")) {
		$r=$db->fetch_assoc($db->query("SELECT * FROM lands WHERE id='$land_id'"));
		if(isset($r['fl_cashier']) && $r['fl_cashier']!=0) {
			print "<p class='alert alert-warning' >Лэндинг используется системой Лояльность 2.0, удаление невозможно</p>";
		} else {
			$land_num=$db->dlookup("land_num","lands","id='$land_id'");
			print "<p class='alert alert-warning' >Удалить лэндинг: <span class='badge' >$land_num</span> $land_name?
			<a href='?do_del=yes&land_id=$land_id' class='' target=''>подвердить</a>
			</p>";
		}
	} else
		print "<p class='alert alert-danger' >Ошибка del_1 обратитесь к техподдержке</p>";
}
if(isset($_GET['dubl'])) {
	$land_id=intval($_GET['land_id']);
	if($land_name=$db->dlookup("land_name","lands","id='$land_id'")) {
		$land_num_old=$db->dlookup("land_num","lands","id='$land_id'");
		$land_url_old=$db->dlookup("land_url","lands","id='$land_id'");

		$row=$db->fetch_assoc($db->query("SELECT * FROM lands WHERE id='$land_id'"));
		unset($row['id']);

		$land_num=$db->fetch_assoc($db->query("SELECT MAX(land_num) AS n FROM lands WHERE del=0 AND land_num>0 "))['n']+1;
		$row['tm']=time();
		$row['land_num']=$land_num;
		$row['land_name']=$row['land_name']." (КОПИЯ)";
		$r=parse_url($row['land_url']);
		$row['land_url']=$r['scheme']."://".$r['host']."/".$land_num;
		//print $row['land_url']; exit;
		if(isset($row['fl_cashier']))
			$row['fl_cashier']=0;

		foreach ($row as $key => $value) {
			$row[$key] = $db->escape($value);
		}
		
		$columns = implode(", ", array_keys($row));
		$values = implode("', '", array_values($row));
		$sql = "INSERT INTO lands ($columns) VALUES ('$values')";
		$db->query("$sql");

		if(!file_exists($land_num)) {
			if(!mkdir($land_num))
				print "Error creating of $land_num";
		}
		if(!copy("1/index.php",$land_num."/index.php"))
			print "Error copy";
		copy("tg_files/land_pic_$land_num_old.jpg","tg_files/land_pic_$land_num.jpg");
		copy("tg_files/thanks_pic_$land_num_old.jpg","tg_files/thanks_pic_$land_num.jpg");
		print "<p class='alert alert-warning' >Лэндинг: $land_name - скопирован.
			<a href='?saved=yes&section=$land_num&msg=".urlencode("Лэндинг: $land_name - скопирован.")."#section_$land_num' class='' target=''>Перейти</a></p>";
		//sleep(3);
		//print "<script>location='?'</script>";
	} else
		print "<p class='alert alert-danger' >Ошибка del_1 обратитесь к техподдержке</p>";
}

?>
<div>
	<a href='javascript:window.close()' class='btn btn-sm btn-warning' target=''>Закрыть</a>
	<a href='s_panel.php' class='' target=''>в профиль</a>
</div>
<div class='container' >
<h2>Лэндинги</h2>
<div><a href='?add_new=yes' class='btn btn-primary' >Добавить новый лэндинг</a></div>
<br>
<?

//~ if(!isset($land_num)) {
	//~ $land_num=0;
	//~ if(isset($_GET['land_num']))
		//~ $land_num=intval($_GET['land_num']);
//~ }


$res=$db->query("SELECT * FROM lands WHERE del=0 ORDER BY land_num");
while($r=$db->fetch_assoc($res)) {
	$land_id=$r['id'];
	$land_num=$r['land_num'];
	$fl_partner_land=$r['fl_partner_land'];
	$fl_not_disp_in_cab=$r['fl_not_disp_in_cab'];
	$fl_not_notify=$r['fl_not_notify'];
	$tm_scdl=$r['tm_scdl'];
	$tm_scdl_period=$r['tm_scdl_period'];
	$bizon_duration=$r['bizon_duration'];
	$bizon_zachot=$r['bizon_zachot'];
	$dt_scdl=($tm_scdl)?date('d.m.Y',$tm_scdl):"00.00.0000";
	$dt_scdl_period=$tm_scdl_period/(60*60);
	$hi_scdl=($tm_scdl)?date('H:i',$tm_scdl):"00:00";
	$land_name=$r['land_name'];
	$land_type=$r['land_type'];
	$land_txt=$r['land_txt'];
	$thanks_txt=$r['thanks_txt'];
	$bot_first_msg=$r['bot_first_msg'];
	$land_razdel=$r['land_razdel'];
	$land_tag=$r['land_tag'];
	$land_man_id=$r['land_man_id'];
	$product_id=$r['product_id'];
	$land_url=$r['land_url'];
	if(empty($land_url))
		$land_url="https://for16.ru/d/$ctrl_dir/$land_num";
	if(!check_url($land_url)) {
		$land_url="https://for16.ru/d/$ctrl_dir/$land_num";
		$db->query("UPDATE lands SET land_url='".$db->escape($land_url)."' WHERE id={$r['id']}");
	}
	if(!$r['fl_disp_phone'] && !$r['fl_disp_email'])
		$r['fl_disp_phone']=1;
	$fl_disp_phone=$r['fl_disp_phone']?'checked':'';
	$fl_disp_email=$r['fl_disp_email']?'checked':'';
	$fl_disp_comm=$r['fl_disp_comm']?'checked':'';
	$fl_disp_city=$r['fl_disp_city']?'checked':'';
	$label_disp_comm=$r['label_disp_comm'];
	$fl_disp_phone_rq=$r['fl_disp_phone_rq']?'checked':'';
	$fl_disp_email_rq=$r['fl_disp_email_rq']?'checked':'';
	$fl_disp_city_rq=$r['fl_disp_city_rq']?'checked':'';
	$btn_label=$r['btn_label'];
	if(empty($btn_label))
		$btn_label='Регистрация';
?>
<form id='f1' method='POST' enctype='multipart/form-data'>
<div class='card bg-light p-3'>
<?
$collapse="collapse";
if(isset($_GET['saved'])) {
	if($_GET['land_num']==$land_num || $_GET['section']==$land_num) {
		if($_GET['msg']) {
			$msg=substr($_GET['msg'],0,128);
			if(stripos($msg, 'ok') === 0) {
				$display_msg = trim(substr($msg, 2));
				$msg_html = "<p class='alert alert-success'>" . htmlspecialchars($display_msg) . "</p>";
			} else {
				$msg_html = "<p class='alert alert-danger'>" . htmlspecialchars($msg) . "</p>";
			}
			print "$msg_html";
		}
		$collapse="";
	}
} elseif(isset($_GET['land_num'])) {
	if($_GET['land_num']==$land_num)
		$collapse="";
}
?>
	<?$itis_partner_land=($fl_partner_land)?"<span class='badge bg-warning' >это партнерский лэндинг</span>":''?>
	<?$itis_product_land=($product_id)?"<span class='badge bg-success' >это товарный лэндинг ($product_id)</span>":''?>
	<div>
		<div class='badge badge-info' > <span class='badge badge-secondary font18' id='land_<?=$land_num?>' onclick='transformToInput(this)'><?=$land_num?></span> создан <?=date('d.m.Y H:i',$r['tm'])?></div>
		<a href='?dubl=yes&land_id=<?=$land_id?>' title='скопировать лэндинг'><span class="fa fa-copy"></span></a>
		<a href='?del=yes&land_id=<?=$land_id?>' title='удалить лэндинг'><span class="fa fa-trash-o"></span></a>
		<?=$itis_partner_land?>
		<?=$itis_product_land?>
	</div>
	<h3 class='text-center' ><?=$land_name?>
<!--
		<a href='#section_<?=$land_num?>' data-toggle='collapse' title='развернуть настройки'>
			<span class="fa fa-folder-open-o"></span>
		</a>
-->
		<a href='?land_num=<?=$land_num?>#section_<?=$land_num?>' title='развернуть настройки'><span class="fa fa-folder-open-o"></span></a>
		&nbsp;
		<a href='<?=$land_url?>' target='_blank' title='перейти на лэндинг в новом окне'><span class="fa fa-arrow-circle-right"></span></a>
	</h3>
	
	<div class='<?=$collapse?>'  id='section_<?=$land_num?>'>
		<?
		if($collapse=="") {
		?>
		<!--
			<p>Инструкция по настройке <a href='https://youtu.be/u5pxQBNolF4' class='' target='_blank'>на youtube</a></p>
		-->
		<div class='form-group top10'>
			<label for='land_name'>Название лэндинга</label>
			<div class="input-group">
				<input type='text' id='land_name' name='land_name' value='<?=$land_name?>' class='form-control' >
				<div class="input-group-append">
					<button type="submit" class="btn btn-primary btn-sm" name='do_save' value='yes'><i class="fa fa-save"></i></button>
				</div>
			</div>
			<?$checked=($fl_partner_land)?'CHECKED':''?>
			<input type="checkbox" id="__fl_partner_land" name="fl_partner_land" value="1" <?=$checked?> style="transform: scale(1.5);">
			<label for="__fl_partner_land">это партнерский лэндинг</label>
			<br>
			<?$checked=($fl_not_disp_in_cab)?'CHECKED':''?>
			<input type="checkbox" id="__fl_not_disp_in_cab" name="fl_not_disp_in_cab" value="1" <?=$checked?> style="transform: scale(1.5);">
			<label for="__fl_not_disp_in_cab">не отображать ссылку в партнерском кабинете</label>
			<br>
			<?$checked=($fl_not_notify)?'CHECKED':''?>
			<input type="checkbox" id="__fl_not_notify" name="fl_not_notify" value="1" <?=$checked?> style="transform: scale(1.5);">
			<label for="__fl_not_notify">не отправлять уведомления о регистрациях</label>


			<br>
			<br>

			<div class='card p-2 bg-light' >
			<a href='#' data-target='#event' data-toggle='collapse' class='' target=''>Привязка к мероприятию</a>
			<div class='card p-2 bg-light collapse' id='event'>
				<label for='dt_scdl'>Дата и время события</label>
				<div class="input-group">
					<input type='text' id='dt_scdl_<?=$land_id?>' name='dt_scdl' value='<?=$dt_scdl?>' class='form-control text-center'  style='display:inline;width:140px;'>
					<input type='time' id='hi_scdl_<?=$land_id?>' name='hi_scdl' value='<?=$hi_scdl?>' class='form-control text-center' data-input style='display:inline;width:100px;'>
					<a href="javascript:document.getElementById('dt_scdl_<?=$land_id?>').value = '00.00.0000';document.getElementById('hi_scdl_<?=$land_id?>').value = '00:00';void(0);" class='btn btn-warning btn-sm mx-1 ' target=''><span class='fa fa-remove' ></span></a>
					<a href="javascript:document.getElementById('dt_scdl_<?=$land_id?>').value = '<?=date('d.m.Y')?>';document.getElementById('hi_scdl_<?=$land_id?>').value = '<?=date('H:i')?>';void(0);" class='btn btn-info btn-sm mx-1 ' target=''><span class='fa fa-clock-o' ></span></a>
					<div class="input-group-append">
						<button type="submit" class="btn btn-primary btn-sm" name='do_save' value='yes'><i class="fa fa-save"></i></button>
					</div>
				</div>
				<small id='' class='form-text text-muted'>Необязательно. Формат: <?=date('d.m.Y H:i')?> либо 00.00.0000 00:00</small>

				<div class="input-group" style="">
				<label for='dt_scdl_period'>Повторять каждые:</label>
				<input type='text' id='dt_scdl_period' name='dt_scdl_period' value='<?=$dt_scdl_period?>' class='form-control text-center mx-3'  style=''>
				<span class='font-weight-bold' >часа</span>
				</div>

				<div class='card p-2' >
				<div class="input-group" style="">
				<label for='bizon_duration'>Длительность вебинара, мин (*):</label>
				<input type='text' id='bizon_duration' name='bizon_duration' value='<?=$bizon_duration?>' class='form-control text-center mx-3'  style=''>
				<span class='font-weight-bold' >мин</span> <br>
				</div>
				<div class="input-group" style="">
				<label for='bizon_zachot'>Процент просмотра для зачета, % (*):</label> 
				<input type='text' id='bizon_zachot' name='bizon_zachot' value='<?=$bizon_zachot?>' class='form-control text-center mx-3'  style=''>
				<span class='font-weight-bold' >процентов</span>
				</div>
				<div><small id='' class='form-text text-muted'>* - только если мероприятие является вебинаром</small></div>
				</div>
				
			</div>
			<script>
				$("#dt_scdl_<?=$land_id?>").datepicker({
					weekStart: 1,
					daysOfWeekHighlighted: "6,0",
					autoclose: true,
					todayHighlight: true,
					format: 'dd.mm.yyyy',
					language: 'ru',
					timeFormat: "HH:mm",
					showTime: true,
					showMinute: true,
					showSecond: false,
					showMillisec: false,
					timeSeparator: ":",
				}).on('show', function() {
					  if ($(this).val() == "00.00.0000") {
						$(this).datepicker('update', '<?=date('d.m.Y')?>');  
					  }
					});
			</script>
			</div>
			<br>
			<br>

			<div class='card p-3 bg-light' >
			<label for='__land_razdel'>Устанавливать этап для новых регистраций <a href='javascript:wopen("razdel.php")' class='btn btn-sm btn-warning' target=''>этапы</a></label>
			<div class="input-group">
				<select name='land_razdel'  class='form-control'  id='__land_razdel'>
					<?
					$res1=$db->query("SELECT * FROM razdel WHERE del=0 AND id>0 ORDER BY razdel_num");
					print "<option value='0'>=не менять=</option> \n";
					while($r1=$db->fetch_assoc($res1)) {
						$sel=($r1['id']==$land_razdel)?"SELECTED":"";
						print "<option value='{$r1['id']}' $sel>{$r1['razdel_name']}</option> \n";
					}
					?>
				</select>
				<div class="input-group-append">
					<button type="submit" class="btn btn-primary btn-sm" name='do_save' value='yes'><i class="fa fa-save"></i></button>
				</div>
			</div>
			</div>
			<br>
			<br>

			<div class='card p-3 bg-light' >
			<label for='__land_tag'>Присваивать тэг для новых регистраций <a href='javascript:wopen("tags.php")' class='btn btn-sm btn-warning' target=''>тэги</a></label>
			<div class="input-group">
				<select name='land_tag'  class='form-control'  id='__land_tag'>
					<?
					$res1=$db->query("SELECT * FROM tags WHERE del=0 ORDER BY tag_name");
					print "<option value='0'>=не менять=</option> \n";
					while($r1=$db->fetch_assoc($res1)) {
						$sel=($r1['id']==$land_tag)?"SELECTED":"";
						print "<option value='{$r1['id']}' $sel>{$r1['tag_name']}</option> \n";
					}
					?>
				</select>
				<div class="input-group-append">
					<button type="submit" class="btn btn-primary btn-sm" name='do_save' value='yes'><i class="fa fa-save"></i></button>
				</div>
			</div>
			</div>
			<br>
			<br>

			<div class='card p-3 bg-light' >
			<label for='__land_man_id'>Назначать лида менеджеру</label>
			<div class="input-group">
				<select name='land_man_id'  class='form-control'  id='__land_man_id'>
					<?
					if(!$db->dlookup("id","users","del=0 AND access_level=4 AND id='$land_man_id'"))
						$db->query("UPDATE lands SET land_man_id=0 WHERE id='$land_id'");
					$res1=$db->query("SELECT * FROM users WHERE del=0 AND access_level=4 ORDER BY real_user_name");
					print "<option value='0'>=не менять=</option> \n";
					while($r1=$db->fetch_assoc($res1)) {
						$sel=($r1['id']==$land_man_id)?"SELECTED":"";
						print "<option value='{$r1['id']}' $sel>{$r1['real_user_name']}</option> \n";
					}
					?>
				</select>
				<div class="input-group-append">
					<button type="submit" class="btn btn-primary btn-sm" name='do_save' value='yes'><i class="fa fa-save"></i></button>
				</div>
			</div>
			</div>
			<br>
			<br>

			<div class='card p-3 bg-light' >
			<label for='__product_id'>Привязать лэндинг к товару/услуге <a href='javascript:wopen("products.php")' class='btn btn-sm btn-warning' target=''>Товары</a></label>
			<div class="input-group">
				<select name='product_id'  class='form-control' id='__product_id'>
					<?
					$res1=$db->query("SELECT * FROM product WHERE del=0");
					print "<option value='0'>=не привязывать=</option> \n";
					while($r1=$db->fetch_assoc($res1)) {
						$sel=($r1['id']==$product_id && $r1['id'])?"SELECTED":"";
						print "<option value='{$r1['id']}' $sel>({$r1['id']}) {$r1['descr']}</option> \n";
					}
					?>
				</select>
				<div class="input-group-append">
					<button type="submit" class="btn btn-primary btn-sm" name='do_save' value='yes'><i class="fa fa-save"></i></button>
				</div>
			</div>
			</div>
			<br>
			<br>

			<div class='card bg-light p-3' >
				<h3>Поля формы ввода данных на лэндинге <a href='<?=$land_url?>' target='_blank' title='перейти на лэндинг в новом окне'><span class="fa fa-arrow-circle-right"></span></a></h3>
				<div class="form-check-inline">
				  <label class="form-check-label">
					<input type="checkbox" class="form-check-input" name='fl_disp_phone' value="on" <?=$fl_disp_phone?> >Запрашивать телефон
				  </label>
				  <label class="form-check-label ml-3">
					<input type="checkbox" class="form-check-input" name='fl_disp_phone_rq' value="on" <?=$fl_disp_phone_rq?> title='обязательное поле'>(<span class='text-danger'  title='обязательное поле'>*</span>) 
				  </label>
				</div>

				<div class="form-check-inline">
				  <label class="form-check-label">
					<input type="checkbox" class="form-check-input" name='fl_disp_email' value="on" <?=$fl_disp_email?>>Запрашивать email
				  </label>
				  <label class="form-check-label ml-3">
					<input type="checkbox" class="form-check-input" name='fl_disp_email_rq' value="on" <?=$fl_disp_email_rq?> title='обязательное поле'>(<span class='text-danger'  title='обязательное поле'>*</span>) 
				  </label>
				</div>

				<div class="form-check-inline">
				  <label class="form-check-label">
					<input type="checkbox" class="form-check-input" name='fl_disp_city' value="on" <?=$fl_disp_city?>>Запрашивать город
				  </label>
				  <label class="form-check-label ml-3">
					<input type="checkbox" class="form-check-input" name='fl_disp_city_rq' value="on" <?=$fl_disp_city_rq?> title='обязательное поле'>(<span class='text-danger'  title='обязательное поле'>*</span>) 
				  </label>
				</div>

				<div class="form-check">
				  <label class="form-check-label">
					<input type="checkbox" class="form-check-input" name='fl_disp_comm' value="on" <?=$fl_disp_comm?>>Предлагать написать комментарий
				  </label>
				</div>

				<div class="form-group">
				  <label for="label_disp_comm">Название у комментария на форме ввода:</label>
				  <textarea type="text" name='label_disp_comm' class="form-control" id="label_disp_comm" rows='3' ><?=$label_disp_comm?></textarea>
				</div>
			</div>
			<div><button type="submit" class="btn btn-primary btn-lg " name='do_save' value='yes'>Записать</button></div>
			<br>
			<br>


			<div class='card p-3 bg-light'>
			<label for='land_url'>Ссылка на лэндинг</label>
				<div class="input-group">
				<input type='text' id='land_url' name='land_url' value='<?=$land_url?>' class='form-control' >
				<div class="input-group-append">
					<button type="submit" class="btn btn-primary btn-sm" name='do_save' value='yes'><i class="fa fa-save"></i></button>
					&nbsp;
					<a class='btn btn-primary btn-sm' href='<?=$land_url?>' target='_blank' title='перейти на лэндинг в новом окне'><span class="fa fa-arrow-circle-right"></span></a>
				</div>
				</div>
			<p>Ссылка на встроенный лэндинг или форму регистрации
				<a href='<?="https://for16.ru/d/$ctrl_dir/$land_num"?>' class='' target='_blank'><?="https://for16.ru/d/$ctrl_dir/$land_num"?></a>
			</p>
			<small id='' class='form-text text-muted'>Не заполнять, чтобы использовать встроенный лэндинг </small>
	<label for='code_tilda'>Код для вставки на внешних сайтах (тильда и т.п.)
		<small>Ссылка на документацию </small>
		<a href='#code_tilda' data-toggle="collapse"  data-parent="#section_<?=$land_num?>">+</a>
	</label>
	<div class='collapse'  id='code_tilda'>
	<small class='form-text text-muted'>
		<?	print "<div class='card bg-light py-3 px-2' >".nl2br(htmlspecialchars("
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fetch URL parameters from the current page URL
    const params = new URLSearchParams(window.location.search);
    const paramString = params.toString() ? (params.toString().includes('?') ? '&' : '?') + params.toString() : '';

    // Update <a> tags
    const links = document.querySelectorAll('a[href*=\"for16.ru\"]');
    links.forEach(link => {
        if (paramString) {
            link.href += paramString;
        }
    });

    // Update <iframe> tags in the source code
    const iframes = document.querySelectorAll('iframe[src*=\"for16.ru\"]');
    iframes.forEach(iframe => {
        if (paramString) {
            // Append parameters to the existing src URL of the iframe
            iframe.src += paramString;
        }
    });
});
</script>
			"))."</div>";
		?>
	</small>
	</div>
			</div>



			<br>
			<br>

			<label for='thanks_pic'>Загрузить изображение для лэндинга</label>
			<div><?s_panel_upload_file($upload_id='land_pic_'.$land_num,$upload_dir='tg_files/',$land_num);?></div>
			<small id='' class='form-text text-muted'>Рекомендуемый размер изображения 1080x1080, не более 300К, только JPG </small>
			<br>
			<br>

			<div>
			<label for='land_txt'><b>Текст лэндинга</b></label>
			<div class="form-check-inline ml-3">
			  <label class="form-check-label">
				<input type="checkbox" class="form-check-input" name='land_type' value='on' <?=($land_type==0) ? '' : 'CHECKED'?>> на десктопах текст переносить вниз
			  </label>
			</div>
			<textarea id='land_txt' name='land_txt' class='form-control tinymce' rows='12' ><?=$land_txt?></textarea>
			<small id='' class='form-text text-muted'>Например: <br>
				<?print nl2br(htmlspecialchars("Как восстановить здоровье занимаясь йогой 3 раза в неделю дома
				Получите бесплатно PDF каталог асан йоги для начинающих.
				"));
				?>
			</small>
			<p><a href='<?=$land_url?>' class='' target='_blank'><?=$land_url?></a> ссылка на ваш лэндинг.</p>
			</div>
			
			<div class='card p-1 bg-light my-2' >
				<label for='btn_label'>Текст кнопки регистрации / оплаты</label>
				<div class="input-group">
				<input type='text' id='btn_label' name='btn_label' value='<?=$btn_label?>' class='form-control' >
				</div>
			</div>

			<div><button type="submit" class="btn btn-primary btn-lg " name='do_save' value='yes'>Записать</button></div>
			<br>
			<br>

			<label for='thanks_pic'>Загрузить изображение для страницы благодарности</label>
			<div><?s_panel_upload_file($upload_id='thanks_pic_'.$land_num,$upload_dir='tg_files/',$land_num);?></div>
			<small id='' class='form-text text-muted'>Рекомендуемый размер изображения 1080px по ширине, не более 300К, только JPG </small>
			<br>
			<br>

			<label for='thanks_txt'>Текст страницы благодарности</label>
			<textarea id='thanks_txt' name='thanks_txt' class='form-control tinymce' rows='6' ><?=$thanks_txt?></textarea>
			<small id='' class='form-text text-muted'>Рекомендуем: <br>
				<?print nl2br(htmlspecialchars("Поздравляем! Вы успешно зарегистрированы.
				Чтобы получить все бонусы перейдите в телеграм (бонусы будут высланы вам в личном сообщении)
				"));
				?>
			</small>
			<p></p>

			<br>
			<br>

			<label for='bot_first_msg'>Первое сообщение чат бота при подписке</label>
			<textarea id='bot_first_msg' name='bot_first_msg'  class='form-control' rows='6' ><?=$bot_first_msg?></textarea>
			<small id='' class='form-text text-muted'>Рекомендуем: <br>
				<?
				print nl2br("<div class='small card p-2' >Еще раз благодарим за регистрацию, обещанные материалы вы можете получить по ссылке .....</div>");
				print nl2br("<div class='small card p-2' >Еще раз благодарим за регистрацию в партнерской программе

Ваша партнерская ссылка : ссылка_на_подписной_лэндинг/?bc={{partner_code}}

Личный кабинет: {{cabinet_link}}</div>");
				print "<p class='small card p-2 bg-light' ><b>Вы можете использовать следующие коды для подстановки значений:</b> ".nl2br($db->prepare_msg_codes())."</p>";
				?>
			</small>

			<br>
			<br>
			<input type='hidden' name='land_id' value='<?=$land_id?>'>
			<input type='hidden' name='land_num' value='<?=$land_num?>'>
			<input type='hidden' name='csrf_token' value='<?=$_SESSION['csrf_token']?>'>
			<input type='hidden' name='csrf_name' value='s_panel_lands'>
			<div><button type="submit" class="btn btn-primary btn-lg btn-block" name='do_save' value='yes'>Записать</button></div>
		</div>
	<? } ?>
	</div>
</div>
</form>
<script>
// Инициализация popover
$(document).ready(function(){
    $('[data-toggle="popover"]').popover(); 
});
</script>

<?

}

?>
</div>

  <script>
    tinymce.init({
      selector: 'textarea.tinymce',
		menubar: false,
		language: 'ru',
      plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
      toolbar: 'undo redo |heading1 heading2 heading3 blocks fontfamily fontsize | bold italic underline strikethrough forecolor backcolor | align lineheight | checklist numlist bullist indent outdent | link image media | emoticons charmap | removeformat',
	  formats: {
		heading1: { block: 'h1', classes: 'text-center' },
		heading2: { block: 'h2', classes: 'text-center' },
		heading3: { block: 'h3', classes: 'text-center' },
	  },
      tinycomments_mode: 'embedded',
      tinycomments_author: 'Author name',
      mergetags_list: [
        { value: 'First.Name', title: 'First Name' },
        { value: 'Email', title: 'Email' },
      ]
    });
  </script>

<script>
function transformToInput(spanElement) {
    var content = spanElement.innerText;
    var originalContent = content;

    var inputField = document.createElement("input");
    inputField.setAttribute("type", "text");
    inputField.setAttribute("value", content);
	inputField.style.border = '0'; // Remove the border
	inputField.style.width = '60px';
    spanElement.parentNode.replaceChild(inputField, spanElement);
    inputField.focus();

    var newValue = '';

    inputField.addEventListener("keypress", function(event) {
        if (event.key === 'Enter') {
			event.preventDefault(); // Prevent form submission
            newValue = inputField.value;
            spanElement.innerText = newValue;
            inputField.setAttribute("value", newValue);

            // AJAX request with jQuery on Enter key press
            $.ajax({
                url: 'jquery.php',
                type: 'GET',
                data: { ch_land_num: 'yes', oldvalue: originalContent, newvalue: newValue },
                success: function(response) {
                    console.log('AJAX request successful');
                    console.log(originalContent);
                    console.log(newValue);
                    location.reload();
                },
                error: function(xhr, status, error) {
                    console.error('AJAX request failed');
                }
            });
        }
    });

    inputField.addEventListener("blur", function() {
        if (inputField.parentNode.contains(inputField)) {
            if (inputField.value !== originalContent) {
                inputField.value = originalContent;
                spanElement.innerText = originalContent;
            }
            inputField.parentNode.replaceChild(spanElement, inputField);
        }
    });
}
</script>


<?
$top->bottom();
?>
