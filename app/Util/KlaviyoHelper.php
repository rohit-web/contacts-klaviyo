<?php

namespace App\Util;

use GuzzleHttp\Client;

class KlaviyoHelper
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * get all module wise list
     */
    public function all()
    {
        return $this->endpointRequest('lists');
    }

    /**
     * get find by id
     * @param $Id get list by ID
     */
    public function findById($id)
    {
        return $this->endpointRequest('/list/' . $id);
    }

    /**
     * get find by id
     * @param $Id get list by ID
     */
    public function getRequest($url)
    {
        return $this->endpointRequest($url);
    }

    /**
     * endpoint request method,
     */
    public function endpointRequest($url)
    {
        try {
            $response = $this->client->request('GET', $url);
        } catch (\Exception $e) {
            throw new \Exception($e);
        }

        return $this->response_handler($response->getBody()->getContents());
    }

    /**
     * post request for the add members
     * @param $url (String) URL base on version wise
     * @param $data (Array) module wise array for the create a entity
     */
    private function postRequest($url, $data)
    {
        try {
            $response = $this->client->request('POST', $url . '?api_key=' . env('klaviyo_api_key'), array(
                'json' => $data,
            ), array());

        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            throw new \Exception($e->getMessage(), 500);
        }

        return $this->response_handler($response->getBody()->getContents());
    }

    /**
     * store request base on module wise
     * @param $url (String) URL base on version wise
     * @param $data (Array) module wise array for the create a entity
     */
    public function store($url, $data)
    {
        $requestData['api_key'] = env('klaviyo_api_key');
        $requestData['profiles'][] = $data;
        return $this->postRequest($url, $requestData);
    }

    /**
     * response decoded into array
     * @param $response (String) request response
     */
    public function response_handler($response)
    {
        if ($response) {
            return json_decode($response);
        }

        return [];
    }
}
