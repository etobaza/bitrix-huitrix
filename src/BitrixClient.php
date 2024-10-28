<?php
namespace Src;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class BitrixClient
{
    private $client;
    private $webhookUrl;

    public function __construct(string $webhookUrl)
    {
        $this->webhookUrl = rtrim($webhookUrl, '/') . '/';
        $this->client = new Client([
            'base_uri' => $this->webhookUrl,
            'timeout' => 10.0,
            'verify' => false,
        ]);
    }

    private function request(string $method, array $params = [], string $httpMethod = 'POST')
    {
        try {
            $options = [
                'verify' => false,
                'http_errors' => false,
            ];

            if (strtoupper($httpMethod) === 'GET') {
                $options['query'] = $params;
                $response = $this->client->get($method, $options);
            } else {
                $options['json'] = $params;
                $response = $this->client->post($method, $options);
            }

            $body = json_decode($response->getBody(), true);

            if (isset($body['error'])) {
                throw new \Exception("Bitrix API Error: " . $body['error_description']);
            }

            return $body['result'];
        } catch (GuzzleException $e) {
            throw new \Exception("HTTP Request failed: " . $e->getMessage());
        }
    }

    public function findContactByPhoneOrEmail(string $phone, string $email)
    {
        $filter = [
            'LOGIC' => 'OR',
            ['=PHONE' => $phone],
            ['=EMAIL' => $email],
        ];

        $params = [
            'filter' => $filter,
            'select' => ['ID', 'NAME', 'LAST_NAME', 'PHONE', 'EMAIL'],
        ];

        return $this->request('crm.contact.list', $params, 'GET');
    }

    public function createContact(array $fields)
    {
        return $this->request('crm.contact.add', [
            'fields' => $fields,
            'params' => ['REGISTER_SONET_EVENT' => 'Y']
        ]);
    }

    public function createDeal(array $fields)
    {
        return $this->request('crm.deal.add', [
            'fields' => $fields,
            'params' => ['REGISTER_SONET_EVENT' => 'Y']
        ]);
    }
}