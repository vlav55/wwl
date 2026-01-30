<?
$imin=@$i;
if($imin==0)
	$imin="00";
$red="red";
$res="document.f_res.date.value"; //чему присвоить результат
$cmd="document.f_res.time.value='';document.f_res.date.style.color='$red';ins_dttm();\n";
$cmd_time="ins_dttm();";
$date_delimiter="."; //разделитель

?>
<script type='text/javascript'>
var tmonth = new Array();
tmonth[1]="€нварь";
tmonth[2]="февраль";
tmonth[3]="март";
tmonth[4]="апрель";
tmonth[5]="май";
tmonth[6]="июнь";
tmonth[7]="июль";
tmonth[8]="август";
tmonth[9]="сент€брь";
tmonth[10]="окт€брь";
tmonth[11]="но€брь";
tmonth[12]="декабрь";
function checkdate(d, m, y)
{
  var dt = new Date(y, m-1, d);
  return ((y == dt.getFullYear()) && ((m-1) == dt.getMonth()) && (d == dt.getDate()));
}

function lastdate(m,y)
{
	var last=31;
	while (!checkdate(last,m,y))
		last--;
	return (last);
}
function firstday (m,y)
{
	var d=new Date();
	d.setMonth(m-1);
	d.setYear(y);
	d.setDate(1);
	return (d.getDay());
}
function next_month (d)
{
	if (d==1)
		m=eval(document.bar.month.value)-1;
	else
		m=eval(document.bar.month.value)+1;
	if (m>12)
	{
		m=1; document.bar.year.value ++;
	}
	if (m<1)
	{
		m=12; document.bar.year.value --;
	}
	fill_form (m,document.bar.year.value);
}
function next_year (d)
{
	if (d==1)
		y=eval(document.bar.year.value)-1;
	else
		y=eval(document.bar.year.value)+1;
	fill_form (document.bar.month.value, y);
}
function today()
{
	var d = new Date();
	fill_form (d.getMonth()+1, d.getFullYear());
}
function disp_form ()
{
//var style="";
//var style_cell=" class='cell' ";
//var style_table="";
document.writeln ("<table align='center' class='cal'><tr><td class='cal'>");
document.writeln ("<table border='0' width='150' class='monthyear'><tr align='center'>");
document.writeln ("<form name='bar'>");
document.writeln ("<td width='10' class='monthyear'><a href='javascript:next_month(1)'><img src='images/cal/back.gif' border=0 alt='-год'><\/a><\/td>");
document.writeln ("<td width='40' class='monthyear'><input type='text' name='tmonth' value=''  class='month'><\/td>");
document.writeln ("<td width='10' class='monthyear'><a href='javascript:next_month(2)'><img src='images/cal/fwd.gif' border=0 alt='-мес'><\/a><\/td>");
document.writeln ("<td width='10' class='monthyear'><a href='javascript:next_year(1)'><img src='images/cal/back.gif' border=0 alt='+мес'><\/a><\/td>");
document.writeln ("<td width='20' class='monthyear'><input type='text' name='year' value=''  class='year'><\/td>");
document.writeln ("<td width='10' class='monthyear'><a href='javascript:next_year(2)'><img src='images/cal/fwd.gif' border=0 alt='+год'><\/a><\/td>");
document.writeln ("<input type='hidden' name='month' value=''>");
document.writeln ("<\/form>");
document.writeln ("<\/tr>");
document.writeln ("<tr><td colspan='6' align='center' class='today'><a href='javascript:goto_today()'>к сегодн€<\/a><\/td><\/tr>");
document.writeln ("<\/table><\/td><\/tr><tr><td align='center' class='cal2'>");

document.writeln ("<table border='1'  class='cells'>");
document.writeln ("<form name='f'>");
var w=10;
	document.writeln ( "<tr  class='weekdays'>");
		document.writeln ("<td class='weekdays'>пн<\/td>");
		document.writeln ("<td class='weekdays'>вт<\/td>");
		document.writeln ("<td class='weekdays'>ср<\/td>");
		document.writeln ("<td class='weekdays'>чт<\/td>");
		document.writeln ("<td class='weekdays'>пт<\/td>");
		document.writeln ("<td class='weekdays'>сб<\/td>");
		document.writeln ("<td class='weekdays'>вс<\/td>");
	document.writeln ("<\/tr>");
var num=1;
for (r=0; r<5; r++)
{
	document.writeln ( "<tr>");
	for (i=0; i<7; i++)
	{
	document.writeln ("<td class='cell'><input type='text' name='cell"+num+"' value='' onclick='javascript:choiced("+num+")' class='cell'><\/td>");
		num++;
	}
	document.writeln ("<\/tr>");
}
document.writeln ("<\/form>");
document.writeln ("<\/table>");
document.writeln ("<form name='res'>");
document.writeln ("<input type='hidden' name='cdate' value=''>");
document.writeln ("<input type='hidden' name='oldnum' value='' size='10'>");
document.writeln ("<\/form>");
document.writeln ("<\/td><\/tr><\/table>");
}
function goto_today() {
	var cur_d = new Date();
	var m=cur_d.getMonth()+1;
	var y=cur_d.getFullYear();
	fill_form (m,y);
}
function fill_form (m,y)
{
	var cur_d = new Date();
	var d_=cur_d.getDate();
	var m_=cur_d.getMonth()+1;
	var y_=cur_d.getFullYear();
	if(y==1970) {
		m=m_; y=y_;
	}
	var num=1;
	var first=firstday(m,y);
	var last=lastdate(m,y);

	document.bar.tmonth.value=tmonth[m];
	document.bar.month.value=m;
	document.bar.year.value=y;
	for (r=0; r<5; r++)
	{
		for (i=0; i<7; i++)
		{
			document.f.elements[num-1].value="";
			if ( (num>=first) && (num<last+first) )
			{
				d=num-first+1;
				document.f.elements[num-1].value=d;
                if ((d==d_)&&(m==m_)&&(y==y_)) {
					document.f.elements[num-1].className='cell_today';
				} else {
					if((d==cur_day)&&(m==cur_month)&&(y==cur_year)) {
						//document.f.elements[num-1].style.backgroundColor='red';
						document.f.elements[num-1].className='cell_dmy';
					} else {
						document.f.elements[num-1].className='cell';
					}
				}
			}
			num++;
		}
	}
}
function choiced(num) {
	var d,m,y;
	if (num !=0) {
		d=document.f.elements[num-1].value;
		if(d<10)
			d="0"+d;
		m=document.bar.month.value;
		if(m<10)
			m="0"+m;
		y=document.bar.year.value;
		//document.res.cdate.value=document.f.elements[num-1].value+"<?=$date_delimiter?>"+document.bar.month.value+"<?=$date_delimiter?>"+document.bar.year.value;
		document.f.elements[num-1].style.color="red";
		n=document.res.oldnum.value;
		if (n=="") n=0;
		document.f.elements[n].style.color="black";
		document.res.oldnum.value=num-1;
	} else {
		document.res.cdate.value="";
	}
	<?=$res?>=d+"<?=$date_delimiter?>"+m+"<?=$date_delimiter?>"+y;
	<?=$cmd?>
}

