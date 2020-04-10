<?php

namespace BrandStudio\Bonus;

use GuzzleHttp\Client;

class BonusService
{

    protected $config;
    protected $client;

    public function __construct(array $config)
    {
        $this->client = new Client([
            'headers' => [
                'Accept' => 'application/json',
            ]
        ]);
        $this->config = $config;
    }

    public function getClient(array $params)
    {
        return $this->sendRequest('client/show', $params);
    }

    public function getClients(array $params = [])
    {
        return $this->sendRequest('client', $params);
    }

    public function searchClient(array $params = [])
    {
        return $this->sendRequest('client/search', $params);
    }

    public function createClient(array $params = [])
    {
        return $this->sendRequest('client', $params, 'POST');
    }

    public function updateClient(array $params = [])
    {
        return $this->sendRequest('client', $params, 'PUT');
    }


    public function getBonuses(array $params = [])
    {
        return $this->sendRequest('bonus', $params);
    }

    public function getBonus(array $params = [])
    {
        return $this->sendRequest('bonus/show', $params);
    }

    public function createBonus(array $params = [])
    {
        return $this->sendRequest('bonus', $params, 'POST');
    }

    public function verifyBonus(array $params = [])
    {
        return $this->sendRequest('bonus', $params, 'PUT');
    }

    public function cancelBonus(array $params = [])
    {
        return $this->sendRequest('bonus/cancel', $params, 'PUT');
    }


    protected function sendRequest($url, array $data = [], $method = 'GET')
    {
        $data['_method'] = $method;
        $data['_token'] = $this->config['token'];
        if (isset($data['bonus'])) {
            $data['bonus']['type'] = $this->config['bonus_type'];
        }
        if (function_exists('backpack_user') && backpack_user()) {
            $data['manager_id'] = backpack_user()->id;
            $data['manager'] = backpack_user()->full_name;
        }

        try {
            $response = $this->client->post($this->config['bonus_url'].'/'.$url, [
                'form_params' => $data,
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            \Log::error($e->getResponse()->getBody()->getContents());
        }
    }


}
