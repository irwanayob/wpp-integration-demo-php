<?php

require '../../../vendor/autoload.php';



use Wirecard\PaymentSdk\Entity\AccountHolder;
use Wirecard\PaymentSdk\Exception\MalformedResponseException;
use Wirecard\PaymentSdk\Response\FailureResponse;
use Wirecard\PaymentSdk\Response\SuccessResponse;
use Wirecard\PaymentSdk\Transaction\SepaCreditTransferTransaction;

$accountHolderLastName = htmlspecialchars($_POST['accountHolderLastName']);
$accountHolderFirstName = htmlspecialchars($_POST['accountHolderFirstName']);

$transaction = new SepaCreditTransferTransaction();
$accountHolder = new AccountHolder();

$accountHolder->setLastName($accountHolderLastName);
$accountHolder->setFirstName($accountHolderFirstName);

$transaction->setAccountHolder($accountHolder);

/* use the IBAN you will receive by the notification response. Have a look at the notify.php and see how notifications
 * are handled.
 */
$transaction->setIban(DEMO_IBAN);

if (array_key_exists('parentTransactionId', $_POST)) {
    $transaction->setParentTransactionId($_POST['parentTransactionId']);
}

$service = createTransactionService(SOFORT);

try {
    $response = $service->credit($transaction);
    if ($response instanceof SuccessResponse) {
        echo 'Refund via SEPA Credit Transfer successfully completed.<br>';
        echo 'TransactionID: ' . $response->getTransactionId();
        require '../showButton.php';
    } elseif ($response instanceof FailureResponse) {
        echoFailureResponse($response);
    }
} catch (MalformedResponseException $e) {
    echo $e->getTraceAsString() . '<br>';
    echo $e->getMessage();
}
