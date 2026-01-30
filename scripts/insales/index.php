<?php
include "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
$db=new vkt('vkt');
include "insales_app_credentials.inc.php";
include "insales_func.inc.php";
$title="Winwinland for inSales ИНструкция по установке модуля";

// At the top of the file, add BC processing
$bc = isset($_GET['bc']) ? intval($_GET['bc']) : 0;
$klid = 0; 
$user_id = 0; 
$uid = 0;

if($bc) {
    if($klid = $db->get_klid_by_bc($bc)) {
        $user_id = $db->get_user_id($klid);
    }
}

// At the beginning of the file, after session_start()
session_start();

// At the top of the file after session_start()
$notification_title = 'Новая заявка c app-insales.winwinland.ru';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];

    // Check last submission time
    if (isset($_SESSION['last_submit']) && 
        (time() - $_SESSION['last_submit']) < 1 * 60) {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Вы уже отправляли форму. Повторная отправка будет доступна через некоторое время.'];
        header('Location: ' . $_SERVER['PHP_SELF'] . '#message');
        exit();
    }

    // Функция для безопасной обработки входных данных
    function sanitize($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }
    
    // Получаем и валидируем данные
    $name = isset($_POST['name']) ? sanitize($_POST['name']) : '';
    $phone = isset($_POST['phone']) ? sanitize($_POST['phone']) : '';
    $email = isset($_POST['email']) ? sanitize($_POST['email']) : '';
    $shop_url = isset($_POST['shop_url']) ? sanitize($_POST['shop_url']) : '';
    $insales_id = isset($_POST['insales_id']) ? sanitize($_POST['insales_id']) : '';
    $agreement_all = isset($_POST['agreement_all']);
    
    // Валидация
    if (empty($name) || strlen($name) < 2) {
        $errors[] = "Имя должно содержать минимум 2 символа";
    }
    
    if (empty($phone) || !preg_match("/^[0-9+\-\s()]{10,20}$/", $phone)) {
        $errors[] = "Введите корректный номер телефона";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Введите корректный email адрес";
    }
    
    if (empty($shop_url) || !filter_var($shop_url, FILTER_VALIDATE_URL)) {
        $errors[] = "Введите корректный URL магазина";
    }
    
    if (empty($insales_id) || !preg_match("/^\d+$/", $insales_id)) {
        $errors[] = "Номер аккаунта inSales должен содержать только цифры";
    }
    
    if (!$agreement_all) {
        $errors[] = "Необходимо согласиться с условиями";
    }

    if (empty($errors)) {
        $tg_bot_notif=$db->dlookup("tg_bot_notif","0ctrl","id=1");
        $DB200="https://for16.ru/d/1000";
        try {
            // Добавляем карточку в CRM
            $card_data = [
                'first_name' => $name,
                'last_name' => '',
                'phone' => $phone,
                'email' => $email,
                'city' => '',
                'comm1' => "", //"$notification_title\nСайт магазина: $shop_url\nID магазина: $insales_id",
                'test_cyrillic' => true,
                'wa_allowed' => 0,
                'tz_offset' => isset($_POST['tz_offset']) ? intval($_POST['tz_offset']) : 3, // Use browser's timezone or default to 3
                'klid' => $klid,
                'user_id' => $user_id
            ];
            
            $uid = $db->cards_add($card_data);
            
            if ($uid) {
                // Save comment with standardized title
                $db->save_comm($uid, 0, "$notification_title\nСайт магазина: $shop_url\nID магазина: $insales_id",51,$insales_id,0,true);
                $db->tag_add($uid,27);
                $db->mark_new($uid,3);
                
                // Notify with standardized title
                $db->notify($uid,"$notification_title\nСайт магазина: $shop_url\nID магазина: $insales_id");
                //$db->notify_me("$notification_title\nСайт магазина: $shop_url\nID магазина: $insales_id");
                // Email notification with standardized title
                $mail_body = "Имя: $name\nТелефон: $phone\nEmail: $email\nСайт магазина: $shop_url\nID магазина: $insales_id";
                mail('info@winwinland.ru', $notification_title, $mail_body);

                $_SESSION['last_submit'] = time();
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Форма успешно отправлена!'];
            }
            header('Location: ' . $_SERVER['PHP_SELF'] . '#message');
            exit();
        } catch (Exception $e) {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'Произошла ошибка при отправке формы. Пожалуйста, попробуйте снова.'];
            header('Location: ' . $_SERVER['PHP_SELF'] . '#message');
            exit();
        }
    } else {
        // Handle validation errors
        $_SESSION['message'] = ['type' => 'danger', 'text' => implode('<br>', $errors)];
        header('Location: ' . $_SERVER['PHP_SELF'] . '#message');
        exit();
    }
}

include "land_top.inc.php";
?>

<h2 class='text-center' style='color:#EC00B8;' >Winwinland for inSales</h2>
<h2 class='text-center' >Приложение для создания партнерских программ для вашего магазина </h2>