function afterhour() {
	var h,m,hm;
	hm=document.f_res.time.value.split(":");
	if(hm.length==2) {
		if(hm[0]=="23")
			hm[0]=0;
		h=eval(hm[0])+1;
		m=hm[1];
		document.f_res.time.value=h+":"+m;
	} else {
		dt = new Date();
		h=dt.getHours()+1;
		m=dt.getMinutes();
		if (h<10)
			h="0"+h;
		if (m<10)
			m="0"+m;
		document.f_res.time.value=h+":"+m;
	}
	document.f_res.time.style.color='<?=$red?>';
	<?=$cmd_time?>
	ins_dttm()
}
function backhour() {
	var h,m,hm;
	hm=document.f_res.time.value.split(":");
	if(hm.length==2) {
		if(hm[0]=="0")
			hm[0]=24;
		h=eval(hm[0])-1;
		m=hm[1];
		document.f_res.time.value=h+":"+m;
	} else {
		dt = new Date();
		h=dt.getHours()-1;
		m=dt.getMinutes();
		if (h<10)
			h="0"+h;
		if (m<10)
			m="0"+m;
		document.f_res.time.value=h+":"+m;
	}
	document.f_res.time.style.color='<?=$red?>';
	<?=$cmd_time?>
	ins_dttm()
}
function today() {
	dt = new Date();
	var d=dt.getDate();
	if(d<10) d='0'+d;
	var m=dt.getMonth()+1;
	if(m<10) m='0'+m;
	var y=dt.getFullYear();
	document.f_res.date.value=d+"."+m+"."+y;
	document.f_res.date.style.color='<?=$red?>';
	document.f_res.time.value="";
	document.f_res.time.style.color='<?=$red?>';
	afterhour();
	ins_dttm()
}
function tomorrow() {
	var ms,oneday;
	dt = new Date();
	ms=Date.UTC(dt.getYear(),dt.getMonth(),dt.getDate(),0,0,0);
	oneday=24*60*60*1000;
	dt=new Date(ms+oneday);
	var d=dt.getDate();
	if(d<10) d='0'+d;
	var m=dt.getMonth()+1;
	if(m<10) m='0'+m;
	var y=dt.getFullYear();
	if(y<2000)
		y+=1900;
	document.f_res.date.value=d+"."+m+"."+y;
	document.f_res.date.style.color='<?=$red?>';
	document.f_res.time.value="";
	ins_dttm()
}
function afterweek() {
	var ms,oneday;
	dt = new Date();
	ms=Date.UTC(dt.getYear(),dt.getMonth(),dt.getDate(),0,0,0);
	oneday=24*60*60*1000;
	dt=new Date(ms+(oneday*7));
	var d=dt.getDate();
	if(d<10) d='0'+d;
	var m=dt.getMonth()+1;
	if(m<10) m='0'+m;
	var y=dt.getFullYear();
	if(y<2000)
		y+=1900;
	document.f_res.date.value=d+"."+m+"."+y;
	document.f_res.date.style.color='<?=$red?>';
	document.f_res.time.value="";
	ins_dttm()
}
function ins_dttm() {
	if(document.f_res.time.value=="")
		document.f_res.time.value="00:00";
	if(document.f_res.date.value=="") {
		document.f1.tm.value=0;
		document.f2.tm1.value=0;
	} else {
		document.f1.tm.value=document.f_res.date.value+" "+document.f_res.time.value;
		document.f2.tm1.value=document.f_res.date.value+" "+document.f_res.time.value;
		//alert(document.f1.tm.value);
	}
	//document.f1.tm.style.color="#DF0000";
}
function no_time() {
	document.f_res.date.value="";
	document.f_res.time.value="";
}

