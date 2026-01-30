<?
$title="Добавить клиента";
include "cab_top.inc.php";

function validateInnWithDaData($inn, $token) {
    $url = "https://suggestions.dadata.ru/suggestions/api/4_1/rs/findById/party";
    
    $data = json_encode(["query" => $inn]);
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json",
            "Accept: application/json",
            "Authorization: Token " . $token
        ]
    ]);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code === 200) {
        $result = json_decode($response, true);
        
        if (!empty($result['suggestions']) && 
            isset($result['suggestions'][0]['data']['inn']) &&
            $result['suggestions'][0]['data']['inn'] === $inn) {
            return [
                'valid' => true,
                'company' => $result['suggestions'][0],
                'message' => 'ИНН действителен'
            ];
        } else {
            return [
                'valid' => true,
                'message' => 'Компания с таким ИНН не найдена'
            ];
        }
    } else {
        return [
            'valid' => false,
            'message' => 'Ошибка проверки ИНН'
        ];
    }
}

function secureTruncate($input, $max_length) {
    $input = trim($input ?? '');
    $input = mb_substr($input, 0, $max_length);
    //return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    return $input;
}

if(isset($_GET['client_uid'])) {
	$_POST['client_uid']=$_GET['client_uid'];
	$_SERVER['REQUEST_METHOD'] ='POST';
}
// Usage in form processing:
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$errors = [];
	$success = false;
	$company_data = null;
	$dadata_token = "5b8fa0c1788ecb8c7d233ef628990ef78ba09273";

	// Form processing
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		// Sanitize and truncate inputs

		$client_uid=0;
		if(isset($_POST['client_uid'])) {
			$client_uid=intval($_POST['client_uid']);
			if(!isset($_POST['btn_save']) && $r=$db->fetch_assoc($db->query("SELECT * FROM cards WHERE del=0 AND uid='$client_uid'"))) {
				$_POST['first_name']=$r['name'];
				$_POST['last_name']=$r['surname'];
				$_POST['phone']=$r['mob_search'];
				$_POST['email']=$r['email'];
				$_POST['comm']=$r['comm1'];
				$arr=$db->cards_read_par($client_uid);
				//print_r($arr); exit;
				$_POST['inn']=$arr['inn'];
				$_POST['company']=$arr['company'];
				$_POST['vid']=$arr['vid'];
				$_POST['website']=$arr['website'];
				$_POST['city']=$arr['city'];
				$_POST['addr']=$arr['addr'];
			} else {
				//~ $errors['edit'] = 'Не найдено в базе';
				//~ print_r($_POST);
			}
		}
		
		$inn = secureTruncate($_POST['inn'] ?? '', 10);
		$company = secureTruncate($_POST['company'] ?? '', 255);
		$vid = secureTruncate($_POST['vid'] ?? '', 255);
		$website = secureTruncate($_POST['website'] ?? '', 255);
		$city = secureTruncate($_POST['city'] ?? '', 100);
		$addr = secureTruncate($_POST['addr'] ?? '', 255);
		$first_name = secureTruncate($_POST['first_name'] ?? '', 100);
		$last_name = secureTruncate($_POST['last_name'] ?? '', 100);
		$phone = $db->check_mob(secureTruncate($_POST['phone'] ?? '', 20));
		$email = secureTruncate($_POST['email'] ?? '', 255);
		$comm = secureTruncate($_POST['comm'] ?? '', 1000);
		
		// Validation
		if (empty($inn)) {
			$errors['inn'] = 'ИНН обязателен для заполнения';
		} elseif (!preg_match('/^\d{10}$/', $inn)) {
			$errors['inn'] = 'ИНН должен содержать ровно 10 цифр';
		} else {
			// Validate INN with DaData API
			$inn_validation = validateInnWithDaData($inn, $dadata_token);
			
			if (!$inn_validation['valid']) {
				$errors['inn'] = $inn_validation['message'];
			} else {
				// INN is valid - auto-fill company data
				$company_data = $inn_validation['company'];
				
				// Auto-fill company name if empty
				if (empty($company) && isset($company_data['value'])) {
					//$company = $company_data['value'];
				}
				
				// Auto-fill address if empty
				if (empty($addr) && isset($company_data['data']['address']['value'])) {
					//$addr = $company_data['data']['address']['value'];
				}
				
				// Auto-fill city if empty
				if (empty($city) && isset($company_data['data']['address']['data']['city'])) {
					//$city = $company_data['data']['address']['data']['city'];
				}
			}
		}
		
		// Validate other required fields
		if (empty($company)) {
			$errors['company'] = 'Название компании обязательно';
		}

		if (empty($vid)) {
			$errors['vid'] = 'Укажите вид бизнеса и вывеску';
		}
		
		// Validate email format
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$errors['email'] = 'Некорректный email адрес';
		}
		
		// Validate website format
		if (empty($city)) {
			$errors['city'] = 'Укажите город';
		}
		if (empty($addr)) {
			$errors['addr'] = 'Укажите адрес';
		}
		if (empty($first_name)) {
			$errors['first_name'] = 'Укажите контактное лицо';
		}
		if (!$db->check_mob($phone)) {
			$errors['phone'] = 'Укажите номер телефона';
		}
		
		if($db->num_rows($db->query("SELECT * FROM cards
						JOIN cards_add ON cards.uid=cards_add.uid
						WHERE cards.del=0 AND par='inn' AND val='$inn' AND user_id!='$user_id' AND user_id>0")) > 0) {
			$errors['client_holded']="ИНН <b>$inn</b> уже закреплен за другим партнером, изменение невозможно";
		}
		if($db->num_rows($db->query("SELECT * FROM cards
						JOIN cards_add ON cards.uid=cards_add.uid
						WHERE cards.del=0 AND (email='$email' OR mob_search='$phone') AND user_id!='$user_id' AND user_id>0")) > 0) {
			$errors['client_holded']="Клиент с таким тлф или емэйл уже закреплен за другим партнером, изменение невозможно";
		}

		// If no errors, process the form
		if (empty($errors) && isset($_POST['btn_save'])) {
			$r=[
				'first_name'=>$first_name,
				'last_name'=>$last_name,
				'phone'=>$phone,
				'email'=>$email,
				'city'=>$city,
				'user_id'=>$user_id,
				'klid'=>$klid,
				'comm1'=>$comm,
			];
			if($uid=$db->cards_add($r, $update_if_exist=$client_uid)) {
				$db->save_comm($uid,$user_id,"Добавлен партнером",1);
				$db->cards_add_par($uid,'inn',$inn);
				$db->cards_add_par($uid,'company',$company);
				$db->cards_add_par($uid,'vid',$vid);
				$db->cards_add_par($uid,'website',$website);
				$db->cards_add_par($uid,'city',$city);
				$db->cards_add_par($uid,'addr',null,$addr);
				//$db->notify_me("HERE_$uid");
				print "<p class='alert alert-success mt-4' >Сохранено успешно</p>";
				$success = true;
			} else
				$errors['cards_add'] = 'Ошибка добавления в базу';
		} else {
			print "<div class='mt-4' >";
			foreach($errors AS $msg) {
				if(!empty($msg))
					print "<p class='alert alert-warning'>$msg</p>\n";
			}
			print "</div>";
		}
	}
}

?>		
		<div class="mt-5">
			<h2 title='вернуться'>
				<a href='cab3.php' class='' target=''>
					<img src='https://winwinland.ru/img/out.svg' alt=''>
				</a>
			</h2>
			<h2><?=!$client_uid?"Добавить клиента":"Редактировать данные клиента"?></h2>
			<div class='card font_rounded p-3' >
			<form method='POST'>
				<div class="form-group">
					<label for="innInput">ИНН *</label>
					<div id="innFeedback" class="invalid-feedback"></div>
					<input type="text" 
						   class="form-control" 
						   id="innInput" 
						   name="inn"
						   placeholder="Введите ИНН"
						   maxlength="12"
						   pattern="\d{10,12}"
						   value="<?=htmlspecialchars($_POST['inn'] ?? '')?>">
				</div>
				<div class="form-group">
					<label for="company">Название юрлица *</label>
					<input type="text" class="form-control" id="company" name="company" placeholder="Название юрлица"
						   value="<?=htmlspecialchars($_POST['company'] ?? '')?>">
				</div>
				<div class="form-group">
					<label for="vid">Вид бизнеса и вывеска *</label>
					<input type="text" class="form-control" id="vid" name="vid" placeholder="Например: Салон красоты 'Бьюти'"
						   value="<?=htmlspecialchars($_POST['vid'] ?? '')?>">
				</div>
				<div class="form-group">
					<label for="website">Сайт</label>
					<input type="text" class="form-control" id="website" name="website" placeholder="Сайт"
						   value="<?=htmlspecialchars($_POST['website'] ?? '')?>">
				</div>
				<div class="form-group">
					<label for="city">Город *</label>
					<input type="text" class="form-control" id="city" name="city" placeholder="Город"
						   value="<?=htmlspecialchars($_POST['city'] ?? '')?>">
				</div>
				<div class="form-group">
					<label for="addr">Адрес *</label>
					<input type="text" class="form-control" id="addr" name="addr" placeholder="Адрес"
						   value="<?=htmlspecialchars($_POST['addr'] ?? '')?>">
				</div>
				<div class="form-group">
					<label for="email">E-mail филиала *</label>
					<input type="email" class="form-control" id="email" name="email" placeholder="E-mail филиала"
						   value="<?=htmlspecialchars($_POST['email'] ?? '')?>">
					<small>* если несколько филиалов с одинаковым ИНН укажите уникальный емэйл для филиала</small>
				</div>
				<div class="form-group">
					<label for="contact_person">Контактное лицо *</label>
					<div class="form-row">
						<div class="col">
							<input type="text" 
								   class="form-control" 
								   id="first_name" 
								   name="first_name" 
								   placeholder="Имя"
								   value="<?=htmlspecialchars($_POST['first_name'] ?? '')?>">
						</div>
						<div class="col">
							<input type="text" 
								   class="form-control" 
								   id="last_name" 
								   name="last_name" 
								   placeholder="Фамилия"
								   value="<?=htmlspecialchars($_POST['last_name'] ?? '')?>">
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="phone">Телефон *</label>
					<input type="phone" class="form-control" id="phone" name="phone" placeholder="Телефон"
						   value="<?=htmlspecialchars($_POST['phone'] ?? '')?>">
				</div>
				<div class="form-group">
					<textarea class="form-control" id="comm" name="comm" placeholder="Заметки" rows='3'><?=htmlspecialchars($_POST['comm'] ?? '')?></textarea>
				</div>
				<button type='submit' class='btn btn-primary' name='btn_save' value='yes'>Отправить</button>
				<a href='cab3.php' class='ml-2 btn btn-light' target=''>Отменить</a>
			</form>
			</div>
		</div>

<!--
		<div class='mt-5 d-flex ' >
			<a class="button_wwl flex-fill " href="#"> Справка </a>
			<a class="button_wwl flex-fill ml-3" href="#"> Обучение </a>
		</div>
-->

<!--
		<div class='mt-3 d-flex ' >
			<a class="button_wwl gradient flex-fill font-weight-normal" href="cab3_add.php"> Новый клиент </a>
		</div>
-->

<script>
	document.addEventListener('DOMContentLoaded', function() {
		const innInput = document.getElementById('innInput');
		const innFeedback = document.getElementById('innFeedback');
		let timeoutId;
		
		innInput.addEventListener('input', function() {
			clearTimeout(timeoutId);
			const innValue = this.value.replace(/\D/g, '');
			
			if (innValue.length === 10) {
				// Debounce to avoid multiple rapid calls
				timeoutId = setTimeout(() => {
					callDaDataAPI(innValue);
				}, 500);
			}
		});
		
		async function callDaDataAPI(inn) {
			const token = "5b8fa0c1788ecb8c7d233ef628990ef78ba09273";
			const url = "https://suggestions.dadata.ru/suggestions/api/4_1/rs/findById/party";
			
			// Show loading state
			innInput.classList.add('loading');
			
			try {
				const response = await fetch(url, {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
						'Accept': 'application/json',
						'Authorization': `Token ${token}`
					},
					body: JSON.stringify({ query: inn })
				});
				
				const data = await response.json();
				handleApiResponse(data);
				
			} catch (error) {
				console.error('API Error:', error);
				document.getElementById('company').value = '';
				showError('Ошибка при поиске компании');
			} finally {
				innInput.classList.remove('loading');
			}
		}
		
		function handleApiResponse(data) {
			if (data.suggestions && data.suggestions.length > 0) {
				const company = data.suggestions[0];
				console.log('Found:', company);
				// Auto-fill other fields
				autoFillCompanyData(company);
			} else {
				document.getElementById('company').value = '';
				innFeedback.textContent = 'Компания не найдена';
				innFeedback.style.display = 'block';
				innFeedback.style.color = '#FFA500';
				//showError('Компания не найдена');
			}
		}
		
		function autoFillCompanyData(company) {
			console.log(company);
			// Example: auto-fill other form fields
			document.getElementById('company').value = company.value || '';
			innFeedback.style.display = 'none';

			// document.getElementById('companyAddress').value = company.data.address.value || '';
			// document.getElementById('companyKpp').value = company.data.kpp || '';
		}
		
		function showError(message) {
			// Display error message to user
			console.error(message);
		}
	});
</script>
		
<? include "cab_bottom.inc.php"; ?>		
