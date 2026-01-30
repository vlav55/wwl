<?
if(isset($_GET['ajax'])) {
	//print "HERE";
	if(isset($_GET['remove'])) {
		$db=new db("vklist2");
		$gid=intval($_GET['gid']);
		$cid=intval($_GET['cid']);
		if($_GET['fl']==1)
			$db->query("UPDATE vklist2 SET del=1,cid='$cid' WHERE gid='$gid'",0);
		else
			$db->query("UPDATE vklist2 SET del=0,cid='0' WHERE gid='$gid'",0);
	}
	if(isset($_GET['fav'])) {
		$db=new db("vklist2");
		$gid=intval($_GET['gid']);
		$cid=intval($_GET['cid']);
		if($_GET['fl']==1)
			$db->query("UPDATE vklist2 SET fav=1,cid='$cid' WHERE gid='$gid'",0);
		else
			$db->query("UPDATE vklist2 SET fav=0,cid='0' WHERE gid='$gid'",0);
	}
	exit;
}


$db=new top($database,"VKlist2",false, $favicon);
$vk=new vklist_api($db->get_first_working_acc()['token']);
$bs=new bs;

if(!isset($_SESSION['where']))
	$where="1";
if(!isset($_SESSION['filter_city']))
	$_SESSION['filter_city']=0;
if(!isset($_SESSION['filter_country']))
	$_SESSION['filter_country']=1;
if(!isset($_SESSION['filter_grp']))
	$_SESSION['filter_grp']="";
if(isset($_GET['filter_country'])) {
	$_SESSION['filter_country']=intval($_GET['filter_country']);
}
if(isset($_GET['filter_city'])) {
	$_SESSION['filter_city']=intval($_GET['filter_city']);
	$_SESSION['where']="del=0  AND city_id='{$_SESSION['filter_city']}' AND country_id='{$_SESSION['filter_country']}'";
}
if(isset($_GET['filter_grp_set'])) {
	if($res=preg_match("|https://vk.com/(.*)|",$_GET['filter_grp'],$m)) {
		$_SESSION['filter_grp']=$_GET['filter_grp'];
		$grp_domen=trim($m[1]);
		//print "HERE $grp_domen";
		$url = 'https://api.vk.com/method/groups.getById';
		$params=array('v'=>5.84,'access_token'=>$vk->token, 'group_ids'=>$grp_domen,'fields'=>"");
		$res=json_decode(file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params))))),true);
		//$db->print_r($res);
		if(isset($res['response'][0]['id'])) { //OK
			$filter_grp_id=intval($res['response'][0]['id']);
			$_SESSION['where']="gid='$filter_grp_id'";
			$_GET['rand']="yes";
			$_GET['from']=0;
		} else {
			print "<div class='alert alert-warning' >Группа задана неправильно</div>";
			$filter_grp_id=0;
			$_SESSION['where']="1";
		}
	} else 
			print "<div class='alert alert-warning' >Группа задана неправильно</div>";
}
if(isset($_GET['filter_grp_clr'])) {
	$_SESSION['filter_grp']="";
}
$cnt_limit=10;
//$filter_city_id=$_SESSION['filter_city'];
//$filter_country_id=$_SESSION['filter_country'];
$filter_grp=$_SESSION['filter_grp'];
$where=$_SESSION['where'];
if(empty(trim($where)))
	$where="1";

$db->connect("vklist2"); //////////////////////////////////////////
?>
<div class='well form-group' >
	<form name='f1' class='form-inline' >
		<div>
			<label for='filter_country'>Country:</label>
			<select name='filter_country' id='filter_country' class="form-control">
			<?
			$res=$db->query("SELECT * FROM country WHERE 1");
			while($r=$db->fetch_assoc($res)) {
				print "<option value='{$r['id']}'>{$r['country_name']}</option>";
			}
			?>
			</select>
			<label for='filter_city'>City:</label>
			<select name='filter_city' id='filter_city' onchange='f1.submit()' class="form-control">
			<option value='0'>-----</option>
			<?
			$res=$db->query("SELECT * FROM city WHERE 1");
			while($r=$db->fetch_assoc($res)) {
				$sel=($_SESSION['filter_city']==$r['id'])?"selected":"";
				print "<option value='{$r['id']}' $sel>{$r['city_name']}</option>";
			}
			?>
			</select>
		</div>
		<div>
			<div class="form-group">
			  <label for="filter_grp">Группа:</label>
			  <input type="text" class="form-control" id="filter_grp" name="filter_grp"  value="<?=$filter_grp?>" style="width:300px;">
			  <button class="btn btn-default" type="submit" name="filter_grp_set"><i class="glyphicon glyphicon-search"></i></button>
			  <button class="btn btn-default" type="submit" name="filter_grp_clr"><i class="glyphicon glyphicon-remove"></i></button>

			</div>
		</div>
	</form>
	</div>