var d = new Date();
var cur_day=0,cur_month=0,cur_year=0;
cur_day=<?=$d?>;
cur_month=<?=$m?>;
cur_year=<?=$y?>;
//today();
</script>
<table class='cal'>
<tr>
	<td>
	<script type='text/javascript'>
		disp_form();
		fill_form (<?=$m?>,<?=$y?>);
	</script>
	</td>

	<td class='time'>
	<?
	if(!@$cal_not_show_time) {
		$events=" onmouseover='className=\"time_mouseover\"' onmouseout='className=\"time_mouseout\"'  class='time_mouseout'";
		print "<table class='time'><tr><td rowspan='11' class='time_time'>в р е м €</td><td $events onclick='document.f_res.time.value=\"\"'>нет</td></tr>\n";
		for($i=9;$i<=18;$i++) {
			if($i<10) $i="0$i";
			print "<tr><td $events onclick='document.f_res.time.value=\"$i:00\";document.f_res.time.style.color=\"$red\";$cmd_time'>$i</td></tr>";
		}
		print "</table>\n";
	}
	?>
	</td>

</tr>
<tr>
	<!--<td class='res' colspan='2'>-->
	<td class='res' colspan='2'>
	<p class='res_controls'>
	<a href='javascript:today()'>сег</a>.
	<a href='javascript:tomorrow()'>зав</a>.
	<a href='javascript:afterweek()'>+нед</a>.
	<a href='javascript:backhour()'>-час</a>.
	<a href='javascript:afterhour()'>+час</a>.
	</p>
	<form name='f_res' class='f_res'>
	<?
		if(@$cal_not_show_time) $min=0;
		if($d<10) $d="0".intval($d); if($m<10) $m="0".intval($m); if($h<10) $h="0".intval($h); if($i<10) $min="0".intval($min);
		if($y=="1970") {
			$d=date("d"); $m=date("m"); $y=date("Y");  $h=date("H");  $min=date("i");
		}
	?>
	<input style='width:120px;' type='text' name='date' value='<?print "$d.$m.$y";?>' onchange='this.style.color="<?=$red?>"'>
	<input style='width:80px;'  type='text' name='time' value='<?print "$h:$min";?>' style='width:100;' onchange='this.style.color="<?=$red?>"'>
	<!--<input type='hidden' name='time' value='00:00' >-->
	<input type='hidden' name='d' value='<?=$d?>'>
	<input type='hidden' name='m' value='<?=$m?>'>
	<input type='hidden' name='y' value='<?=$y?>'>
	<input type='hidden' name='h' value='<?=$h?>'>
	<input type='hidden' name='i' value='<?=$min?>'>

	<!--<input type='button' name='ins' value='без времени'  style='width:70;background-color:#DDD;'  onclick='no_time();'>-->
	</form>
	</td>
</tr>
</table>
<?
?>