<?php

namespace CMPayments\IDeal\Request;

use CMPayments\IDeal\IDeal;

/**
 * Class StatusRequest
 * @package CMPayments\IDeal\Request
 */
class StatusRequest extends Request
{
    /**
     * Class constant
     */
    const ROOT_NAME = 'AcquirerStatusReq';

    /**
     * @var int Transaction ID
     */
    private $transactionId;

    /**
     * StatusRequest constructor.
     *
     * @param IDeal $ideal
     */
    public function __construct(IDeal $ideal)
    {
        parent::__construct($ideal, self::ROOT_NAME);
    }

    /**
     * Set transaction ID
     *
     * @param int $id Transaction ID
     */
    public function setTransactionId($id)
    {
        $this->transactionId = $id;
    }

    /**
     * Presign status request
     */
    protected function preSign()
    {
        $transaction = $this->createElement('Transaction');
        $transaction->appendChild($this->createElement('transactionID', $this->transactionId));
        $this->root->appendChild($transaction);
    }

    /**
     * Get transaction ID
     *
     * @return int Transaction ID
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }
}
