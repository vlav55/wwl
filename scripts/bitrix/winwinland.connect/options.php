<?php
use Bitrix\Main\Config\Option;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin.php");

$moduleId = "winwinland.connect";

if ($_SERVER["REQUEST_METHOD"] == "POST" && check_bitrix_sessid()) {
    $currentToken = trim($_POST["token_wwl"]);
    Option::set($moduleId, "currentToken", $currentToken);
    
    $currentKey = trim($_POST["key_wwl"]);
    Option::set($moduleId, "currentKey", $currentKey);
    
    BXClearCache(true); // Очистка кеша
}

$currentToken = Option::get($moduleId, "currentToken");
$currentKey = Option::get($moduleId, "currentKey");
if(strlen($currentKey) > 0){
    $hashKey = md5($currentKey);
}

$APPLICATION->SetTitle("Настройки модуля WinWinLand Connect");
?>

<form method="post" action="<?= $APPLICATION->GetCurPage() ?>?mid=<?= urlencode($moduleId) ?>&lang=<?= LANGUAGE_ID ?>">
    <?= bitrix_sessid_post() ?>
    <table>
        <tr>
            <td>Токен:</td>
            <td>
                <input type="text" name="token_wwl" value="<?= htmlspecialcharsbx($currentToken) ?>" size="50">
            </td>
        </tr>
        <tr>
            <td>Секретный ключ:</td>
            <td>
                <input type="text" name="key_wwl" value="<?= htmlspecialcharsbx($currentKey) ?>" size="50">
            </td>
        </tr>
        <tr>
            <td></td>
            <td style="text-align: end;">
                <a href="https://app-bitrix.winwinland.ru/doc.php" target="_blank">Документация</a>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <a href="https://app-bitrix.winwinland.ru/go.php<?=($hashKey) ? '?'.$hashKey : ''?>" target="_blank">Настройки</a>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="submit" value="Сохранить">
            </td>
        </tr>
    </table>
</form>

<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");