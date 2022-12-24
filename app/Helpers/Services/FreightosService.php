<?php

namespace App\Helpers\Services;

use App\Repositories\Eloquent\AffiliateApiCallRepository;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class FreightosService
{
    const DATE_FORMAT = 'Y-m-d\TH:i:s\Z';

    const DOCUMENT_TYPE_7501 = 'US_CUSTOMS_INVOICE';
    const DOCUMENT_TYPE_ACE = 'ACE';

    public static $mappedDocumentTypes = [
        'ISF Certificate' => self::DOCUMENT_TYPE_7501,
        'ACE' => self::DOCUMENT_TYPE_ACE,
    ];

    const GET = 'GET';
    const POST = 'POST';

    protected AffiliateApiCallRepository $aacRepo;

    protected $client;

    public function __construct(
        AffiliateApiCallRepository $aacRepo
    ) {
        $freightosConfig = config('freightos');
        $this->client = new Client([
            'base_uri' => $freightosConfig['apiEndpoint'],
            RequestOptions::HEADERS => [
                'Content-Type' => 'application/json',
                'appID' => $freightosConfig['appID']
            ]
        ]);
        $this->aacRepo = $aacRepo;
    }

    public function request($method, $url, $data = [], $headers = [])
    {
        return $this->client->request($method, $url, [
            RequestOptions::JSON => $data,
            RequestOptions::HEADERS => $headers,
        ]);
    }

    public function get($url, $data = [], $headers = [])
    {
        return $this->request(self::GET, $url, $data, $headers);
    }

    public function post($url, $data = [], $headers = [])
    {
        return $this->request(self::POST, $url, $data, $headers);
    }

    public function uploadShipmentAttachment($shipmentNumber, $documentType, $guid, UploadedFile $file)
    {
        $freightosConfig = config('freioghtos');
        $fileName = $file->getClientOriginalName();
        $fileContent = base64_encode($file->getContent());
        $contentLength = strlen($fileContent);
        $ip = request()->ip();
        $uri = sprintf("/marketplace/shipment/%s/attachments", $shipmentNumber);
        $params = [
            "messageHeader" => [
                "messageID" => $guid,
                "conversationID" => '#' . $shipmentNumber,
            ],
            "businessInfo" => [
                "serviceName" => "Booking",
                "serviceMethod" => "UPDATE_DOCUMENTS",
                "messageDateTime" => (new Carbon)->format(self::DATE_FORMAT),
                "parties" => [
                    [
                        "partyTypeCode" => "CB",
                        "ID" => $freightosConfig['partyIdClearit'],
                    ],
                ],
            ],
            "documentIdentifier" => [
                [
                    "documentType" => $documentType,
                    "documentID" => $guid,
                    "documentName" => $fileName,
                    "documentContent" => [
                        "contentLength" => $contentLength,
                        "documentEncoding" => "base64",
                        "content" => $fileContent,
                    ],
                ]
            ],
        ];
        try {
            $res = $this->post($uri, $params);
            $responseStatusCode = $res->getStatusCode();
            $responseBody = $res->getBody()->getContents();
            $json = json_encode($responseBody);
        } catch (\Exception $e) {
            // TODO: save proper error response and status code.
            $responseBody = $e->getMessage();
            $responseStatusCode = 400;
            $json = false;
        }
        $this->aacRepo->create([
            'guid' => Str::upper(Str::uuid()),
            'requestUri' => $freightosConfig['apiEndpoint'] . $uri,
            'requestMethod' => 'POST',
            'requestBody' => json_encode($params),
            'responseBody' => $responseBody,
            'responseStatusCode' => $responseStatusCode,
            'ipaddress' => $ip,
            'token' => $freightosConfig['appID']
        ]);
        return $json;
    }
}
