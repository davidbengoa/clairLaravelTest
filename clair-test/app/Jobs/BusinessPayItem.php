<?php

namespace App\Jobs;

use App\Models\Business;
use App\Models\BusinessUser;
use App\Models\PayItem;
use App\Models\User;
use App\Utils\Apis;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BusinessPayItem implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private Business $business;
    private Apis $api;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Business $business)
    {
        $this->business = $business;
        $this->api = new Apis();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            DB::transaction(function () {
                $page = 1;
                $isLastPage = false;
                $foundPayItems = [];

                while (!$isLastPage) {
                    $apiResponse = $this->api->clairPayItemSync(
                        $this->business->getAttribute('external_id'), $page);
                    $businessUser = new BusinessUser();
                    foreach ($apiResponse['payItems'] as $apiPayItem) {
                        $userContext = $businessUser->findByUserExternalId($apiPayItem['employeeId']);
                        // Only continue if user context was found
                        if (!is_null($userContext)) {
                            $foundPayItems[] = $apiPayItem['id'];
                            $business = Business::query()->find($userContext->get('business_id'));
                            $deductionPercentage = is_null($business) || is_null($business->get('deduction'))
                                ? 0.3 : $business->get('deduction');
                            $amount = floatval($apiPayItem['hoursWorked']) * floatval($apiPayItem['payRate'])
                                * $deductionPercentage;
                            $amount = round($amount, 2); // default PHP_ROUND_HALF_UP

                            $data = [
                                'amount' => $amount,
                                'worked_hours' => $apiPayItem['hoursWorked'],
                                'pay_rate' => $apiPayItem['payRate'],
                                'pay_date' => $apiPayItem['date'],
                                'external_id' => $userContext->get('external_id'),
                                'user_id' => $userContext->get('user_id'),
                                'business_id' => $userContext->get('business-id')
                            ];

                            $payItem = new PayItem();
                            $existingPayItem = $payItem->findPayItem(
                                $apiPayItem['id'],
                                $userContext->get('user_id'),
                                $userContext->get('business_id')
                            );

                            if (is_null($existingPayItem)) {
                                // new pay item
                                $toSave = $payItem->newInstance($data);
                                $toSave->save();
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
            });

        } catch (Exception $e) {
            Log::error("Error executing the job");
            Log::error($e);
        }
    }
}
