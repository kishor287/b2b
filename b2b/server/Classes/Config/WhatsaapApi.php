<?php
namespace Panel\Server\Classes\Config;

class WhatsappApi
{
    private $apiKey;
    private $apiSecret;
    private $baseUrl;

    public function __construct(string $apiKey = null, string $apiSecret = null)
    {
        $this->apiKey = $apiKey ?: '63e22876583a7318bbc19e96';
        $this->apiSecret = $apiSecret ?: 'edab7b1e312543b894d708f5a9526ef3';
        $this->baseUrl = 'https://server.gallabox.com/devapi/messages/whatsapp';
    }

    public function sendWhatsAppMessage(array $recipient, string $templateName, array $bodyValues = [])
    {
        $data = array(
            "channelId" => '63e206b7fce0e80b431d639d',
            "channelType" => "whatsapp",
            "recipient" => array(
                "name" => $recipient['name'],
                "phone" => "91" . $recipient['phone']
            ),
            "whatsapp" => array(
                "type" => "template",
                "template" => array(
                    "templateName" => $templateName,
                    "bodyValues" => $bodyValues
                )
            )
        );

        $curl = curl_init();
        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => $this->baseUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => array(
                    'apiKey: ' . $this->apiKey,
                    'apiSecret: ' . $this->apiSecret,
                    'Content-Type: application/json',
                ),
            )
        );

        $response = curl_exec($curl);
        if ($response === false) {
            // Handle error
            $error = curl_error($curl);
            curl_close($curl);
            throw new \Exception('cURL error: ' . $error);
        }

        curl_close($curl);
        error_log(" {{$templateName}} whatsaap message log: ".$response);
        return $response;
    }
}