<?
//header('X-Frame-Options: ALLOW-FROM https://wwl.winwinland.ru');
include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";
include "../init.inc.php";
$csrf_token_land = bin2hex(random_bytes(32));
$_SESSION['csrf_token_land'] = $csrf_token_land;
$db->query("INSERT INTO csrf SET token='$csrf_token_land',token_name='csrf_token_land',tm='".time()."'");

$db=new db($database);
$for16_url=$db->get_for16_url($ctrl_dir);
$for16_cwd=$db->get_for16_cwd($ctrl_dir);
if(!isset($agreements_index)) {
	$agreements_index="
							<label class='small mute' >
								<input id='chk1' type='checkbox' value='' $policy_checked >
								отправляя форму, вы соглашаетесь с 
								<a href='$pp' target='_blank'>политикой об обработке персональных данных</a>, 
								принимаете условия
								<a href='$dogovor' class='' target='_blank'>пользовательского соглашения</a>
								и даете согласие на
								<a href='$agreement' class='' target='_blank'>получение информационных материалов</a>
							</label>
						";
}

if(isset($_GET['bc'])) {
	$bc=$db->promocode_validate($_GET['bc']);
}
$cards0ctrl_id=(isset($_GET['ctrl_id'])) ? intval($_GET['ctrl_id']) : 0;

$vk_id=0; $uid_md5=0; $uid=0;
if(isset($_GET['uid'])) {
	$uid=$db->get_uid(intval($_GET['uid']));
	$uid_md5= $uid ? $db->uid_md5($uid) : 0;
	$vk_id = intval($_GET['uid'])>0 ? intval($_GET['uid']) : 0;
}

//$db->notify_me(print_r($_GET,true));


$title='Добро пожаловать!';
$descr=$title;
$og_url=$land;
$favicon="https://for16.ru/images/favicon.png";

if(preg_match("|[0-9]+$|",getcwd(),$m)) {
	if(!$land_num=intval($m[0]))
		$land_num=1;
	//~ if(!$db->dlookup("id","lands","land_num='$land_num' AND del=0"))
		//~ $land_num=1;
}

$db->connect('vkt');
	$pp=$db->dlookup("pp","0ctrl","id=$ctrl_id");
	$land_txt_1=$db->dlookup("land_txt","0ctrl","id='$ctrl_id'");
	$land_txt_p=$db->dlookup("land_txt_p","0ctrl","id='$ctrl_id'");
$db->connect($database);

$thanks_url="$for16_url/thanks.php?from=$land_num";
$thanks_pic=(file_exists("$for16_cwd/tg_files/thanks_pic_$land_num.jpg"))?"<img src='$for16_url/tg_files/thanks_pic_$land_num.jpg' class='img-fluid' >":"";
$thanks_txt=$db->dlookup("thanks_txt","lands","land_num='$land_num' AND del=0");
$bot_first_msg=$db->dlookup("bot_first_msg","lands","land_num='$land_num' AND del=0");
$target=(empty($thanks_pic) && empty($thanks_txt) && !empty($bot_first_msg)) ? "_blank" : "";

if($land_num==1 && !$db->dlookup("id","lands","land_num='1' AND del=0")) {
	$title="Добро пожаловать";
	$land_pic=(file_exists('../tg_files/land_pic.jpg'))?"<img src='../tg_files/land_pic.jpg' class='img-fluid' >":"";
	$land_txt=$land_txt_1;
	$tm_scdl=0;
	$og_image="$for16_url/tg_files/land_pic.jpg";
	$fl_disp_phone='block';
	$fl_disp_email='none';
	$fl_disp_comm='none';
	$label_disp_comm='none';
	$btn_label='Регистрация';
} elseif($land_num==2 && !$db->dlookup("id","lands","land_num='2' AND del=0")) {
	$thanks_url="$for16_url/thanks_p.php";
	$title="Добро пожаловать";
	$land_pic=(file_exists('../tg_files/land_pic_p.jpg'))?"<img src='../tg_files/land_pic_p.jpg' class='img-fluid' >":"";
	$land_txt=$land_txt_p;
	$tm_scdl=0;
	$og_image="../tg_files/land_pic_p.jpg";
	$fl_disp_phone='block';
	$fl_disp_email='block';
	$fl_disp_comm='none';
	$label_disp_comm='none';
	$btn_label='Регистрация';
} else {
	$r=$db->fetch_assoc($db->query("SELECT * FROM lands WHERE land_num='$land_num' AND del=0"));
	if(!$r) {
		$r=$db->fetch_assoc($db->query("SELECT * FROM lands WHERE del=0 ORDER BY land_num"));
		$land_num=$r['land_num'];
	}
	$land_pic=(file_exists("$for16_cwd/tg_files/land_pic_$land_num.jpg"))?"<img src='$for16_url/tg_files/land_pic_$land_num.jpg' class='img-fluid' >":"";
	$title=$r['land_name'];
	$land_txt=$r['land_txt'];
	$land_type=$r['land_type'];
	$tm_scdl=$r['tm_scdl'];
	$og_image="$for16_url/tg_files/land_pic_$land_num.jpg";
	$fl_disp_phone=$r['fl_disp_phone']?'block':'none';
	$fl_disp_email=$r['fl_disp_email']?'block':'none';
	$fl_disp_city=$r['fl_disp_city']?'block':'none';
	$fl_disp_comm=$r['fl_disp_comm']?'block':'none';
	$fl_disp_phone_rq=$r['fl_disp_phone_rq']?'*':'';
	$fl_disp_email_rq=$r['fl_disp_email_rq']?'*':'';
	$fl_disp_city_rq=$r['fl_disp_city_rq']?'*':'';
	$label_disp_comm=$r['label_disp_comm'];
	$btn_label=$r['btn_label'];
	if(empty($btn_label)) {
		$btn_label='Регистрация';
		$db->query("UPDATE lands SET btn_label='$btn_label' WHERE land_num='$land_num' AND del=0");
	}
}

