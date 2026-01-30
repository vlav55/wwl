<?
include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";
class vkt extends db {
	function __construct($database=false) {
		if($database)
			$this->connect($database);
	}
	function get_database_by_cwd() {
		return $this->get_ctrl_database($this->get_ctrl_id_by_cwd());
	}
	function get_ctrl_id_by_cwd() {
		$cwd=getcwd();
		if(preg_match("/db1/",$cwd))
			return 1;
		if(preg_match("/1000/",$cwd))
			return 1;
		//print "$cwd"; exit;
		//$cwd="https://1-info.ru/vkt/d/498629140";
		if(preg_match("/\/(\d+)(?:\/\d+)?$/i",$cwd,$m)) {
			$dir=intval($m[1]);
			if($dir) {
				return $this->dlookup("id","0ctrl","ctrl_dir='$dir'");
			}
		}
		if(preg_match("/\/(\d+)(?:\/lk)?$/i",$cwd,$m)) {
			$dir=intval($m[1]);
			if($dir) {
				return $this->dlookup("id","0ctrl","ctrl_dir='$dir'");
			}
		}
		return false;
	}
	function get_ctrl_id_by_uid($uid) {
		return $this->dlookup("id","0ctrl","uid='$uid'");
	}
	function get_ctrl_link($ctrl_id,$filter='tasks') {
		$dir=$this->get_ctrl_dir($ctrl_id);
		$d=($dir=='db1')?"db1":"d/$dir";
		return "https://for16.ru/$d/cp.php?view=yes&filter=$filter";
	}
	function get_ctrl_url($ctrl_id) {
		$dir=$this->get_ctrl_dir($ctrl_id);
		$d=($dir=='db1')?"db1":"d/$dir";
		return "https://for16.ru/d/$dir";
	}
	function get_ctrl_path($ctrl_id) {
		$dir=$this->get_ctrl_dir($ctrl_id);
		return "/var/www/vlav/data/www/wwl/d/$dir";
	}
	function get_ctrl_database($ctrl_id) {
		if($ctrl_id==1)
			return 'vkt';
		return 'vkt1_'.$ctrl_id;
	}
	function get_ctrl_id_by_db($db) {
		if($db=='vkt')
			return 1;
		if(preg_match("/vkt1_(\d+)/",$db,$m)) {
			$ctrl_id=intval($m[1]);
			if($this->get_ctrl_database($ctrl_id) == $db)
				return $ctrl_id;
		}
		return false;
	}
	function get_db200($ctrl_dir) {
		return "https://for16.ru/d/$ctrl_dir";
	}

