<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Repositories\Eloquent\PaymentTransactionRepository;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\InAppPaymentService;
use Faker\Provider\ar_EG\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends BaseController
{
    protected $inAppPaymentService;
    private $paymentTransactionRepository;
    protected $userRepository;

    public function __construct(PaymentTransactionRepository $paymentTransactionRepository,UserRepositoryInterface $userRepository)
    {
        $this->inAppPaymentService = app(InAppPaymentService::class);
        $this->paymentTransactionRepository = $paymentTransactionRepository;
        $this->userRepository = $userRepository;
    }

    public function payment(Request $request)
    {
        DB::beginTransaction();
        try {
            $iTunes = false;
            $android = false;
            $data_update = array();

            $data = [
                'user' => Auth::user()->id,
                'type' => $request->payment_type,
                'purchase_code' => $request->purchase_code,
                'store_product_id' => $request->store_product_id,
            ];
            if ($request->payment_type == PAYMENT_IOS) {
                $iTunes = $this->inAppPaymentService->itunesCheck($request->purchase_code);
                foreach ($iTunes->getLatestReceiptInfo() as $key => $info) {
                    if ($info->getProductId() == $request->store_product_id) {
                        $data_update = ['date_payment_start' => $info->getPurchaseDate()->toIso8601String(),
                            'date_payment_end' => $info->getExpiresDate()->toIso8601String(),
                            'transaction_id' => $info->getTransactionId()
                        ];
                        break;
                    }
                }
            }
            if ($request->payment_type == PAYMENT_ANDROID) {
                $android = $this->inAppPaymentService->androidCheckInfo($request->store_product_id, $request->purchase_code);
                $data_update = ['date_payment_start' => date('Y-m-d h:m:s', $android->getStartTimeMillis() / 1000),
                    'date_payment_end' => date('Y-m-d h:m:s', $android->getExpiryTimeMillis() / 1000)
                ];
            }
            $data = array_merge($data_update, $data);

            if ($android || $iTunes) {
                if(date(DATE_ISO8601, strtotime('now')) < $data['date_payment_end']) {
                    $transaction = $this->paymentTransactionRepository->findTransaction($data['transaction_id']);
                    if ($transaction) {
                        return $this->sendResponse($transaction, 'Payment successfully.');
                    } else {
                        $paymentTransaction = $this->paymentTransactionRepository->create($data);
                        if ($paymentTransaction) {
                            $this->userRepository->update(Auth::user()->id, ['plan' => VIP_PLAN_VALUE]);
                            DB::commit();
                            return $this->sendResponse($paymentTransaction, 'Payment successfully.');
                        }                    }
                } else  {
                    return $this->sendError("Receipt is expired", 500);
                }
            }

            return $this->sendError("Payment failed!", 500);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    public function getPaymentInfo()
    {
        try {
            $userId = Auth::id();
            $paymentInfo = $this->paymentTransactionRepository->findTransactionLatest($userId);
            if ($paymentInfo) {
                $paymentInfo->plan = $this->userRepository->findById($userId)->plan;
            } else {
                $paymentInfo = [];
            }

            return $this->sendResponse($paymentInfo, 'Get payment info successfully.');
        } catch (\Exception $e) {
            return $this->sendError("Something when wrong!", 500);
        }

    }
}
