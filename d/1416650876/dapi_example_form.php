<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/core.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/md5.min.js"></script>
<script src="//code.jquery.com/jquery-3.6.0.min.js"></script>

<!--
$url = "https://for16.ru/d/2322626082/dapi.php";
$land_num="1"; //* обязательно 1-основной лэндинг
$client_name="Вася"; //*
$client_phone="89119998877"; //*
$client_email="test@mail.ru"; //не обязательно
$secret=md5($land_num.$client_name.$client_phone.'98f13708210194c475687be6106a3b84');
$bc=1234567890; //партнерский код, который был передан в URL лэндинга ?bc=
-->

<form method='GET' action='https://for16.ru/db1/dapi.php'>
<input type='hidden' name='bc' value='1002'> //Получено из GET параметра ?bc=1002 - это партнерский код, если есть, то передавать обязательно! (может и отсутствовать, тогда передавать не обязательно)
<input type='text' name='client_name' value='' id='p1'>
<input type='text' name='client_phone' value='' id='p2'>
<input type='text' name='client_email' value='' id='p3'> //может отсутствовать
<button type='submit' name='send' value='yes'>Send</button>

</form>

<script> //Код лучше загрузить из отдельного файла, чтобы не было явно видно
function md5(str) {
  return CryptoJS.MD5(str).toString();
}

function submit() {
	$("form").submit(function(e) {
		e.preventDefault();
		var secret=md5('1'+$("#p1").val()+$("#p2").val()+'98f13708210194c475687be6106a3b84');
		console.log("HERE_"+secret);
		console.log('1'+$("#p1").val()+$("#p2").val()+'98f13708210194c475687be6106a3b84');
		const queryParams = {
			land_num:'1',
		  client_name: $("#p1").val(),
		  client_phone: $("#p2").val(),
		  client_email: $("#p3").val(),
		  secret: secret,
		};
		$.ajax({
			type: 'POST',
			url: 'https://for16.ru/d/2322626082/dapi.php',
			data: queryParams,
			success: function(data) {
				console.log('RESULT - ',data);
			},
			error: function(jqXHR, textStatus, errorThrown) {
			console.error('Error getting results:', textStatus, errorThrown);
			},
		});
	});
}

$(document).ready(function() {
	submit();
});


</script>
