<?php

namespace App\Services;

use Exception;
use Log;

use ReceiptValidator\iTunes\Validator as iTunesValidator;
use ReceiptValidator\GooglePlay\Validator as PlayValidator;
use ReceiptValidator\iTunes\AbstractResponse;

class InAppPaymentService
{

    public function __construct()
    {

    }

    /**
     * Connect to google play and check info purcharse
     * @param string $productId
     * @param string $purchaseToken
     * @return false|object
     */
    public function androidCheckInfo($productId, $purchaseToken)
    {
        $googleClient = new \Google_Client();
        $googleClient->setScopes([\Google_Service_AndroidPublisher::ANDROIDPUBLISHER]);
        $googleClient->setApplicationName('診察ノオト');
        $pathToServiceAccountJsonFile = base_path() . SHARED_ANDROID;
        $googleClient->setAuthConfig($pathToServiceAccountJsonFile);

        $googleAndroidPublisher = new \Google_Service_AndroidPublisher($googleClient);
        $validator = new \ReceiptValidator\GooglePlay\Validator($googleAndroidPublisher);
        try {
            $response = $validator->setPackageName(env('PACKAGE_NAME'))
                ->setProductId($productId)
                ->setPurchaseToken($purchaseToken)
                ->validateSubscription();
            return $response;
        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    /**
     * Connect to appstore play and check info purcharse
     * @param string $purchaseToken
     * @return false|object
     */

    public function itunesCheck($purchaseToken)
    {

        $validator = new iTunesValidator(iTunesValidator::ENDPOINT_SANDBOX);
        $receiptBase64Data = $purchaseToken;
        $response = null;
        try {
            $response = $validator
                ->setSharedSecret(SHARED_IOS)
                ->setReceiptData($receiptBase64Data)
                ->validate();
        } catch (\Exception $e) {
            echo 'got error = ' . $e->getMessage() . PHP_EOL;
            echo $e->getTraceAsString() . PHP_EOL;
            exit;
        }

        if ($response instanceof AbstractResponse && $response->isValid()) {
            return $response;

        } else {
            echo 'Receipt is not valid.' . PHP_EOL;
            echo 'Receipt result code = ' . $response->getResultCode() . PHP_EOL;
            return false;
        }
    }
}