<?
$res=$db->query("SELECT gid FROM vklist2 WHERE  $where GROUP BY gid HAVING COUNT(gid) <$cnt_limit");
$num_recs=$db->num_rows($res);
if(isset($_GET['disp_contacts'])) {
	print $bs->button_href($text="Вернуться", $href="javascript:history.back()", $style="warning");
} else {
	print "<h4>Всего выборка : <span class='badge' >$num_recs</span><h4>";
	print $bs->button_href($text="Случайная выборка", $href="?rand=yes", $style="primary");
	print "&nbsp;";
	print $bs->button_href($text="Фавориты", $href="?fav=yes", $style="success");
	print "&nbsp;";
	print $bs->button_href($text="Отправленные", $href="?sent=yes", $style="warning");
}
	
if(isset($_GET['do_del'])) {
	$uid=intval($_GET['uid']);
	$cid=intval($_GET['cid']);
	if($uid) {
		$db->query("UPDATE vklist2 SET del=1,tm='".time()."',cid='$cid' WHERE uid='$uid'");
		print "<div class='alert alert-info' >Удален из базы <a href='https://vk.com/id$uid' class='' target='_blank'>https://vk.com/id$uid</a></div>";
	}
}
if(isset($_GET['do_del_grp'])) {
	$gid=intval($_GET['gid']);
	$cid=intval($_GET['cid']);
	if($gid) {
		$db->query("UPDATE vklist2 SET del=1,tm='".time()."',cid='$cid' WHERE gid='$gid'");
		print "<div class='alert alert-info' >Группа удалена из базы <a href='https://vk.com/public$gid' class='' target='_blank'>https://vk.com/public$gid</a></div>";
	}
}

