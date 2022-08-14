<?php


namespace App\Repositories\Eloquent;


use App\Models\PaymentTransaction;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\PaymentTransactionRepositoryInterface;

class PaymentTransactionRepository extends BaseRepository implements PaymentTransactionRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(PaymentTransaction $model)
    {
        $this->model = $model;
    }

    public function findTransaction($transactionId)
    {
        return PaymentTransaction::where('transaction_id', $transactionId)->first();
    }

    public function findTransactionLatest($userId)
    {
        return PaymentTransaction::where('user', $userId)->orderBy('id', 'desc')->first();
    }
}
