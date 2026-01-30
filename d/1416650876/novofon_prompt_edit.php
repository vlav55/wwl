<?
include "/var/www/vlav/data/www/wwl/inc/top.class.php";
include "init.inc.php";
$db=new top($database,'Edit prompt',false,$favicon);

if(!isset($_SESSION['man_number']))
	$_SESSION['man_number']=$db->dlookup("sip_internal_number","users","del=0 AND sip_internal_number>0");
else {
	if(!$man_number=$_SESSION['man_number'])
		$man_number=$db->dlookup("sip_internal_number","users","del=0 AND sip_internal_number>0");
}
if(!isset($_SESSION['novofon_prompt_test']))
	$_SESSION['novofon_prompt_test']="https://for16.ru/d/1000/calls/?fname=calls/166024375_101_78006000757_15-05-2025_16-56.txt";
else
	$fname=$_SESSION['novofon_prompt_test'];

if(isset($_POST['cancel'])) {
	print "<script>location='?'</script>";
	exit;
}
if(isset($_POST['ch_man'])) {
    $_SESSION['man_number']=intval($_POST['man_number']);
}
if (isset($_POST['save_prompt'])) {
    $prompt = isset($_POST['prompt']) ? mb_substr($_POST['prompt'],0,4096) : '';
    $man_number=intval($_POST['man_number']);
    $vsegpt_model=mb_substr(trim($_POST['vsegpt_model']),0,128);
    $_SESSION['man_number']=$man_number;
    //file_put_contents($fname, $content);
    if(!$db->dlookup("id","users_pbx","man_number='$man_number'"))
		$db->query("INSERT INTO users_pbx SET vsegpt_model='".$db->escape($vsegpt_model)."',man_number='$man_number',prompt='".$db->escape($prompt)."' ");
	else
		$db->query("UPDATE users_pbx SET  vsegpt_model='".$db->escape($vsegpt_model)."',prompt='".$db->escape($prompt)."' WHERE man_number='$man_number'");
    echo '<div class="alert alert-success mt-3">Content saved successfully!</div>';
}

$man_number=$_SESSION['man_number'];
$prompt=$db->dlookup("prompt","users_pbx","man_number='$man_number'",0);
$vsegpt_model=$db->dlookup("vsegpt_model","users_pbx","man_number='$man_number'");

if(isset($_GET['test'])) {
	if($txt=file_get_contents($_GET['fname'])) {
		$_SESSION['novofon_prompt_test']=$_GET['fname'];
		$fname=$_SESSION['novofon_prompt_test'];
		$p[]=['role' => 'system', 'content' => "You are a large language model.
		Carefully heed the user's instructions.
		Respond without Markdown."];
		$p[]=['role' => 'user', 'content' => $prompt."\n".$txt];
		//~ print "$vsegpt_secret,$p,$vsegpt_model";
		//~ print_r($p);
		$res=$db->vsegpt($vsegpt_secret,$p,$vsegpt_model);
		?>
		<div class='card p-3 my-4 bg-light' ><?=nl2br($res)?></div>
		<?
		preg_match('/Итог=(\d+)/u', $res, $m);
		$val=isset($m[1]) ? intval($m[1]) : 0;
		print "<br> VAL=$val";
	} else
		print "<p class='alert alert-warning' >$fname not found</p>";
}

?>
<style>
</style>
<div class="container mt-5">
    <h2 class='text-center' >Prompt editor</h2>
    <form method='POST' action="?">
        <div class="form-group mb-2">
            <label for="man_number" class="col-form-label">Caller number:</label>
            <div class="d-flex justify-content-between">
                <select class="form-control mr-2 flex-grow-1" id="man_number" name="man_number">
                    <?php
                        $res1 = $db->query("SELECT * FROM users WHERE sip_internal_number > 0 AND del = 0");
                        while ($r1 = $db->fetch_assoc($res1)) {
                            $sel = ($man_number == $r1['sip_internal_number']) ? "SELECTED" : "";
                            print "<option $sel value='{$r1['sip_internal_number']}'>{$r1['real_user_name']}</option>";
                        }
                    ?>
                </select>
                <button type='submit' class='btn btn-primary' name='ch_man' value='yes'>Переключить</button>
            </div>
        </div>
		<div class="form-group mx-sm-3 mb-2">
			<label for="vsegpt_model" class="">AI model:</label>
			<input type='text' name='vsegpt_model' value='<?=$vsegpt_model?>' class='form-control' >
		</div>
        <div class="form-group">
            <textarea class="form-control" id="prompt" name='prompt' rows="20" ><?php echo htmlspecialchars($prompt); ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary" name='save_prompt' value='yes'>Save</button>
        <button type="submit" class="btn btn-warning" name='cancel' value='yes'>Cancel</button>
    </form>

	<form>
		<div class="form-group mx-sm-3 mb-2 mt-5">
			<label for="fname" class="">Тестовый разговор (ссылка на файл):</label>
			<input type='text' name='fname' value='<?=$fname?>' class='form-control' >
		</div>
        <button type="submit" name='test' value='yes' class="btn btn-primary">Протестировать</button>
        <a href='<?=str_replace(".txt",".json",$fname)?>' class='btn btn-info' target='_blank'>View</a>
	</form>
    
</div>

<? 
include "/var/www/vlav/data/www/wwl/inc/bottom.class.php";
?>
