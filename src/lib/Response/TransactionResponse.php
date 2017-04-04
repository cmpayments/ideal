<?php

namespace CMPayments\IDeal\Response;

use DateTime;

/**
 * Class TransactionResponse
 * @package CMPayments\IDeal\Response
 */
class TransactionResponse extends Response
{
    /**
     * Get acquirer ID
     *
     * @return string Acquirer ID
     */
    public function getAcquirerId()
    {
        return $this->singleValue('//i:Acquirer/i:acquirerID');
    }

    /**
     * Get response transaction ID
     *
     * @return string Transaction ID
     */
    public function getTransactionId()
    {
        return $this->singleValue('//i:Transaction/i:transactionID');
    }

    /**
     * Get response authentication URL
     *
     * @return string Authentication URL
     */
    public function getAuthenticationUrl()
    {
        return $this->singleValue('//i:Issuer/i:issuerAuthenticationURL');
    }

    /**
     * Get response transaction date/time
     *
     * @return DateTime
     */
    public function getTransactionDateTime()
    {
        return new DateTime($this->singleValue('//i:Transaction/i:transactionCreateDateTimestamp'));
    }

    /**
     * Get response purchase ID
     *
     * @return string
     */
    public function getPurchaseId()
    {
        return $this->singleValue('//i:Issuer/i:purchaseID');
    }
}
