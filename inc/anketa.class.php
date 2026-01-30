<?
//session_start ();
//session_register ("serialized_data");
define('INC_PATH',"/var/www/vlav/data/www/wwl/inc");
include(INC_PATH."/func.inc.php"); //

class point {
	var $label;
	var $name;
	var $quantity;
	var $qwidth=0; //px width of text quantity field
	var $comment;
	var $next_question;
	var $weight=0;
	var $type="radio";
	var $checked=false;
	var $source=-1;
	var $razdel=-1;
	var $raw=-1;
	var $capacity=-1;
	function __construct() {}
}
class question {
	var $num=false;
	var $n=0;
	var $mess="";
	var $hdr="";
	var $points=array();
	var $maintype="radio"; //or checkbox
	var $chkboxes=array();
	var $comment_hdr="";
	var $comment=false;
	var $result=false; //or $point->name
	var $last=false;
	var $next=false;
	var $prev;
	var $displayed=false;

	function add_mess($mess) {
		$p=new point;
		$p->type="mess";
		$p->label=$mess;
		$p->comment=false;
		$this->points[]=$p;
		return $p;
	}
	function add_point($label, $comment_quantity,$next_question,$weight) {
		$p=new point;
		$p->type=$this->maintype;
		$p->name="p-".$this->num."-".($this->n++);
		//print $p->name."<br>";
		//$p->name=rand(0,1000000);
		$p->label=$label;
		if(empty($label)) {
			//$p->type="hidden";
		}
		$p->quantity="";
		$p->next_question=$next_question;
		$p->weight=$weight;
		if($comment_quantity=="")
			$p->comment=false; else 	$p->comment=$comment_quantity;
		$this->points[]=$p;
		return $p;
	}
	function add_qcomment($hdr) {
		$this->comment_hdr=$hdr;
		$this->comment="";
	}
}
class udata {
	var $fld; //text 8 char label
	var $name;
	var $value="";
	var $style="";
	var $maxlen=0;
	var $fieldtype="text";
	var $type=false;
	var $required=false;
}
class anketa {
	var $userdata=array();
	var $questions=array();
	var $prev_question;
	var $error=false;
	var $udata_error=false;
	var $udata_email="";
	var $fl1=false; //not use
	var $razdel=-1;
	var $source=-1;
	var $raw=-1;
	var $capacity=-1;
	var $variant=0;
	var $weight=0;
	var $code="";
	function __construct() {
		$this->init();
	}
	function init() {
		print "<h1>INIT</h1>";
		$this->add_userdata("Firm",true,0,"firm");
		$this->add_userdata("Phone.",false,100,"tel"); //100px width
		$this->add_userdata("Email",false,0,"email");

		$q1=new question;
		$q1->add_point("test11","","comment11",2,1);
		$q1->add_point("test12","100","",2,5);
		$q1->add_qcomment("Q1");
		$this->add_question("Question 1",1,$q1);

		$q2=new question;
		$q2->add_point("->>1","","comment21",1,0);
		$q2->add_point("->>3","200","comment22",3,10);
		$this->add_question("Question 2",2,$q2);

		$q3=new question;
		$q3->add_qcomment("header-3");
		$this->add_question("Question 3",3,$q3);
		$this->disp_userdata();
	}
	function add_question($hdr,$num) {
		$q=new question;
		$q->num=$num; $q->hdr=$hdr;
		$this->questions[]=$q;
		return $q;
	}
	function add_userdata($name,$required, $s,$fld,$val) {
		$u=new udata;
		$u->name=$name; $u->required=$required; $u->style=$s; $u->fld=$fld; $u->value=$val;
		$this->userdata[]=$u;
		return $u;
	}
	function add_userdata_hiddenfield($fld,$val) {
		$u=new udata;
		$u->fieldtype="hidden"; $u->fld=$fld; $u->value=$val;
		$this->userdata[]=$u;
		return $u;
	}
	function disp_controls($type) {
		switch($type) {
			case 1: return "disp_controls";
		}
	}
	function disp_userdata() {
		$n=0;
		print "<FORM method='POST' action='#TOP_ANKETA'>";
		foreach($this->userdata AS $u) {
			if($u->style!="") {
				$s=" style='".$u->style."' ";
			} else { $s=""; }
			if($u->fieldtype=="text")
				print "<p class='userdata'>".$u->name."</p><input type='text' class='userdata' $s name='u_$n' value='".$u->value."'>";
			elseif($u->fieldtype=="textarea")
				print "<p class='userdata'>".$u->name."</p><textarea class='userdata' $s name='u_$n'>".$u->value."</textarea>";
			elseif($u->fieldtype=="hidden")
				print "<input type='hidden' name='u_$n' value='".$u->value."'>";
			else
				print "<p class='red'>error: userdata->type</p>";
			$n++;
		}
		print "<br><input type='submit' class='submit' name='send_userdata' value='".$this->disp_controls(1)."'>
		</form>";
	}
	function validate_userfield($key,$val) {
		if($key=="email") {
			$regex = '^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]{2,})+$';
			if (eregi($regex, trim($val)))
				return $this->udata_email=trim($val);
			else 	$this->udata_error=2;
		}
		return $val;
	}
	function notify_user() {
	}
	function set_userdata($arr) {
		$n=0; $this->udata_error=false;
		//if($this->udata_error) 	print "ERR"; else print "OK";
		foreach($this->userdata AS $u) {
			$u->value=$this->validate_userfield($u->fld, $arr['u_'.$n]);
			if($u->required && trim($u->value)=="")
				$this->udata_error=1;
			$n++;
		}
	}
	function get_point_by_name($name,$q) {
		if(!$q)	return false;
		if(trim($name)=="") return false;
		foreach($q->points AS $p) {
			if($p->name==$name) {
				return $p;
			}
		}
		$num=$q->num;
		return false;
		//die("<p class='red'>get_point_by_name: question #<b>$num</b>, point #<b>$name</b> not found!</p>");
	}
	function num_points($num) {
		$q=$this->get_question_by_num($num);
		return sizeof($q->points);
	}
	function get_question_by_num($num) {
		for($i=0; $i<sizeof($this->questions); $i++) {
			if($this->questions[$i]->num==$num) {
				return $this->questions[$i];
			}
		}
		return false;
		//die("<p class='red'>disp_question: question #<b>$num</b> not found!</p>");
	}
	function get_val_by_userfield($fld) {
		foreach($this->userdata AS $u) {
			if($u->fld==$fld)
				return $u->value;
		}
		return false;
	}
	function disp_header() {
		print "<h1>disp_header</h1>";
	}
	function disp_nav($num) {
		$q=$this->get_question_by_num($num);
		if(!$q->last)
			$this->disp_header();
	}
	function before_question($num) {
	}
	function disp_question($num) {
		$this->before_question($num);
		$q=$this->get_question_by_num($num);
		$q->displayed=true;
		if($q) {
			//print "<h3>found num=$num result=".$q->result."</h3>";
			$this->disp_nav($num);
			//print "<h2> num=$num prev=".$q->prev." prev_question=".$this->prev_question."</h2>";
			print "<h2>".$q->hdr."</h2>";
			print "<FORM method='POST' action='#TOP_ANKETA'>";
			print $q->mess;
			foreach($q->points AS $p) {
				if($p->next_question=="label") {
					print "<p class='header2'>".$p->label."</p>"; continue;
				}
				$name=$p->name;
				if($name==$q->result) {
					$sel="checked";
					if(!$this->get_next_question($num,$name))
						$next="";
				} else $sel="";

				if($p->type=='radio') {
					//print "HERE_".sizeof($q->points);
					$typ=(sizeof($q->points)==1)?"hidden":"radio";
					
					print "<div class='point_radio form-check mb-2'>
							<label class='form-check-label'>
							<input class='form-check-input' type='$typ' name='name' value='$name' $sel id='id_$name'>
							".$p->label."
							</label>
							";
				} elseif($p->type=='checkbox') {
					if($p->checked) $checked="checked"; else $checked="";
					print "<div class='point_checkbox form-check mb-2'>
							<label class='form-check-label'>
							<input  class='form-check-input' type='checkbox' name='chk_".$p->name."' value='$name' $checked>
							".$p->label."
							</label>
							";
				} elseif($p->type=='mess') {
					print $p->label;
				} elseif($p->type=='hidden') {
					print "<input type='hidden' name='chk_".$p->name."' value='$name'>";
				} else {
					print "error: unknown type ".$p->label." &nbsp;";
				}
				if($p->comment) {
					if ($p->next_question=="goto" || $p->next_question=="goto_blank") {
						print "<input type='hidden' name='".$p->name."_q' value='".$p->comment."'>";
					} else {
						$qwidth=($p->qwidth)?"width:$p->qwidth"."px":"";
						print "<label for='id_input_$name'>".$p->comment."</label>";
						print "<input style='$qwidth' class='quantity form-control' id='id_input_$name' type='text' name='".$name."_q' value='".$p->quantity."' onchange='document.getElementById(\"id_$name\").checked=true;'>";
					}
				} //else print "<br>";
				if($p->type=='radio' || $p->type=='checkbox')
					 print "</div>";
			}
			if($q->comment !== false)
				print "<div class='form-group'>
						<label for='comment_$num'>".$q->comment_hdr."</label>
						<textarea class='form-control' name='comment_$num' id='comment_$num'>".$q->comment."</textarea>
						</div>";
			$ready=$this->button_ready();
			$tofirst=$this->button_tofirst();
			if($this->prev_question && $num>1)
				$prev="<input class='btn btn-secondary' type='submit' name='send_prev' value='Назад'>"; else $prev="";
			$prev="";
			$next="<br><button class='btn btn-primary btn-lg' type='submit' name='send_next'> ДАЛЕЕ </button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$tofirst&nbsp;$prev<br>";
			print "<input type='hidden' name='num' value='$num'>
			$next
			$ready
			</form><hr>";
		} else {
			$this->finish();
		}
	}
	function button_tofirst() {
		return "";
		return "<input type='submit' name='send_first' value='Â íà÷àëî'>";
	}
	function button_ready() {
		return "";
		return "<input type='submit' name='send_last' value='Ãîòîâî'>";
	}
	function get_next_question($num,$name) {
		$q=$this->get_question_by_num($num);
		if($q->next===false) {
			$p=$this->get_point_by_name($name,$q);
			if($p)
				return $p->next_question; else return -1;
		} else
			return $q->next;
	}
	function set_quantity($num,$name) {
				//print "<p>here2134=".$_POST[$name.'_q']."</p>";exit;
		$p=$this->get_point_by_name($name,$this->get_question_by_num($num));
		if(@$_POST[$name.'_q']) {
			$p->quantity=$_POST[$name.'_q'];
			//print $p->quantity; exit;
		} else
			$p->quantity="";
	}
	function set_result($num,$name) {
		$q=$this->get_question_by_num($num);
		$q->result=$name;
		//print "N=$num R=$name";
	}
	function disp_finish($res) {
		print nl2br($res);
	}
	var $city_tmp="";
	function prepare_fld($key,$val) {
		return mysql_real_escape_string(substr($val,0,2048));
	}
	function connect() {
		include(INC_PATH."/connect.inc.php");
	}
	function connect_pini() {
		$conn=mysql_connect ("localhost", "vlav_baza", "^guinesS");
		mysql_select_db("pini");
	}
	function print_result($res) {
		print_r($res);
	}
	function save_n_notify($contacts, $userdata, $res_anketa) {
		require_once '/var/www/vlav/data/www/wwl/inc/baza_connector.inc.php';
		$this->print_result($res_anketa);
		$u=new baza_connector;
		//$u->disp_queries=true;
		$u->firm=$this->get_val_by_userfield("firm");
		$u->country=$this->get_val_by_userfield("country");
		$u->city=$this->get_val_by_userfield("city");
		$u->addr=$this->get_val_by_userfield("city")." / ".$this->get_val_by_userfield("addr");
		$u->www=$this->get_val_by_userfield("www");
		$u->sirname=$this->get_val_by_userfield("sirname");
		$u->name=$this->get_val_by_userfield("name");
		$u->app=$this->get_val_by_userfield("app");
		$u->tel=$this->get_val_by_userfield("tel");
		$u->mob=$this->get_val_by_userfield("mob");
		$u->email=$this->get_val_by_userfield("email");
		$u->icq=$this->get_val_by_userfield("icq");
		$u->skype=$this->get_val_by_userfield("skype");

		$u->birthday=trim("");
		$u->person_comm=trim("");
		$u->razdel=$this->razdel;
		$u->source=$this->source;
		$u->rawid=$this->raw;
		$u->capacity=$this->capacity;
		$u->comment=$res_anketa;

		$u->weight=$this->weight;
		$u->ip=trim($_SESSION['ip']);
		$u->referer=trim($_SESSION['referer']);
		$u->pages=trim($_SESSION['pages']);
		$u->type=100; //íîâàÿ ðåãèñòðàöèÿ
		$u->type1=101; //ïîâòîðíàÿ ðåãèñòðàöèÿ
		$u->save();
		if($u->result !=-1) {
			$this->notify_user();
		} else
			print "<p class='red'>Çàïðîñ óæå îòïðàâëåí. ".($u->timepast)." (".$u->timeout.") ñåê íàçàä</p>";
	}
	function get_weight($weight) {
		//print "HERE_".$this->weight+$weight."<br>";
		return $this->weight+$weight;
	}
	function last_question() {
		$this->disp_userdata();
	}
	function first_question() {
		disp_contacts();
		$this->disp_question(1);
	}
	function disp_err_mess($id) {
		switch($id) {
			case 1: return "Ïðîñüáà çàïîëíèòü âñå äàííûå, ïîìå÷åííûå (*)!"; break;
			case 2: return "Îøèáêà â àäðåñå ýëåêòðîííîé ïî÷òû!"; break;
			case 3: return "Îøèáêà ïðè çàïîëíåíèè êîíòàêòíûõ äàííûõ!"; break;
		}
	}
	function run() {
		if(@$_GET['init'])
			$_POST['send_next']="yes";
		if(array_key_exists("send_userdata",$_POST)) {
			$this->set_userdata($_POST);
			/////////////////////
			$res="user data ------------\n";
			foreach($this->userdata AS $u) {
				$val=trim($u->value); if($val=="" || !$val) $val="";
				$res .= str_replace("(<SPAN style='color:red;'>*</SPAN>)", "", $u->name)." <b>$val</b>\n";
			}
			if($this->udata_error) {
				$res.="udata_error: ".$this->udata_error."\n";
			}
			/////////////////////////
			if(!$this->udata_error) {
				$this->after_disp_userdata();
			} else {
				if($this->udata_error==1)
					print "<p id='ERR' class='red'>".$this->disp_err_mess(1)."</p>";
				elseif($this->udata_error==2)
					print "<p id='ERR' class='red'>".$this->disp_err_mess(2)."</p>";
				else
					print "<p id='ERR' class='red'>".$this->disp_err_mess(3)."</p>";
				$this->disp_userdata();
			}
		}
		if(array_key_exists("send_first",$_POST)) {
			$this->first_question();
		}
		$no_one_checked=false;
		//$no_quantity=false;
		if(array_key_exists("send_next",$_POST) || array_key_exists("send_last",$_POST) ) {
			$num=$_POST['num'];
			//print "HERE_".$num;
			$q=$this->get_question_by_num($num);
			if($this->prev_question)
				$q->prev=$this->prev_question;
			$this->prev_question=$num;
			if(@$_POST["comment_".$num])
				$q->comment=$_POST["comment_".$num];
			//print "<h1>send num=$num</h1>";
			if($q->last) {
				//print "HERE $num<br>".$q->comment; exit;
				//print_r($_POST); exit;
			}
			if(array_key_exists("name",$_POST)) {
				$name=$_POST['name'];
				$next_question=$this->get_next_question($num,$name);
				if($next_question=="goto") {
					print "<script>location=\"".$_POST[$name.'_q']."\"</script>
					<a href='".$_POST[$name.'_q']."'>goto</a>";
					exit;
				} elseif($next_question=="goto_blank") {
					print "<script>window.open(\"".$_POST[$name.'_q']."\");</script>
					<a href='".$_POST[$name.'_q']."'>goto</a>";
					exit;
				} elseif($next_question) {
					$this->set_result($num,$name);
					$this->set_quantity($num,$name);
					$p=$this->get_point_by_name($name,$q);
					if($p->comment && trim($p->quantity)=="") {
						//print "<p class='red'>err</p>";
						$next_question=$num;
					}
				} else {
					print "<p>error: 23</p>"; exit;
				}
			} else {
				$no_one_checked=true;
				foreach($q->points AS $p)
						$p->checked=false;
				foreach($_POST as $key=>$val) {
					if(preg_match("/chk_/",$key)) {
						$p=$this->get_point_by_name($val,$q);
						$p->checked=true;
						$no_one_checked=false;
					}
				}
				foreach($_POST as $key=>$val) {
					if(preg_match("/_q$/",$key)) {
						if($val!="") {
							$p=$this->get_point_by_name(preg_replace("/_q/","",$key),$q);
							if($p->type=="checkbox") {
								$p->checked=true;
								$p->quantity=$val;
								$no_one_checked=false;
								//print "$key $val <br>";
							}
						}
					}
				}
				if($this->num_points($num)>1 && !@$_POST['send_last'] && $no_one_checked) {
					print "<p class='alert alert-danger'>Выберите значение!</p>";
					$next_question=$num;
				} else {
					$next_question=$q->next;
					if(!$next_question) {
						foreach($q->points AS $p) {
							if($p->checked)
								$next_question=$p->next_question;
						}
						if(!$next_question) {
							$num++;
							while(!$q && $num<1000) $num++;
							$next_question=$num;
							if($num==1000)
								$this->last_question();
						}
					}
				}
			}
			if($this->num_points($q->num) != 0) {
				$res="";
				$name=$q->result;
				$p=$this->get_point_by_name($name,$q);
				if($p) {
					if($p->comment) {
						$quantity=$p->quantity; $comment=$p->comment;
						if($quantity=="")	$quantity="123";
					} else {
						$quantity=""; $comment="";
					}
					$label=$p->label;
					$this->weight=$this->get_weight($p->weight);
					$res.= $q->hdr." <b>$label</b> $comment <b>$quantity</b>\n";
				} else {
					$res.="Here is checkbox - not processing";
				}
				if($q->comment) {
					$res.=" | ".trim($q->comment);
				}
			}
			//if((array_key_exists("send_last",$_POST) || $q->last) && $no_one_checked !== false ) {
			if((array_key_exists("send_last",$_POST) || $q->last) ) {
			//	print "<h1>LAST $q->last</h1>";
				$this->last_question();
			} else {
				//print "<h1>NEXT $q->last</h1>";
				$this->disp_question($next_question);
			}
		}
		if(array_key_exists("send_prev",$_POST)) {
			$q=$this->get_question_by_num($_POST['num']);
			if($q->prev)
				$this->disp_question($q->prev); else $this->disp_question($this->prev_question);
		}
	}
	function before_finish() {
		//$q=$this->get_question_by_num(1);
		//print "HERE=".$q->result; exit;
		//$p=$this->get_point_by_name($q->result,$q);
		//$this->razdel=$p->name;
	}
	function finish() {
		$this->weight=0;
		$res_add=$this->before_finish();
		//$res="weight=$this->weight \n";
		$userdata="";
		$contacts=array();
		foreach($this->userdata AS $u) {
			if($u->fld=="email")
				if(trim($u->value)=="")
					$this->error=true;
			$val=trim($u->value); if($val=="" || !$val) $val="";
			$userdata .= $u->name." <b>$val</b>\n";
			if(trim($u->fld)!="") {
				$contacts[$u->fld]=$this->prepare_fld($u->fld,$val);
			}
		}
		//print sizeof($this->questions);
		$answ=array(); $answ_n=0;
		foreach($this->questions AS $q) {
			//print "HERE_".$q->num." ".$q->result."<br>";
			if(!$q->displayed) continue;
			if($q->hdr=="") continue;
			$name=$q->result;
			$hdr=$q->hdr;
			if($this->num_points($q->num) != 0) {
				$p=$this->get_point_by_name($name,$q);
				if($p) {
					if($p->comment) {
						$quantity=$p->quantity; $comment=$p->comment;
						if($quantity=="")	$quantity="íå óêàçàíî";
					} else {
						$quantity=""; $comment="";
					}
					$label=$p->label;
					$this->weight=$this->get_weight($p->weight);
					$answ[]=["num"=>$q->num,"hdr"=>$hdr,"point"=>$p->name, "label"=>$label, "weight"=>$p->weight,"comm"=>$comment,"quantity"=>$quantity, "comment"=>""];
				//	$res.="num=$q->num weight=$p->weight sum=$this->weight\n";
					//print "HERE_W=".$this->weight."<br>";
					$res.= $q->hdr." <b>$label</b> $comment <b>$quantity</b>\n";
					$this->code.=$p->name."-";
					if($p->razdel!=-1)	$this->razdel=$p->razdel;
					if($p->source!=-1)	$this->source=$p->source;
					if($p->raw!=-1)	$this->raw=$p->raw;
					if($p->capacity!=-1)	$this->capacity=$p->capacity;
				} else {
					if($q->maintype!="checkbox") continue;
					$no_one_checked=true;
					foreach($q->points AS $p) {
						if($p->checked) {
							$no_one_checked=false;
							if($p->comment) {
								$quantity=$p->quantity; $comment=$p->comment;
								if($quantity=="")	$quantity="íå óêàçàíî";
							} else {
								$quantity=""; $comment="";
							}
							$label=$p->label;
							$this->weight=$this->get_weight($p->weight);
				//	$res.="num=$q->num weight=$p->weight sum=$this->weight\n";
							$answ[]=["num"=>$q->num,"hdr"=>$hdr,"point"=>$p->name,"label"=>$label, "weight"=>$p->weight,"comm"=>$comment,"quantity"=>$quantity,"comment"=>""];
							$res.= $q->hdr." <b>$label</b> $comment <b>$quantity</b>\n";
							$this->code.=$p->name."-";
							if($p->razdel!=-1)	$this->razdel=$p->razdel;
							if($p->source!=-1)	$this->source=$p->source;
							if($p->raw!=-1)	$this->raw=$p->raw;
							if($p->capacity!=-1)	$this->capacity=$p->capacity;
						}
					}
					if($no_one_checked)
						$res.=" <b>no_one_checked</b>\n";
				}
			}
			if($q->comment) {
				$comment=trim($q->comment);
				if($comment=="") $comment=""; else $this->weight=$this->get_weight(0);
				if($q->comment_hdr=="")
					$hdr=$q->hdr; else $hdr=$q->comment_hdr;
				$res.= " ".$hdr." <b>".$comment."</b>\n";
				$answ[]=["num"=>$q->num,"hdr"=>$hdr, "point"=>"","label"=>"", "weight"=>0,"comm"=>"","quantity"=>"","comment"=>$comment];
				//print "HERE";
			} else {
			}
			if($q->last) {
				//print "LAST HERE_".$q->num." ".$q->result."<br>";
				break;
			}
		}
		$res.="\n$res_add";
		//print nl2br($res)."<br>".$this->code; exit;
		$this->disp_finish($res);
		return ($answ);
		//print $res; exit;
	}
	function after_disp_userdata() {
		$this->finish();
	}
	function tolowercase($str)
	{
		return mb_strtolower($str);
	}
}

function anketa2_run() {
	print "<DIV  id='TOP_ANKETA' class='anketa2'><br><br>\n";
	if(sizeof($_POST) == 0) {
		//disp_contacts();
		$a=new anketa;
		$a->disp_question(1);
	} else {
		if(@$_POST['init']) {
			$a=new anketa;
			$_SESSION['anketa2_data']=serialize($a);
		}
		if (isset($_SESSION['anketa2_data']))
			$a=unserialize($_SESSION['anketa2_data']);
		if(@$_POST['goto']) {
			$a->disp_question($_POST['goto']);
		}
		$a->run();
	}
	$_SESSION['anketa2_data']=serialize($a);
	print "</DIV>\n";
}

function disp_contacts() {
		print "<h1>disp_contacts</h1>";
}

?>

