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

    $existingContacts = $bitrix->findContactByPhoneOrEmail($phone, $email);

    $contactId = null;
    $phoneMatch = false;
    $emailMatch = false;

    foreach ($existingContacts as $contact) {
        $contactPhones = array_map(function ($p) {
            return $p['VALUE'];
        }, $contact['PHONE'] ?? []);
        $contactEmails = array_map(function ($e) {
            return $e['VALUE'];
        }, $contact['EMAIL'] ?? []);

        if (in_array($phone, $contactPhones)) {
            $phoneMatch = true;
        }

        if (in_array($email, $contactEmails)) {
            $emailMatch = true;
        }

        if ($phoneMatch || $emailMatch) {
            $contactId = $contact['ID'];
            break;
        }
    }

    if (!$contactId) {
        $contact = new Contact($name, $lastName, $phone, $email, $type);
        $contactId = $bitrix->createContact($contact->toArray());
        if (!$contactId) {
            throw new \Exception("Не удалось создать контакт.");
        }
    }

    $deal = new Deal("Запрос от $name $lastName", "SALE", "WEB_FORM", "NEW");

    $customFields = [
        'UF_CRM_TYPE_OF_REQUEST' => 'UF_CRM_1730114130',
        'UF_CRM_MESSAGE' => 'UF_CRM_1730115445',
    ];

    $typeOptions = [
        'Консультация' => '257',
        'Поддержка' => '259',
        'Обратная связь' => '261',
    ];

    $typeId = $typeOptions[$type] ?? null;
    if (!$typeId) {
        throw new \Exception("Некорректный тип запроса.");
    }

    $dealFields = array_merge($deal->toArray(), [
        'CONTACT_ID' => $contactId,
        $customFields['UF_CRM_TYPE_OF_REQUEST'] => $typeId,
        $customFields['UF_CRM_MESSAGE'] => $message,
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