<h2 class='text-center ' style='color:#32a9ec;' >Что это дает</h2>
<div>
    <p class='text-center font-weight-bold' >Партнерская программа на WinWinLand дает возможность увеличить продажи, сделать клиентов лояльными и привязать их к вашему бизнесу!</p>

    <p><strong>WinWinLand</strong> — это ваш надежный помощник для увеличения продаж и эффективного управления партнерской программой. </p>
    <p class='text-center font-weight-bold' >Запустите партнерскую программу среди клиентов, инфлюенсеров, блогеров и всех, кто может вас рекомендовать. 
    Стимулируйте партнеров, создавайте амбассадоров и управляйте сарафанным радио самым эффективным способом. 
    </p>
    <p>Вот, что мы предлагаем:
    </p>

    <ul>
        <li>
			<b>Учет партнерских промокодов</b>
			К каждому промокоду можно привязать партнера, назначить ему вознаграждение в рублях или процентах.
			Вознаграждение будет рассчитываться автоматически при каждой покупке с этим промокодом.
			Выплачивать вознаграждение можно автоматически баллами магазины.
			У партнеров есть личные кабинеты, где они видят все операции.
			<br>
			Неважно сколько у вас партнеров, промокодов, как вы выплачиваете вознаграждения -
			все операции можно полностью автоматизировать.
		</li>
        <li>
			<b>Генерация партнерских ссылок</b> на ваш магазин, с установкой партнерских вознаграждений на двух уровнях.
		</li>
		<li>
			<b>Личные кабинеты у партнеров</b>. Какждый партнер автоматически получает ссылку на личный кабинет,
			что обеспечивает доверие со стороны партнеров к вашему бизнесу.
		</li>
        <li>
            <strong>Гибкие условия вознаграждения</strong>: задайте партнерские выплаты на двух уровнях — в процентах, рублях, без ограничений или на определенное количество продаж.
        </li>
        <li>
            <strong>Индивидуальные настройки</strong> вознаграждений: устанавливайте сроки закрепления клиентов за партнерами и создавайте уникальные условия для отдельных партнеров и товаров.
        </li>
        <li>
            <strong>Доступ к мощной CRM системе</strong>, где вы можете отслеживать партнеров, клиентов и их взаимодействия, а также подключать чат-ботов и создавать воронки продаж.
        </li>
        <li>
            <strong>Подробная аналитика</strong>: отчеты по партнерам, продажам и меткам для глубокого анализа эффективности вашей программы.
        </li>
    </ul>

    <p><strong>Бонус:</strong> Полный доступ к функционалу CRM WinWinLand без ограничений, включая рассылки, чат-боты и интеграции.</p>

    <p class='text-center font-weight-bold' >Присоединяйтесь к WinWinLand и ускорьте рост ваших продаж уже сегодня!</p>
</div>
<P class='text-center small' ><a href='https://winwinland.ru/pdf/winwinland_for_ecommerce.pdf' class='' target='_blank'>скачать PDF</a></P>

<h2 class='text-center' >Как работает приложение</h2>

<div class="youtube my-4">
    <div id="player"></div>
    <script>
       var player = new Playerjs({id:"player",
           file:"https://98a2bdd6-8f95-4630-985e-659c5575e2e6.selcdn.net/Promo/WinWinLand_ecommerce_2/master.m3u8",
           poster:"https://98a2bdd6-8f95-4630-985e-659c5575e2e6.selcdn.net/Promo/WinWinLand_ecommerce_2/poster.jpg"
           });
    </script>
</div>

<h2 class='text-center mb-3' >Заполните форму ниже и получите доступ к приложению</h2>
<p class='text-center text-secondary' >Бесплатный период 14 дней</p>

<div id="message">
    <?php 
    if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo htmlspecialchars($_SESSION['message']['type']); ?>" role="alert">
            <?php 
            echo htmlspecialchars($_SESSION['message']['text']); 
            unset($_SESSION['message']);
            ?>
        </div>
    <?php endif; ?>
</div>

<div class="container">
    <form id="accessForm" action="" method="POST" class="my-4 mb-5">
        <input type="hidden" name="bc" value="<?php echo htmlspecialchars($bc); ?>">
        <input type="hidden" name="tz_offset" id="tz_offset">
        
        <div class="form-group">
            <label for="name">Имя*</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        
        <div class="form-group">
            <label for="phone">Телефон*</label>
            <input type="tel" class="form-control" id="phone" name="phone" required>
        </div>
        
        <div class="form-group">
            <label for="email">E-mail*</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="shop_url">Сайт магазина*</label>
            <input type="url" class="form-control" id="shop_url" name="shop_url" placeholder="https://your-shop.ru" required>
        </div>
        
        <div class="form-group">
            <label for="insales_id">Номер аккаунта inSales* 
                <i class="fa fa-question-circle" style="cursor: pointer;" 
                   data-toggle="modal" data-target="#accNumberModal"></i>
            </label>
            <input type="text" class="form-control" id="insales_id" name="insales_id" required>
        </div>
        
        <div class="form-group">
            <div class="custom-control custom-checkbox small">
                <input type="checkbox" class="custom-control-input" id="agreementAll" name="agreement_all" required>
                <label class="custom-control-label small" for="agreementAll">
                    Используя функции платформы Winwinland, я соглашаюсь c <a href="https://winwinland.ru/privacypolicy.pdf" target="_blank" rel="noopener noreferrer">Политикой конфиденциальности</a>, условиями <a href="https://winwinland.ru/dogovor.pdf" target="_blank" rel="noopener noreferrer">Договора-оферты</a> и подтверждаю <a href="https://winwinland.ru/agreement.pdf" target="_blank" rel="noopener noreferrer">Согласие на обработку персональных данных</a>
                </label>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary mb-4">Получить доступ</button>
    </form>
</div>

<!-- Add Modal -->
<div class="modal fade" id="accNumberModal" tabindex="-1" role="dialog" aria-labelledby="accNumberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="accNumberModalLabel">Номер аккаунта inSales</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img src="acc_number.jpg" class="img-fluid" alt="Номер аккаунта inSales">
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('accessForm').addEventListener('submit', function(e) {
    const checkbox = document.getElementById('agreementAll');
    if (!checkbox.checked) {
        e.preventDefault();
        alert('Пожалуйста, подтвердите согласие с условиями');
        checkbox.focus();
    }
});

// Get timezone offset in hours (converting from minutes and handling the sign)
document.getElementById('tz_offset').value = -(new Date().getTimezoneOffset() / 60);
</script>

<?
include "land_bottom.inc.php";
?>
