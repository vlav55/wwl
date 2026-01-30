<?php
namespace WWL\Connect;

use Bitrix\Main\Application;
use Bitrix\Main\Web\HttpClient;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Diag\Debug;
use Bitrix\Main\UserTable;
use Bitrix\Sale\Order;
use Bitrix\Sale\OrderStatus;


class EventHandler
{
    
    public static function onProlog()
    {
        // Получаем текущий URL и IP-адрес
        $request = Application::getInstance()->getContext()->getRequest();
        $currentUrl = $request->getRequestUri();
        $clientIp = $request->getRemoteAddress();

        if(strpos($currentUrl, 'bc=') !== false){
            setcookie('first_current_location', $currentUrl, time() + 3600*24*7*12*30, "/");
        }
    }
    
    /**
     * Обработчик события изменения статуса заказа.
     *
     * @param int $orderId - ID заказа
     * @param string $status - Новый статус заказа
     */
    public static function onStatusChange($orderId, $status)
    {
        // Загружаем заказ
        $order = Order::load($orderId);

        if ($order) {
            // Получаем данные о заказе
            $orderData = self::getOrderData($order);
            // Отправляем данные на вебхук
            self::sendDataToWebhook($orderData);
        } else {
            self::logError("Заказ с ID $orderId не найден.");
        }
    }

    protected static function getOrderData(Order $order)
    {
        $first_current_location = htmlspecialchars($_COOKIE["first_current_location"]);
        
        $orderData = [
            "id" => $order->getId(),
            "order_changes" => [
                "value_is" => self::getStatusName($order->getField("STATUS_ID")),
                "status_id" => $order->getField("STATUS_ID"),
            ],
            "price" => $order->getPrice(),
            "currency" => $order->getCurrency(),
            "userId" => $order->getUserId(),
            "fields_values" => self::getOrderProperties($order),
            "order_lines" => self::getOrderItems($order),
            "client" => self::getClientData($order->getUserId()), // Добавляем данные клиента
            "payment" => self::getPaymentData($order), // Данные о платежной системе
            "delivery" => self::getDeliveryData($order), // Данные о способе доставки
            "discount" => $order->getDiscountPrice(),
            "total_price" => $order->getPrice(),
            "items_price" => floatval($order->getPrice()) - floatval($order->getDeliveryPrice()),
            "number" => $order->GetField('ACCOUNT_NUMBER'),
            "delivery_title" => self::getDeliveryName($order->getDeliverySystemId()[0]),
            "delivery_price" => $order->getDeliveryPrice(),
            "full_delivery_price" => $order->getDeliveryPrice(),
            "payment_title" => self::getPaymentSystemName($order->getPaymentSystemId()[0]),
            "first_current_location" => $first_current_location
        ];

        return $orderData;
    }

    protected static function getOrderProperties(Order $order)
    {
        $properties = [];
        foreach ($order->getPropertyCollection() as $property) {
            $properties[$property->getField("CODE")] = $property->getValue();
        }
        return $properties;
    }

    protected static function getOrderItems(Order $order)
    {
        $items = [];
        foreach ($order->getBasket() as $item) {
            $items[] = [
                "id" => $item->getId(),
                "order_id" => $order->getId(),
                "title" => $item->getField("NAME"),
                "quantity" => $item->getQuantity(),
                "sale_price" => $item->getPrice(),
            ];
        }
        return $items;
    }
    
    protected static function getClientData($userId)
    {
        // Получаем данные клиента (пользователя)
        if ($userId > 0) {
            $user = UserTable::getById($userId)->fetch();
            if ($user) {
                return [
                    "id" => $user["ID"],
                    "name" => $user["NAME"],
                    "surname" => $user["LAST_NAME"],
                    "email" => $user["EMAIL"],
                    "phone" => $user["PERSONAL_PHONE"],
                ];
            }
        }
        return null;
    }
    
    protected static function getPaymentData(Order $order)
    {
        // Получаем данные о платежной системе
        $paymentData = [];
        $paymentCollection = $order->getPaymentCollection();
        foreach ($paymentCollection as $payment) {
            $paymentData[] = [
                "id" => $payment->getField("ID"),
                "paymentSystemId" => $payment->getField("PAY_SYSTEM_ID"),
                "paymentSystemName" => self::getPaymentSystemName($payment->getField("PAY_SYSTEM_ID")),
                "sum" => $payment->getField("SUM"),
                "currency" => $payment->getField("CURRENCY"),
            ];
        }
        return $paymentData;
    }

    protected static function getPaymentSystemName($paymentSystemId)
    {
        // Получаем название платежной системы по ID
        $paymentSystem = \Bitrix\Sale\PaySystem\Manager::getById($paymentSystemId);
        return $paymentSystem["NAME"] ?? "Неизвестная платежная система";
    }

    protected static function getDeliveryData(Order $order)
    {
        // Получаем данные о способе доставки
        $deliveryData = [];
        $shipmentCollection = $order->getShipmentCollection();
        foreach ($shipmentCollection as $shipment) {
            if (!$shipment->isSystem()) {
                $deliveryData[] = [
                    "id" => $shipment->getField("ID"),
                    "deliveryId" => $shipment->getField("DELIVERY_ID"),
                    "deliveryName" => self::getDeliveryName($shipment->getField("DELIVERY_ID")),
                    "price" => $shipment->getField("PRICE_DELIVERY"),
                    "currency" => $shipment->getField("CURRENCY"),
                ];
            }
        }
        return $deliveryData;
    }

    protected static function getDeliveryName($deliveryId)
    {
        // Получаем название способа доставки по ID
        $deliveryService = \Bitrix\Sale\Delivery\Services\Manager::getById($deliveryId);
        return $deliveryService["NAME"] ?? "Неизвестный способ доставки";
    }
    
    protected static function getStatusName($statusId)
    {
        // Получаем массив всех статусов с их названиями
        $statuses = OrderStatus::getAllStatusesNames();

        // Проверяем, существует ли статус с таким ID
        if (isset($statuses[$statusId])) {
            $statusName = $statuses[$statusId];
            return $statusName;
        } else {
            return "Неизвестный статус";
        }
    }

    protected static function sendDataToWebhook(array $data)
    {
        $moduleId = "winwinland.connect";
        $currentToken = Option::get($moduleId, "currentToken");
        $currentKey = Option::get($moduleId, "currentKey");
        
        if(strlen($currentToken) > 0){
            $hashToken = md5($data['id'].$data['userId'].$currentToken);
        }else{
            return false;
        }
        
        if(strlen($currentKey) > 0){
            $hashKey = md5($currentKey);
        }else{
            return false;
        }
        
        $webhookUrl = "https://for16.ru/d/{$hashKey}/bitrix_webhook.php?$hashToken";
        $webhookUrl = "https://for16.ru/d/{$currentKey}/bitrix_webhook.php?$hashToken";

        $httpClient = new HttpClient();
        //$httpClient->setHeader("Content-Type", "application/json");
        $response = $httpClient->post($webhookUrl, http_build_query($data));

        if ($httpClient->getStatus() != 200) {
            self::logError("Ошибка отправки данных на вебхук. $webhookUrl HTTP-код: " . $httpClient->getStatus());
        }
    }

    protected static function logError(string $message)
    {
        \Bitrix\Main\Diag\Debug::writeToFile($message, "", "wwl_connect.log");
    }
}
