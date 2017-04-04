<?php

namespace CMPayments\IDeal\Response;

use DateTime;

/**
 * Class StatusResponse
 * @package CMPayments\IDeal\Response
 */
class StatusResponse extends Response
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
     * @return string
     */
    public function getTransactionId()
    {
        return $this->singleValue('//i:Transaction/i:transactionID');
    }

    /**
     * Get reponse status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->singleValue('//i:Transaction/i:status');
    }

    /**
     * Get response date/time
     *
     * @return DateTime
     */
    public function getStatusDateTime()
    {
        return new DateTime($this->singleValue('//i:Transaction/i:statusDateTimestamp'));
    }

    /**
     * Get response consumer name
     *
     * @return string
     */
    public function getConsumerName()
    {
        return $this->singleValue('//i:Transaction/i:consumerName');
    }

    /**
     * Get response consumer IBAN
     *
     * @return string
     */
    public function getConsumerIBAN()
    {
        return $this->singleValue('//i:Transaction/i:consumerIBAN');
    }

    /**
     * Get response BIC code
     *
     * @return string
     */
    public function getConsumerBIC()
    {
        return $this->singleValue('//i:Transaction/i:consumerBIC');
    }

    /**
     * Get response internal amount in decimal notation
     *
     * @return string
     */
    public function getInternalAmount()
    {
        return $this->singleValue('//i:Transaction/i:amount');
    }

    /**
     * Get response amount in eurocents
     *
     * @return int
     */
    public function getAmount()
    {
        $val = $this->getInternalAmount();
        $parts = explode('.', $val);
        if (count($parts) < 2) {
            return (int) $parts[0] * 100;
        }

        return (int)($parts[0] * 100) + (int)$parts[1];
    }

    /**
     * Get currency code
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->singleValue('//i:Transaction/i:currency');
    }
}
