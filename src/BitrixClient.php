<?php
// src/BitrixClient.php

namespace Src;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class BitrixClient
{
    private $client;
    private $webhookUrl;

    public function __construct(string $webhookUrl)
    {
        // Убедитесь, что URL вебхука заканчивается на "/"
        $this->webhookUrl = rtrim($webhookUrl, '/') . '/';
        $this->client = new Client([
            'base_uri' => $this->webhookUrl,
            'timeout' => 10.0,
            'verify' => false,
        ]);
    }

    /**
     * Выполняет запрос к API Битрикс24.
     */
    private function request(string $method, array $params = [])
    {
        try {
            // Формируем полный путь с методом
            $url = $method;

            $response = $this->client->post($url, [
                'form_params' => $params
            ]);

            $body = json_decode($response->getBody(), true);

            // Логирование ответа
            file_put_contents(__DIR__ . '/../logs/bitrix.log', date('Y-m-d H:i:s') . " - $method - " . print_r($body, true) . "\n", FILE_APPEND);

            if (isset($body['error'])) {
                throw new \Exception("Bitrix API Error: " . $body['error_description']);
            }

            return $body['result'];
        } catch (GuzzleException $e) {
            throw new \Exception("HTTP Request failed: " . $e->getMessage());
        }
    }

    /**
     * Поиск контакта по телефону или email.
     */
    public function findContact(string $value)
    {
        return $this->request('crm.contact.list', [
            'filter' => [
                ['=PHONE' => $value],
                ['=EMAIL' => $value],
            ],
            'select' => ['ID', 'NAME', 'LAST_NAME', 'PHONE', 'EMAIL'],
            'limit' => 1,
        ]);
    }

    /**
     * Создание нового контакта.
     */
    public function createContact(array $fields)
    {
        return $this->request('crm.contact.add', [
            'fields' => $fields,
            'params' => ['REGISTER_SONET_EVENT' => 'Y']
        ]);
    }

    /**
     * Создание новой сделки.
     */
    public function createDeal(array $fields)
    {
        return $this->request('crm.deal.add', [
            'fields' => $fields,
            'params' => ['REGISTER_SONET_EVENT' => 'Y']
        ]);
    }
}
