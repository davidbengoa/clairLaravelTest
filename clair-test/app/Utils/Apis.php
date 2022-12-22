<?php
namespace App\Utils;

use Exception;
use Illuminate\Support\Facades\Log;

class Apis {
    private string $url = 'https://some-partner-website.com';

    /**
     * @throws Exception
     */
    public function clairPayItemSync($businessExternalId, $pageNumber) {
        $url = $this->url . '/clair-pay-item-sync/' . $businessExternalId . '?page=' . $pageNumber;
        $ch = curl_init($url);
        $headers = [
            'Content-Type:application/json',
            'X-API-KEY:CLAIR-ABC-123'
        ];
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            Log::error('Curl Error: ' . curl_errno($ch) . ' - ' . curl_error($ch));
            return null;
        }
        $curlInfo = curl_getinfo($ch);
        $httpCode = $curlInfo['http_code'];
        curl_close($ch);
        if ($httpCode == 401) {
            Log::alert('Error - invalid X-API-KEY provided');
            throw new Exception('API - 401 received');
        } elseif ($httpCode == 404) {
            Log::critical('Error - business not found');
            throw new Exception('API - 404 received');
        }

        return json_decode($response, true);
    }

}
