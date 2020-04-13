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

    public function all()
    {
        return $this->endpointRequest('lists');
    }

    public function findById($id)
    {
        return $this->endpointRequest('/list/' . $id);
    }

    public function endpointRequest($url)
    {
        try {
            $response = $this->client->request('GET', $url);
        } catch (\Exception $e) {
            return [];
        }

        return $this->response_handler($response->getBody()->getContents());
    }

    /**
     * post request for the add members
     */
    public function postRequest($url, $data)
    {
        // \Log::error(json_encode($data));
        // \Log::error($url . '?api_key=' . env('klaviyo_api_key'));
        try {
            $response = $this->client->request('POST', $url . '?api_key=' . env('klaviyo_api_key'),array(
                'json' => $data
            ),array());

        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            throw new \Exception($e->getMessage(), 403);
        }

        return $this->response_handler($response->getBody()->getContents());
    }

    public function store($url, $data)
    {
        $requestData['api_key'] = env('klaviyo_api_key');
        $requestData['profiles'][] = $data;       
        return $this->postRequest($url, $requestData);
    }


    public function response_handler($response)
    {
        if ($response) {
            return json_decode($response);
        }

        return [];
    }
}