//print "HERE_$btn_label";
if($product_id=$db->dlookup("product_id","lands","land_num='$land_num' AND del=0")) {
	$uid=0;
	if(isset($_GET['uid'])) {
		$uid=$db->get_uid($_GET['uid']);
		if($db->is_md5($_GET['uid']))
			$disp_contacts=true;
	}
	if($uid)
		$_SESSION['vk_uid']=$uid;

	if(isset($_SESSION['vk_uid'])) {
		$uid=intval($_SESSION['vk_uid']);
		if(empty($client_email)) {
			$r=$db->fetch_assoc($db->query("SELECT * FROM cards WHERE uid='$uid' AND del=0"));
			if($r) {
				$client_phone=$r['mob']; $client_name=$r['name']; $client_email=$r['email'];
			}
		}
	} else 
		$uid=0;
	$uid_md5=($uid)?$db->uid_md5($uid):0;
	$t=0;
	$link="$for16_url/order.php?s=0&t=$t&product_id=$product_id&land_num=$land_num&uid=$uid_md5&bc=$bc";
	if($btn_label=='Регистрация')
		$btn_label='Оплатить';
	$btn="<a  href='$link'  class='btn btn-info btn-lg' target=''>$btn_label</a>";

	$r=$db->fetch_assoc($db->query("SELECT * FROM product WHERE id='$product_id'"));
	$land_txt=preg_replace("/\{\{price0\}\}/",$r['price0'],$land_txt);
	$land_txt=preg_replace("/\{\{price1\}\}/",$r['price1'],$land_txt);
	$land_txt=preg_replace("/\{\{price2\}\}/",$r['price2'],$land_txt);
	$land_txt=preg_replace("/\{\{descr\}\}/",$r['descr'],$land_txt);
	$land_txt=preg_replace("/\{\{term\}\}/",$r['term'],$land_txt);
	$land_txt=preg_replace("/\{\{fee_1\}\}/",$r['fee_1'],$land_txt);
	$land_txt=preg_replace("/\{\{fee_2\}\}/",$r['fee_2'],$land_txt);
	
} else {
	$btn="<a  href='#' data-toggle='modal' data-target='#regModal' class='btn btn-info btn-lg' target=''>$btn_label</a>";
}
$policy_checked=($ctrl_id != 74) ?"CHECKED" : "";

