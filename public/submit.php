<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Src\BitrixClient;
use Src\Contact;
use Src\Deal;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$config = require __DIR__ . '/../config/config.php';

$name = trim($_POST['name'] ?? '');
$lastName = trim($_POST['last_name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');
$type = trim($_POST['type'] ?? '');
$message = trim($_POST['message'] ?? '');

if (empty($name) || empty($phone) || empty($email) || empty($type) || empty($message)) {
    die('Пожалуйста, заполните все обязательные поля.');
}

try {
    $bitrix = new BitrixClient($config['bitrix']['webhook_url']);

    $contactData = $bitrix->findContact($phone);

    if (!$contactData) {
        $contactData = $bitrix->findContact($email);
    }

    if ($contactData && isset($contactData['ID'])) {
        $contactId = $contactData['ID'];
    } else {
        $contact = new Contact($name, $lastName, $phone, $email, $type);
        $contactId = $bitrix->createContact($contact->toArray());

        if (!$contactId) {
            throw new \Exception("Не удалось создать контакт.");
        }
    }

    $deal = new Deal("Запрос от $name $lastName", "SALE", "WEB_FORM", "NEW");
    $dealFields = array_merge($deal->toArray(), [
        'CONTACT_ID' => $contactId
    ]);
    $dealId = $bitrix->createDeal($dealFields);

    if (!$dealId) {
        throw new \Exception("Не удалось создать сделку.");
    }

    echo "Ваша заявка успешно отправлена! Мы свяжемся с вами в ближайшее время.";
} catch (Exception $e) {
    error_log($e->getMessage());
    echo "Произошла ошибка при обработке вашей заявки. Пожалуйста, попробуйте позже.";
}
