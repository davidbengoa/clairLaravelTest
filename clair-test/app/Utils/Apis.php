<?php
namespace App\Utils;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Apis {
    private string $url = 'https://some-partner-website.com';

    /**
     * @throws Exception
     */
    public function clairPayItemSync($businessExternalId, $pageNumber) {
        $url = $this->url . '/clair-pay-item-sync/' . $businessExternalId . '?page=' . $pageNumber;
        $headers = [
            'Content-Type:application/json',
            'X-API-KEY:CLAIR-ABC-123'
        ];
        $response = Http::withHeaders($headers)->get($url);
        if ($response->failed()) {
            if ($response->status() == 401) {
                Log::alert('Error - invalid X-API-KEY provided');
                throw new Exception('API - 401 received');
            } elseif ($response->status() == 404) {
                Log::critical('Error - business not found');
                throw new Exception('API - 404 received');
            }
            throw new Exception('API - Other exception');
        }
        return $response->json();
    }

    /*
    // ONLY FOR TESTING PURPOSES
    public function testFakeData($businessExternalId, $pageNumber): array {
        $items = [];
        $data = DB::select("
            select pi.external_id, bu.external_user_id, pi.worked_hours, pi.pay_rate, pi.pay_date
            from pay_items pi inner join business_users bu on pi.business_id = bu.business_id and pi.user_id = bu.user_id
            inner join businesses b on bu.business_id = b.id where b.external_id = '" . $businessExternalId . "'");
        foreach ($data as $datum) {
            $items[] = [
                'id' => $datum->external_id,
                'employeeId' => $datum->external_user_id,
                'hoursWorked' => $datum->worked_hours,
                'payRate' => $datum->pay_rate,
                'date' => $datum->pay_date,
            ];
        }
        return [
            'payItems' => $items,
            'isLastPage' => true
        ];
    }*/

}