include "$for16_cwd/land_top.inc.php";
if(isset($_GET['err'])) {
	print "<p class='alert alert-warning' >".htmlspecialchars($_GET['err'])."</p>";
}
if(!empty($land_pic) ) {
	if($land_type==0) {
	?>
	<div class='container-fluid' >
		<div class='row' >
			<div class='text-center col-md-6 m-0 p-0' ><?=$land_pic?></div>
			<div class='col-md-6 pl-md-5' >
				<div class='container' >
					<?=$land_txt?>
					<div class='text-center mb-5 mt-4' >
						<?=$btn?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?
	} elseif($land_type==1) {
	?>
	<div class='container-fluid' >
		<div class='row mx-auto' style='max-width:800px;'>
			<div class='text-center col-md-12 m-0 p-0' ><?=$land_pic?></div>
			<div class='col-md-12 pl-md-5' >
				<div class='container' >
					<?=$land_txt?>
					<div class='text-center mb-5 mt-4' >
						<?=$btn?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?
	}

	//print "uid=$uid tg_code=$tg_code<br>";
	//$db->print_r($_POST);
	?>

	<div class="modal fade" id="regModal" tabindex="-1" role="dialog" aria-labelledby="regModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title text-center pb-4" id="regModalLabel">Укажите контакты</h4>
				</div>
				<div class="modal-body"  id="regMsg_body">
					<form  method='POST' id='f_reg' action='<?=$thanks_url?>'>
						<div class="form-group"> <label for="regName">Имя и фамилия <span class='text-danger' >*</span></label> <input type="text" class="form-control" id="regName" placeholder="Имя" name='regName'> </div>
						<div class="form-group" style='display:<?=$fl_disp_phone?>;'> <label for="regPhone">Телефон <span class='text-danger' ><?=$fl_disp_phone_rq?></span></label> <input type="phone" class="form-control" id="regPhone" placeholder="Телефон" name='regPhone'> </div>
						<div class="form-group" style='display:<?=$fl_disp_email?>;'> <label for="regEmail">E-mail <span class='text-danger' ><?=$fl_disp_email_rq?></span></label> <input type="email" class="form-control" id="regEmail" placeholder="Email" name='regEmail'> </div>
						<div class="form-group" style='display:<?=$fl_disp_city?>;'>
							<label for="regCity">Город <span class='text-danger' ><?=$fl_disp_city_rq?></span></label>
							<input type="text" class="form-control" id="regCity" placeholder="Город" name='regCity' autocomplete="off" >
							<div id="cityList" style='display:<?=$fl_disp_city?>;'></div>
						</div>

						<div class="form-group" style='display:<?=$fl_disp_comm?>;'>
						  <label for="comment"><?=$label_disp_comm?></label>
						  <textarea class="form-control" rows="3" id="comment" name='regComm'></textarea>
						</div>
						<div class='checkbox small text-muted'>
							<?=$agreements_index?>
						</div>
						<input type='hidden' id='bc' name='bc' value='<?=$bc?>'>
						<input type='hidden' id='uid' name='uid' value='<?=$uid_md5?>'>
						<input type='hidden' id='vk_id' name='vk_id' value='<?=$vk_id?>'>
						<input type='hidden' id='land_num' name='land_num' value='<?=$land_num?>'>
						<input type='hidden' id='tm_scdl' name='tm_scdl' value='<?=$tm_scdl?>'>
						<input type='text' name='tzoffset' value='0' id='tzoffset' style='display:none;'>
						<input type='hidden' name='cards0ctrl_id' value='<?=$cards0ctrl_id?>' id='ctrl_id'>
						<input type='hidden' name='csrf_token_land' value='<?=$csrf_token_land?>'>
						<input type='hidden' name='csrf_name' value='index_1'>
	<?
		if(isset($_GET['utm_campaign']))
			print "<input type='hidden' name='utm_campaign' value='{$_GET['utm_campaign']}'>\n";
		if(isset($_GET['utm_content']))
			print "<input type='hidden' name='utm_content' value='{$_GET['utm_content']}'>\n";
		if(isset($_GET['utm_medium']))
			print "<input type='hidden' name='utm_medium' value='{$_GET['utm_medium']}'>\n";
		if(isset($_GET['utm_source']))
			print "<input type='hidden' name='utm_source' value='{$_GET['utm_source']}'>\n";
		if(isset($_GET['utm_term']))
			print "<input type='hidden' name='utm_term' value='{$_GET['utm_term']}'>\n";
		if(isset($_GET['utm_ab']))
			print "<input type='hidden' name='utm_ab' value='{$_GET['utm_ab']}'>\n";
	?>
					</form>
					<div class='contact-us' ><div class='contact-us-bgimage grid-margin' id='regMsg_' style='display:none;'></div></div>
				</div>
				<div class="modal-footer"  id="regMsg_footer">
					<button type="button" class="btn btn-success"  form="f_reg" id='regSend'>Отправить</button>
				</div>
			</div>
		</div>
	</div>

<? 	} elseif($product_id) {
		print "<script>location='$link'</script>";
	} else {

	if(!isset($_GET['ok'])) {?>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <form  method='POST' id='f_reg' action='<?=$thanks_url?>' target='<?=$target?>'>
                            <div class="form-group">
                                <label for="name">Фамилия и имя</label>
                                <input id="regName" name='regName' type="text" class="form-control" placeholder="Введите фамилию и имя" >
                            </div>
                            <div class="form-group" style='display:<?=$fl_disp_phone?>;'>
                                <label for="phone">Телефон <span class='text-danger' ><?=$fl_disp_phone_rq?></span></label>
                                <input id="regPhone" name='regPhone' type="text" class="form-control" placeholder="Введите номер телефона">
                            </div>
                            <div class="form-group" style='display:<?=$fl_disp_email?>;'>
                                <label for="email">Эл.почта <span class='text-danger' ><?=$fl_disp_email_rq?></span></label>
                                <input  id="regEmail" name='regEmail' type="email" class="form-control" placeholder="Введите емэйл">
                            </div>
                            <div class="form-group" style='display:<?=$fl_disp_city?>;'>
                                <label for="regCity">Город <span class='text-danger' ><?=$fl_disp_city_rq?></span></label>
                                <input  id="regCity" name='regCity' type="text" class="form-control" placeholder="Введите город" autocomplete="off" >
								<div id="cityList" style='display:<?=$fl_disp_city?>;'></div>
                            </div>
							<div class="form-group" style='display:<?=$fl_disp_comm?>;'>
							  <label for="comment"><?=$label_disp_comm?></label>
							  <textarea class="form-control" rows="3" id="comment" name='regComm'></textarea>
							</div>

							<div class='checkbox small text-muted mt-3'>
								<?=$agreements_index?>
							</div>


						<input type='hidden' id='bc' name='bc' value='<?=$bc?>'>
						<input type='hidden' id='uid' name='uid' value='<?=$uid_md5?>'>
						<input type='hidden' id='vk_id' name='vk_id' value='<?=$vk_id?>'>
						<input type='hidden' id='land_num' name='land_num' value='<?=$land_num?>'>
						<input type='hidden' id='tm_scdl' name='tm_scdl' value='<?=$tm_scdl?>'>
						<input type='text' name='tzoffset' value='0' id='tzoffset' style='display:none;'>
						<input type='hidden' name='csrf_token_land' value='<?=$csrf_token_land?>'>

 <?
	if(isset($_GET['utm_campaign']) && $_GET['utm_campaign']!='null')
		print "<input type='hidden' name='utm_campaign' value='{$_GET['utm_campaign']}'>\n";
	if(isset($_GET['utm_content']) && $_GET['utm_content']!='null')
		print "<input type='hidden' name='utm_content' value='{$_GET['utm_content']}'>\n";
	if(isset($_GET['utm_medium']) && $_GET['utm_medium']!='null')
		print "<input type='hidden' name='utm_medium' value='{$_GET['utm_medium']}'>\n";
	if(isset($_GET['utm_source']) && $_GET['utm_source']!='null')
		print "<input type='hidden' name='utm_source' value='{$_GET['utm_source']}'>\n";
	if(isset($_GET['utm_term']) && $_GET['utm_term']!='null')
		print "<input type='hidden' name='utm_term' value='{$_GET['utm_term']}'>\n";
	if(isset($_GET['utm_ab']) && $_GET['utm_ab']!='null')
		print "<input type='hidden' name='utm_ab' value='{$_GET['utm_ab']}'>\n";
?>
                           <button type="button" class="btn btn-primary" form="f_reg" id='regSend'>Отправить</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?} else
			print "<h4 class='text-center my-5' >Благодарим за регистрацию!</h4>";
    ?>
<? } ?>

