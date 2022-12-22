<?php

namespace App\Services;

use App\Models\Business;
use App\Models\BusinessUser;
use App\Models\PayItem;
use App\Utils\Apis;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncRoutine
{
    private Apis $api;

    public function __construct()
    {
        $this->api = new Apis();
    }

    public function syncPayItems(Business $business): void
    {
        try {
            DB::transaction(function () use ($business) {
                $page = 1;
                $isLastPage = false;
                $foundPayItems = [];

                while (!$isLastPage) {
                    $apiResponse = $this->api->clairPayItemSync($business->external_id, $page);
                    //$apiResponse = $this->api->testFakeData($business->external_id, $page);
                    $businessUser = new BusinessUser();
                    foreach ($apiResponse['payItems'] as $apiPayItem) {
                        $userContext = $businessUser->findByUserExternalId($apiPayItem['employeeId']);
                        // Only continue if user context was found
                        if (!is_null($userContext)) {
                            $foundPayItems[] = $apiPayItem['id'];
                            $business = Business::find($userContext->business_id);
                            $deductionPercentage = is_null($business) || is_null($business->deduction)
                                ? env('DEFAULT_BUSINESS_DEDUCTION') : $business->deduction;
                            $amount = floatval($apiPayItem['hoursWorked']) * floatval($apiPayItem['payRate'])
                                * $deductionPercentage;
                            $amount = round($amount, 2); // default PHP_ROUND_HALF_UP

                            $data = [
                                'amount' => $amount,
                                'worked_hours' => $apiPayItem['hoursWorked'],
                                'pay_rate' => $apiPayItem['payRate'],
                                'pay_date' => $apiPayItem['date'],
                                'user_id' => $userContext->user_id,
                                'business_id' => $userContext->business_id
                            ];

                            $payItem = new PayItem();
                            $existingPayItem = $payItem->findPayItem(
                                $apiPayItem['id'],
                                $userContext->user_id,
                                $userContext->business_id
                            );

                            if (is_null($existingPayItem)) {
                                // new pay item
                                PayItem::create($data);
                            } else {
                                // pay item already exists
                                $existingPayItem->update($data);
                            }
                        }
                    }
                    $isLastPage = $apiResponse['isLastPage'];
                    $page += 1;
                }
                $payItemsToDelete = new PayItem();
                $payItemsToDelete->deleteOldPayItems($foundPayItems);
                Log::debug("syncPayItems finished successfully!");
            });

        } catch (Exception $e) {
            Log::error("Error executing the syncPayItems");
            Log::error($e);
        }
    }
}
