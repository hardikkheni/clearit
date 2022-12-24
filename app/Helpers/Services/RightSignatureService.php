<?php

namespace App\Helpers\Services;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class RightSignatureService
{
    protected $client;

    const GET = 'GET';
    const POST = 'POST';

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.rightsignature.com',
            RequestOptions::HEADERS => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode(config('rightsignature.rs4Token'))
            ]
        ]);
    }

    public function request($method, $url, $data = [], $headers = [])
    {
        $req = $this->client->request($method, '/public/v1' . $url, [
            RequestOptions::JSON => $data,
            RequestOptions::HEADERS => $headers,
        ]);
        dd($req->getBody()->getContents());
        return json_decode($req->getBody()->getContents(), true);
    }

    public function get($url, $data = [], $headers = [])
    {
        return $this->request(self::GET, $url, $data, $headers);
    }

    public function post($url, $data = [], $headers = [])
    {
        return $this->request(self::POST, $url, $data, $headers);
    }

    public function prepareDocument($templateId)
    {
        // return $this->post("/public/v1/reusable_templates/$templateId/prepare_document");
        return $this->get("/me");
    }

    public function getEmbeddedLink($templateId, $username, $redirectUrl, $clientName = 'Client', $prefill = [], $height = 700)
    {
        $result = $this->prepareDocument($templateId);
        dd($result);
    }
}