	function create_ctrl_databases($ctrl_id) {
		//$d='vkt1_test';

		$d=$this->get_ctrl_database($ctrl_id);

		$env = file_get_contents("/var/www/vlav/data/.env_r");
		if (!$env) {
			die('Invalid configuration file');
		}
		$r=json_decode($env,true);
		$mysql_user=$r['DB_USER'];
		$mysql_passw=$r['DB_PASSW'];
		//$this->notify_me("$mysql_user, $mysql_passw"); exit;
		$conn=mysqli_connect ("localhost", $mysql_user, $mysql_passw);
		if($conn) {
			try {
					mysqli_query ($conn,"CREATE DATABASE IF NOT EXISTS `$d` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;");
				}  catch (mysqli_sql_exception $e) {
					print "error select db";
					$this->email($emails=array("vlav@mail.ru"), "mysql_error ", "mysql_error\n db=$d\n $qd\n ".$e->getMessage()." (".mysqli_errno($conn).")\n".nl2br(print_r(debug_backtrace (),true))."\n\nGLOBALS\n", $from="noreply@winwinland.ru",$fromname="WWL", $add_globals=true);
				}
			mysqli_select_db ($conn,$d);
			mysqli_query ($conn,"GRANT SELECT , INSERT , UPDATE , DELETE, ALTER ON $d.* TO vlav@localhost");
			mysqli_query ($conn,"set character_set_results='utf8mb4'");
			mysqli_query ($conn,"set collation_connection='utf8mb4_general_ci'");
			mysqli_query($conn,"set character_set_client='utf8mb4'");
		}
	
		$path="/var/www/vlav/data/www/wwl/scripts/structure/";

		$res=$this->parse_sql_file($path.'vkt.sql');
		foreach($res AS $q) {
	//print "$q <hr>";
			mysqli_query ($conn,$q) or die ("query error!");
		}
		$res=$this->parse_sql_file($path.'razdel.sql');
		foreach($res AS $q)
			mysqli_query ($conn,$q) or die ("query error!");
			
		$res=$this->parse_sql_file($path.'sources.sql');
		foreach($res AS $q)
			mysqli_query ($conn,$q) or die ("query error!");
			
		$res=$this->parse_sql_file($path.'users.sql');
		foreach($res AS $q)
			mysqli_query ($conn,$q) or die ("query error!");
			
		$res=$this->parse_sql_file($path.'vklist_acc.sql');
		foreach($res AS $q)
			mysqli_query ($conn,$q) or die ("query error!");

		$res=$this->parse_sql_file($path.'msgs_templates.sql');
		foreach($res AS $q)
			mysqli_query ($conn,$q) or die ("query error!");

		$res=$this->parse_sql_file($path.'tags.sql');
		foreach($res AS $q)
			mysqli_query ($conn,$q) or die ("query error!");

		//~ $this->query("INSERT INTO `tags` (`id`, `tag_name`, `tag_color`, `del`)
						//~ VALUES (1, 'Клиент', '#ffff00', 0)");
			
		mysqli_query ($conn,"GRANT SELECT , INSERT , UPDATE , DELETE, ALTER ON $d . * TO vlav@localhost") or die ("query error!");
		mysqli_query($conn,"ALTER TABLE cards AUTO_INCREMENT = 1000;") or die ("query error 2!");

		//print "Ok";
	}
	function get_ctrl_dir($ctrl_id) {
		if($ctrl_id==1)
			return '1000';
		return crc32($ctrl_id);
	}
	function create_ctrl_company($uid) {
		if(!$ctrl_id=$this->get_ctrl_id_by_uid($uid)) {
			$this->query("INSERT INTO 0ctrl	SET
					uid='$uid',
					tm='".time()."',
					fee_1=0,
					fee_2=0,
					fee_hello=0,
					hold=180,
					keep=0
					");
			$ctrl_id=$this->insert_id();
			$ctrl_dir=$this->get_ctrl_dir($ctrl_id);
			$this->query("UPDATE 0ctrl SET ctrl_dir='$ctrl_dir' WHERE id='$ctrl_id'");
			return $ctrl_id;
		}
		return false;
	}
	function create_ctrl_dir($ctrl_id) {
		$ctrl_dir=$this->get_ctrl_dir($ctrl_id);
		
		$dir="/var/www/vlav/data/www/wwl/d/$ctrl_dir";
		$src_dir="/var/www/vlav/data/www/wwl/d/1000";
		if(!file_exists($dir)) {
			if(!mkdir($dir)) {
				print "error creating of ctrl_dir: $dir";
				exit;
			}
			if(!mkdir($dir.'/lk')) {
				print "error creating of ctrl_dir: $dir".'lk';
				exit;
			}
			if(!mkdir($dir.'/reports')) {
				print "error creating of ctrl_dir: $dir".'lk';
				exit;
			}
			if(!mkdir($dir.'/tg_files')) {
				print "error creating of ctrl_dir: $dir".'tg_files';
				exit;
			}
			if(!mkdir($dir.'/1')) {
				print "error creating of ctrl_dir: $dir".'1';
				exit;
			}
			if(!mkdir($dir.'/2')) {
				print "error creating of ctrl_dir: $dir".'2';
				exit;
			}
		} else {
			print "$dir exists. Exiting. <br> \n";
			exit;
		}
		//print "$dir created OK <br> \n";

		
		$fd=opendir($src_dir);
		while($fname=readdir($fd)) {
			if(is_dir($src_dir.'/'.$fname))
				continue;
			//print "$fname <br>";
			if (!copy($src_dir.'/'.$fname, $dir.'/'.$fname)) {
				echo "failed to copy $fname...\n <br>";
				print $src_dir.'/'.$fname.' -> '.$dir.'/'.$fname."<br>";
			} else {
				//print "$fname copied ok <br> \n";
			}
			
		}
		closedir($fd);
		
		$fd=opendir($src_dir.'/lk');
		while($fname=readdir($fd)) {
			if(is_dir($src_dir.'/lk/'.$fname))
				continue;
			//print "$fname <br>";
			if (!copy($src_dir.'/lk/'.$fname, $dir.'/lk/'.$fname)) {
				echo "failed to copy $fname...\n <br>";
				print $src_dir.'/lk/'.$fname.' -> '.$dir.'/lk/'.$fname."<br>";
			} else {
				//print "$fname copied ok <br> \n";
			}
			
		}
		closedir($fd);

		$fd=opendir($src_dir.'/reports');
		while($fname=readdir($fd)) {
			if(is_dir($src_dir.'/reports/'.$fname))
				continue;
			//print "$fname <br>";
			if (!copy($src_dir.'/reports/'.$fname, $dir.'/reports/'.$fname)) {
				echo "failed to copy $fname...\n <br>";
				print $src_dir.'/reports/'.$fname.' -> '.$dir.'/reports/'.$fname."<br>";
			} else {
				//print "$fname copied ok <br> \n";
			}
			
		}
		closedir($fd);

		$fd=opendir($src_dir.'/1');
		while($fname=readdir($fd)) {
			if(is_dir($src_dir.'/1/'.$fname))
				continue;
			//print "$fname <br>";
			if (!copy($src_dir.'/1/'.$fname, $dir.'/1/'.$fname)) {
				echo "failed to copy $fname...\n <br>";
				print $src_dir.'/1/'.$fname.' -> '.$dir.'/1/'.$fname."<br>";
			} else {
				//print "$fname copied ok <br> \n";
			}
			
		}
		closedir($fd);

		$fd=opendir($src_dir.'/2');
		while($fname=readdir($fd)) {
			if(is_dir($src_dir.'/2/'.$fname))
				continue;
			//print "$fname <br>";
			if (!copy($src_dir.'/2/'.$fname, $dir.'/2/'.$fname)) {
				echo "failed to copy $fname...\n <br>";
				print $src_dir.'/2/'.$fname.' -> '.$dir.'/2/'.$fname."<br>";
			} else {
				//print "$fname copied ok <br> \n";
			}
			
		}
		closedir($fd);



		file_put_contents($dir.'/ctrl.id',$ctrl_id);
		file_put_contents($dir.'/lk/ctrl.id',$ctrl_id);
		//print "File ctrl.id with $ctrl_id created <br>\n";
		
	}
	function parse_sql_file($fname) {
		if(!$txt=file($fname)) {
			print "File $fname is not found <br>\n";
			return false;
		}
		$txt[]="----------------\n";
		$res=[];
		foreach($txt AS $str) {
			if(preg_match("/([\-]{2,})|(^SET)|(\/\*\!)/",$str)) {
				if(!empty(trim($out)))
					$res[]=preg_replace("/COMMIT;/","",$out);
				$out="";
				continue;
			}
			$out.=$str;
		}
		return $res;
	}
	function vkt_create_account($uid,$product_id) { //$product_id=0 not add record in avangard
		if(!$uid)
			return false;
		if(!$ctrl_id=$this->dlookup("id","0ctrl","uid='$uid'") ) {
    echo "<style>
        .cursor-wait {cursor: wait !important;}
        .cursor-auto {cursor: auto !important;}
    </style>";
    
    // Add wait class
    echo "<script>
        document.documentElement.classList.add('cursor-wait');
        document.documentElement.classList.remove('cursor-auto');
    </script>";
    ob_flush();
    flush();

			$r=$this->fetch_assoc($this->query("SELECT * FROM cards WHERE del=0 AND uid='$uid'"));
			$client_name=$this->escape($r['name'].' '.$r['surname']);
			$client_email=$r['email'];
			$client_phone=$r['mob_search'];
			$term=($product_id) ? $this->dlookup("term","product","id='$product_id'") : 0 ;
			$tm_end=time()+$this->dt2($term*24*60*60);
			$sum=0; 
			$order_id=$this->get_next_avangard_orderid();
			$order_number=$order_id;
			$descr=($product_id) ? $this->dlookup("descr","product","id='$product_id'") : "";

			if(!$ctrl_id=$this->get_ctrl_id_by_uid($uid)) {
				$ctrl_id=$this->create_ctrl_company($uid);
				//print "ctrl_id=$ctrl_id <br>";
				$this->create_ctrl_dir($ctrl_id);
				$this->create_ctrl_databases($ctrl_id);
				$ctrl_dir=$this->get_ctrl_dir($ctrl_id);
				$ctrl_db=$this->get_ctrl_database($ctrl_id);
				$this->email($emails=array("vlav@mail.ru"), "WWL new company ctrl_id=$ctrl_id CREATED", "", $from="noreply@winwinland.ru",$fromname="WWL", $add_globals=true);
				$passw=$this->passw_gen($len=10);

				$this->connect($ctrl_db);
				$this->query("UPDATE users SET
					passw='".md5($passw)."',
					real_user_name='".$this->escape($client_name)."',
					email='".$this->escape($client_email)."',
					comm='".$this->escape($passw)."'
					WHERE username='admin'");

				//add 2 weeks code here ...
				
				$this->connect('vkt');
				$this->query("UPDATE 0ctrl SET admin_passw='$passw' WHERE id='$ctrl_id'");

				if($product_id) {
					$this->query("INSERT INTO avangard SET
								tm='".time()."',
								pay_system='vkt_create_account',
								product_id='$product_id',
								order_id='$order_id',
								order_number='".$this->escape($order_number)."',
								order_descr='".$this->escape($descr)."',
								amount='$sum',
								amount1='$sum',
								c_name='".$this->escape($client_name)."',
								phone='".$this->escape($client_phone)."',
								email='".$this->escape($client_email)."',
								vk_uid='$uid',
								res=1,
								tm_end='$tm_end'
								");
				}
			} else {
    echo "<script>
        document.documentElement.classList.remove('cursor-wait');
        document.documentElement.classList.add('cursor-auto');
    </script>";
    ob_flush();
    flush();
				return false;
			}
			$this->notify_me("SUCCESS : vkt_create_account($uid,$product_id) for ctrl_id=$ctrl_id");
			$this->tag_add($uid,29);
    echo "<script>
        document.documentElement.classList.remove('cursor-wait');
        document.documentElement.classList.add('cursor-auto');
    </script>";
    ob_flush();
    flush();
			return $ctrl_id;
		}
    echo "<script>
        document.documentElement.classList.remove('cursor-wait');
        document.documentElement.classList.add('cursor-auto');
    </script>";
    ob_flush();
    flush();
		return $ctrl_id;
	}
	function vkt_delete_account($ctrl_id) {
		$path=$this->get_ctrl_path($ctrl_id);
		rename($path, $path."__");
		$this->query("UPDATE 0ctrl SET del=1 WHERE id='$ctrl_id'",0);
		return $this->dlookup("uid","0ctrl","id='$ctrl_id'");
	}
	function encode_ctrl_id($n) {
		return intval($n * (2000000 / 101)) + 1;
	}
	function decode_ctrl_id($n_encoded) {
		$n=intval(intval($n_encoded) * (101 / 2000000));
		if($this->encode_ctrl_id($n) !== intval($n_encoded))
			return false; else return $n;
	}
	function ctrl_days_end_set($ctrl_id,$days) {
		$tmp=$this->database;
		$this->connect('vkt');
		if(intval($days)) {
			$tm_end=$this->dt2(time()+($days*24*60*60));
			$this->query("UPDATE 0ctrl SET tm_end='$tm_end' WHERE id='$ctrl_id'");
		}
		$this->connect($tmp);
		return false;
	}
}

?>
