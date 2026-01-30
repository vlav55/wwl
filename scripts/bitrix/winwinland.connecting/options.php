<?php
use Bitrix\Main\Config\Option;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin.php");
$APPLICATION->SetTitle("Настройки модуля WinWinLand Connecting");

$moduleId = "winwinland.connecting";

if ($_SERVER["REQUEST_METHOD"] == "POST" && check_bitrix_sessid()) {
    $currentToken = trim(htmlspecialcharsbx($_POST["token_wwl"]));
    Option::set($moduleId, "currentToken", $currentToken);
    
    $currentKey = trim(htmlspecialcharsbx($_POST["key_wwl"]));
    Option::set($moduleId, "currentKey", $currentKey);
    
    //if(isset($_POST["domains_wwl"]) && !empty($_POST["domains_wwl"])){
        $arCurrentDomains = $_POST["domains_wwl"];
        foreach($arCurrentDomains as $k => $v){
            $arCurrentDomains[$k] = trim(htmlspecialcharsbx($v));
        }
        if(is_array($arCurrentDomains)){
            Option::set($moduleId, "currentDomains", implode('|', $arCurrentDomains));
        }else{
            Option::set($moduleId, "currentDomains", '');
        }
    //}
    
    BXClearCache(true); // Очистка кеша
}

$currentToken = htmlspecialcharsbx(trim(Option::get($moduleId, "currentToken")));
$currentKey = htmlspecialcharsbx(trim(Option::get($moduleId, "currentKey")));

$arCurrentDomains = explode('|', htmlspecialcharsbx(Option::get($moduleId, "currentDomains")));

$arDomains = [];
$rsSites = CSite::GetList($by = "sort", $order = "asc");
while ($arSite = $rsSites->Fetch()) {
    $arDomain = explode(PHP_EOL, $arSite['DOMAINS']);
    foreach($arDomain as $k => $v){
        $arDomain[$k] = trim(htmlspecialcharsbx($v));
    }
    $arDomains = array_merge($arDomains, $arDomain);
}
?>

<form method="post" action="<?= $APPLICATION->GetCurPage() ?>?mid=<?= urlencode($moduleId) ?>&lang=<?= LANGUAGE_ID ?>">
    <?= bitrix_sessid_post() ?>
    <table>
        <tr>
            <td>Токен:</td>
            <td>
                <input type="text" name="token_wwl" value="<?= $currentToken ?>" size="50">
            </td>
        </tr>
        <tr>
            <td>Секретный ключ:</td>
            <td>
                <input type="text" name="key_wwl" value="<?= $currentKey ?>" size="50">
            </td>
        </tr>
        <tr>
            <td>Домены (для установки cookies):</td>
            <td>
                <select multiple="" name="domains_wwl[]" size="5" style="width: 342px">
                    <?foreach($arDomains as $domain):?>
                    <option value="<?=$domain?>" <?= (in_array($domain, $arCurrentDomains)) ? 'selected' : '' ?>><?=$domain?></option>
                    <? endforeach;?>
                </select>
            </td>
        </tr>
        <tr>
            <td></td>
            <td style="text-align: end;">
                <a href="https://app-bitrix.winwinland.ru/doc.php" target="_blank">Документация</a>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <a href="https://app-bitrix.winwinland.ru/go.php?<?=($currentKey) ? $currentKey : ''?>&<?=($currentToken) ? $currentToken : ''?>" target="_blank">Настройки</a>
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