<script type="text/javascript">
	//console.log('test');
	$("#regSend").click(function() {
		//event.preventDefault();
		//alert($("#c_name").val());
		if($("#regName").val().trim()=="") {
			alert("Необходимо указать имя!");
		}
		<?if($r['fl_disp_phone'] && $r['fl_disp_phone_rq'])  { ?>
			else if($("#regPhone").val().trim()=="") {
				alert("Укажите, пожалуйста, телефон для связи!");
			}
		<?}?>
		<?if($r['fl_disp_email'] && $r['fl_disp_email_rq']) { ?>
			else if($("#regEmail").val().trim()=="") {
				alert("Укажите, пожалуйста, email!");
			}
		<?}?>
		<?if($r['fl_disp_city'] && $r['fl_disp_city_rq']) { ?>
			else if($("#regCity").val().trim()=="") {
				alert("Укажите, пожалуйста, город!");
			}
		<?}?>
		else if(!$("#chk1").is(":checked")) {
			alert("Необходимо согласиться с обработкой персональных данных !");
		//~ } else if(!$("#chk2").is(":checked")) {
			//~ alert("Необходимо согласиться с договором оферты !");
		} else {
			$( "#f_reg" ).submit();
			<? if($target=='_blank')
				print "window.location.href = '?ok=yes';";
			?>
		}
	});

	$(document).ready(function(){

		var tzOffset = new Date().getTimezoneOffset();
		document.getElementById('tzoffset').value = tzOffset;

		$('#regCity').keyup(function(){
			console.log('click');
			var msgs_city = $(this).val();
			if(msgs_city != ''){
				$.ajax({
					url:"$for16_url/jquery.php",
					method:"POST",
					data:{msgs_city:msgs_city},
					success:function(data){
						$('#cityList').fadeIn();
						$('#cityList').html(data);
					}
				});
			}
		});
		$(document).on('click', 'li', function(){
			$('#regCity').val($(this).text());
			$('#cityList').fadeOut();
		});
	});
</script>

<? include "$for16_cwd/land_bottom.inc.php"; ?>