if(isset($_GET['fav'])) {
	$res=$db->query("SELECT gid,COUNT(uid) AS cnt FROM vklist2
					WHERE cid='$customer_id' AND fav=1 AND tm=0
					GROUP BY gid HAVING COUNT(gid) <$cnt_limit
					ORDER BY tm DESC");
	print "<div class='alert alert-success' > Фавориты - <span class='badge' >".$db->num_rows($res)."</span> (выводятся последние 50)</div>";
	$n=0;
	while($r=$db->fetch_assoc($res)) {
		$gids.="{$r['gid']},";
		if($n++==50)
			break;
	}
	$res=vk_grpinfo($gids,$vk->token);
	foreach($res['response'] AS $r) {
		//$db->print_r($r);
		disp_vk_group($r);
	}
}
if(isset($_GET['sent'])) {
	$res=$db->query("SELECT gid,COUNT(uid) AS cnt FROM vklist2
					WHERE cid='$customer_id' AND tm>0 AND del=0
					GROUP BY gid HAVING COUNT(gid) <$cnt_limit
					ORDER BY tm DESC ");
	print "<div class='alert alert-success' >Отправленные - <span class='badge' >".$db->num_rows($res)."</span> (выводятся последние 50)</div>";
	$n=0;
	while($r=$db->fetch_assoc($res)) {
		$gids.="{$r['gid']},";
	}
	$res=vk_grpinfo($gids,$vk->token);
	foreach($res['response'] AS $r) {
		//$db->print_r($r);
		disp_vk_group($r);
		if($n++==50)
			break;
	}
}
if(isset($_GET['disp_contacts'])) {
	$gid=intval($_GET['gid']);
	$res=vk_grpinfo($gid,$vk->token);
	disp_vk_group($res['response'][0],true);
}
if(isset($_GET['rand'])) {
	if(!isset($_GET['from'])) {
		$max=$num_recs;
		$rnd_from=rand(0,$max);
	} else {
		$rnd_from=intval($_GET['from']);
	}
	if(!isset($filter_grp_id)) {
		$gids="";
		print "<div class='_' >Случайная выборка : <span class='badge' >$rnd_from ($max)</span></div>";
		$res=$db->query("SELECT gid,COUNT(uid) AS cnt FROM vklist2
						WHERE $where
						GROUP BY gid HAVING COUNT(gid) <$cnt_limit LIMIT $rnd_from,20");
		while($r=$db->fetch_assoc($res)) {
			$gids.="{$r['gid']},";
		}
	} else	
		$gids="$filter_grp_id";
	$res=vk_grpinfo($gids,$vk->token);
	foreach($res['response'] AS $r) {
		//$db->print_r($r);
		disp_vk_group($r);
	}
}
function vk_grpinfo($gids,$token) {
	$url = 'https://api.vk.com/method/groups.getById';
	$params=array('v'=>5.84,'access_token'=>$token, 'group_ids'=>$gids,'fields'=>"city,country,description,status,members_count,contacts");
	$res=json_decode(file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params))))),true);
	return $res;
	//print_r($res);
}
function disp_vk_group($r, $disp_contacts=false) {
	global $db,$bs,$vk,$customer_id,$rnd_from;
	$city=(isset($r['city']))?"<div class='label label-info' >{$r['city']['title']}</div>":"";
	$gid=intval($r['id']);
	if(!$gid)
		return;
	$r1=$db->fetch_assoc($db->query("SELECT * FROM vklist2 WHERE gid='$gid'"));
	$checked_remove=($r1['remove']==1)?"CHECKED":"";
	$checked_fav=($r1['fav']==1)?"CHECKED":"";
	?>
	<div class="media well">
	  <div class="media-left">
		<div class="checkbox">
			<span class='badge' ><label><input type='checkbox' name='chk_fav' id='<?=$gid?>' <?=$checked_fav?> style='display:none;'><span class="glyphicon glyphicon-star"></span></label></span>
			<span class='badge' ><label><input type='checkbox' name='chk_remove' id='<?=$gid?>' <?=$checked_remove?> style='display:none;'><span class="glyphicon glyphicon-trash"></span></label></span>
		</div>
		<img src="<?=$r['photo_200']?>" class="media-object" style="width:200px">
	  </div>
	  <div class="media-body">
		<h4 class="media-heading"><?
			print "<a href='https://vk.com/public{$r['id']}' class='' target='_blank'>".htmlspecialchars($r['name'])."</a>";
			?></h4>
		<div><span  class='badge' ><?=$r['members_count']?></span></div>
		<?=$city?>
		<div><?print "<div class='' ><i>{$r['status']}</i></div>";?></div>
		<div><a  data-toggle="collapse" data-target="#descr_<?=$r['id']?>">Описание группы</a></div>
		<div id='descr_<?=$r['id']?>' class='collapse' ><?=nl2br(htmlspecialchars($r['description']))?></div>
		<? if(!$disp_contacts) print $bs->button_href($text="Контакты", $href="?disp_contacts=yes&gid=$gid", $style="primary");?>
		<?
			if($disp_contacts) {
				if(isset($r['contacts'])) {
					print "<h2>Контакты группы</h2>";
					//$db->print_r($r['contacts']);
					$contacts=array();
					foreach($r['contacts'] AS $c) {
						if(isset($c['user_id'])) {
							$u=$vk->vk_get_userinfo($c['user_id']);
							$contacts[]=$c['user_id'];
							if(isset($c['desc'])) {
								print "<div class='bold' >".nl2br(htmlspecialchars($c['desc']))."</div>";
							}
							disp_vk_user($u,$gid);
						}
					}
				}
				print "<h2>Место работы - эта группа</h2>";
				// AND city_id='$filter_city_id' AND country_id='$filter_country_id'
				$res1=$db->query("SELECT * FROM vklist2
					WHERE del=0 AND gid='$gid'
					LIMIT 10",0);
				while($r1=$db->fetch_assoc($res1)) {
					//~ if(in_array($r1['uid'],$contacts))
						//~ continue;
					$u=$vk->vk_get_userinfo($r1['uid']);
					disp_vk_user($u,$gid);
					//print "{$r1['uid']} <br>";
				}
			}
		?>
		<div><?//print "Удалить группу из базы <a href='?do_del_grp=yes&gid=$gid&cid=$customer_id&rand=yes&from=$rnd_from' class='' target=''><span class='glyphicon glyphicon-trash'></span></a>";?></div>
		<div><a href='#top' class='' target=''><button class="btn btn-default" ><i class="glyphicon glyphicon-arrow-up"></i></button></a></div>
	  </div>
	</div>
	<hr>
	<?
	//$db->print_r($r);
}

function disp_vk_user($r,$gid=false) {
	global $db,$customer_id,$rnd_from;
	if($r['can_write_private_message']!=1 || $r['blacklisted']==1 || !$r['id']) {
		$acc_closed="<span class='badge' >ЛИЧКА ЗАКРЫТА !</span>";
	} else
		$acc_closed="";
	$uid=intval($r['id']);
	$city_id=(isset($r['city']))?intval($r['city']['id']):0;
	$country_id=(isset($r['country']))?$r['country']['id']:1;
	$city=(isset($r['city']))?$r['city']['title']:"";
	$country=(isset($r['country']))?$r['country']['title']:"";
	$tm=$db->dlookup("tm","vklist2","uid='$uid'");
	if($tm>0) {
		$cid=$db->dlookup("cid","vklist2","uid='$uid'");
		$msg_sent="".date("d.m.Y H:i",$tm)." БЫЛО ОТПРАВЛЕНО СООБЩЕНИЕ (cid=$cid)";
	} else $msg_sent="";
	?>
	<div class="media">
	  <div class="media-left">
		<img src="<?=$r['photo_100']?>" class="media-object" style="width:100px">
	  </div>
	  <div class="media-body">
		<h4 class="media-heading"><?
			print "<a href='https://vk.com/id{$r['id']}' class='' target='_blank'>".htmlspecialchars($r['first_name'])." ".htmlspecialchars($r['last_name'])."</a>";
			print " <a href='javascript:wopen(\"msg.php?get_from_vklist=yes&uid={$r['id']}&cid=$customer_id\")' class='' target=''><span class='glyphicon glyphicon-send'></a>";
			print " <span class='label label-dander' >$msg_sent</span> $acc_closed";
			?></h4>
		<div><?print "<div class='' ><span class='label label-info' >$city</span> <span class='label label-info' >$country</span></div>";?></div>
		<div><?print "<div class='' ><i>{$r['status']}</i></div>";?></div>
		<div class='bold' ><?print "<a href='https://vk.com/public{$r['occupation']['id']}' class='orange_' target='_blank'>{$r['occupation']['name']}</a>";?></div>
		<div><?print "<i><a href='{$r['site']}' class='' target='_blank'>{$r['site']}</a></i>";?></div>
		<div><?print "<a href='?do_del=yes&uid={$r['id']}&cid=$customer_id&rand=yes&from=$rnd_from' class='' target=''><span class='glyphicon glyphicon-trash'></span></a>";?></div>
	  </div>
	</div>
	<?
	//print "HERE_ $uid $city_id $country_id";
	if($city_id && $uid && $gid) {
		if(!$db->dlookup("id","vklist2","uid=$uid")) {
			$db->query("INSERT INTO vklist2 SET gid='$gid',uid='$uid',city_id='$city_id',country_id='$country_id'");
			print "<div>$uid added to vklist2</div>";
		}
	}
	usleep(100000);
}

?>
	
	<script type="text/javascript">

	$("input[name='chk_fav']").show();
	$("input[name='chk_remove']").show();

	$("input[name='chk_remove']").change(function() {
		var gid=$(this).attr('id');
		if(this.checked)
			var fl=1;  else var fl=0;
		var url='ajax=yes&remove=yes&gid='+gid+'&cid='+<?=$customer_id?>+'&fl='+fl;
		console.log(url);
		//setup the ajax call
		$.ajax({
			type:'GET',
			url:'vklist2.php',
			data:url
		});
	});
	$("input[name='chk_fav']").change(function() {
		var gid=$(this).attr('id');
		if(this.checked)
			var fl=1;  else var fl=0;
		var url='ajax=yes&fav=yes&gid='+gid+'&cid='+<?=$customer_id?>+'&fl='+fl;
		console.log(url);
		//setup the ajax call
		$.ajax({
			type:'GET',
			url:'vklist2.php',
			data:url
		});
	});
	</script>

<?

    //~ [id] => 10149280
    //~ [first_name] => Диана
    //~ [last_name] => Комарова
    //~ [sex] => 1
    //~ [bdate] => 2.6
    //~ [city] => Array
        //~ (
            //~ [id] => 2
            //~ [title] => Санкт-Петербург
        //~ )

    //~ [country] => Array
        //~ (
            //~ [id] => 1
            //~ [title] => Россия
        //~ )

    //~ [photo_50] => https://pp.userapi.com/c844417/v844417789/e48dc/ssJeXLioxJc.jpg?ava=1
    //~ [photo_100] => https://pp.userapi.com/c844417/v844417789/e48db/dOHUzhGTxY0.jpg?ava=1
    //~ [photo_200] => https://pp.userapi.com/c844417/v844417789/e48da/c8Wa4xfZIcc.jpg?ava=1
    //~ [has_photo] => 1
    //~ [is_friend] => 0
    //~ [can_write_private_message] => 1
    //~ [can_send_friend_request] => 1
    //~ [site] => https://vk.com/id153194743
    //~ [status] => 🤱🏻💕
    //~ [blacklisted] => 0
    //~ [occupation] => Array
        //~ (
            //~ [type] => work
            //~ [id] => 75716222
            //~ [name] => Фитнес с Дианой Комаровой (Кировск,Спб)
        //~ )

?>
