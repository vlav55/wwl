<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\EventManager;
use Bitrix\Main\ORM\Fields;

Loc::loadMessages(__FILE__);


class winwinland_connecting extends CModule
{
    public $MODULE_ID = "winwinland.connecting";
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $MODULE_GROUP_RIGHTS;
    public $PARTNER_NAME;
    public $PARTNER_URI;

    public function __construct()
    {
        $this->MODULE_ID = "winwinland.connecting";
        $this->MODULE_NAME = Loc::getMessage("WWL_CONNECT_MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("WWL_CONNECT_MODULE_DESCRIPTION");
        $this->PARTNER_NAME = "WinWinLand";
        $this->PARTNER_URI = "https://winwinland.ru";
        $this->MODULE_VERSION = "1.0.7";
        $this->MODULE_VERSION_DATE = "2025-05-07 00:00:00";
    }

    public function DoInstall()
    {
        file_put_contents($_SERVER['DOCUMENT_ROOT'].'/wwl2_send_log_'.date('d_m_Y').'.txt', '-------- DoInstall --------'.PHP_EOL, FILE_APPEND);
        RegisterModule($this->MODULE_ID);
        $this->RegisterEvents();
    }

    public function DoUninstall()
    {
        $this->UnRegisterEvents();
        UnRegisterModule($this->MODULE_ID);
    }

    public function RegisterEvents()
    {
        $eventManager = EventManager::getInstance();
        // Регистрируем обработчик для регистрации данных о посещении
        $eventManager->registerEventHandler(
            "main",
            "OnProlog",
            $this->MODULE_ID,
            "WWL\\Connecting\\EventHandler",
            "OnProlog"
        );
        
        // Регистрируем обработчик для смены статуса заказа
        $eventManager->registerEventHandler(
            "sale",
            "OnSaleStatusOrder",
            $this->MODULE_ID,
            "WWL\\Connecting\\EventHandler",
            "onStatusChange"
        );
        
        $eventManager->registerEventHandler(
            "sale",
            "OnSaleOrderSaved",
            $this->MODULE_ID,
            "WWL\\Connecting\\EventHandler",
            "OnSaleOrderSaved"
        );
    }

    public function UnRegisterEvents()
    {
        $eventManager = EventManager::getInstance(); // Исправлено
        $eventManager->unRegisterEventHandler(
            "main",
            "OnProlog",
            $this->MODULE_ID,
            "WWL\\Connecting\\EventHandler",
            "OnProlog"
        );

        $eventManager->unRegisterEventHandler(
            "sale",
            "OnSaleStatusOrder",
            $this->MODULE_ID,
            "WWL\\Connecting\\EventHandler",
            "onStatusChange"
        );
        
        $eventManager->unRegisterEventHandler(
            "sale",
            "OnSaleOrderSaved",
            $this->MODULE_ID,
            "WWL\\Connecting\\EventHandler",
            "OnSaleOrderSaved"
        );
    }
    
    
}