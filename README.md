iDEAL library
=============

This library implements the iDEAL protocol and can be used to perform iDEAL transactions with all major iDEAL acquiring banks.
The following acquiring banks are supported:
* ABN AMRO Bank
* BNP Paribas Fortis
* Rabobank
* Deutsche Bank
* ING
* PPRO Financial Limited

This library is compatible with PHP 5.4+ and PHP 7.0

## Installation

To install cmpayments/ideal just require it with composer:
```
# composer require cmpayments/ideal
```

## Usage examples

Initialize the library and provide configuration options for the connection:
```php
<?php
require __DIR__ . '/../vendor/autoload.php';

$ideal = new CMPayments\IDeal\IDeal('https://ideal.acquirer.nl/ideal/iDEALv3');

$ideal->setOptions([
    'acquirerCertificate' => 'acq_cert.pem',
    'merchantCertificate' => 'mer_cert.pem',
    'merchantPrivateKey'  => 'mer_cert.key',
    'password'            => 'private_key_password',
    'merchantId'          => '002099999',
    'merchantSubId'       => 1
]);
```

Retrieve a list of all issuing banks:
```php
<?php

$request = $ideal->createDirectoryRequest();
$response = $request->send();
$issuers = $response->getAllIssuers();
```

Start an iDEAL transaction:
```php
<?php

$transactionRequest = $ideal->createTransactionRequest('INGBNL2A', 'http://yourwebsite.nl/returnpath', 'purchaseId', 123456, 'Description');

try {
	$transactionResponse = $transactionRequest->send();
} catch (CMPayments\IDeal\Exception\ResponseException $e) {
	// Handle an error response here
	var_dump($e->getSuggestedAction());
	exit();
}

// Store these values in your local database:
$entranceCode = $transactionRequest->getEntranceCode();
$transactionId = $transactionResponse->getTransactionId();

// redirect the user to the bank environment
header('Location: ' . $transactionResponse->getAuthenticationUrl());
```

Validate the iDEAL transaction status on return:
```php
<?php

// retrieve and sanitize transaction id from the querystring
$transactionId = preg_replace('/[^0-9]/','',$_GET['trxid']);

// at this point the entrance code ($_GET['ec']) should be checked against
// the value that was returned upon creation of the transaction

// Request the transaction status.
$statusRequest = $ideal->createStatusRequest($transactionId);
$statusResponse = $statusRequest->send();

// Get the transaction status.
switch ($statusResponse->getStatus()) {
    case \CMPayments\IDeal\IDeal::SUCCESS:
        // consumerIBAN and consumerName are available on 'Success'.
        $consumerIban = $statusResponse->getConsumerIBAN();
        $consumerName = $statusResponse->getConsumerName();
        break;
    case \CMPayments\IDeal\IDeal::OPEN:
        // When the transaction status is still 'Open' it should be retried later.
        // According to the iDEAL implementation guidelines the next attempt should only be performed after 5 minutes.
        // In total, 5 attempts are allowed and only if a non final status is returned.
        break;
    case \CMPayments\IDeal\IDeal::FAILURE:
    case \CMPayments\IDeal\IDeal::CANCELLED:
    case \CMPayments\IDeal\IDeal::EXPIRED:
        // The transaction has failed with either a Failure, Cancelled or Expired status
        // This is the moment where the user needs to be informed and the transaction should possibly be retried.
        break;    
}

```

## Submitting bugs and feature requests

Bugs and feature request are tracked on [GitHub](https://github.com/cmpayments/ideal/issues)

## Copyright and license

The cmpayment/ideal library is copyright © [CM Payments](https://github.com/cmpayments/), interexperts and bravesheep and licensed for use under the MIT License (MIT). Please see [LICENSE](LICENSE) for more information.