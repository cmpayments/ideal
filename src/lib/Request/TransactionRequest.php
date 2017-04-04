<?php

namespace CMPayments\IDeal\Request;

use CMPayments\IDeal\IDeal;
use DateTime;

/**
 * Class TransactionRequest
 * @package CMPayments\IDeal\Request
 */
class TransactionRequest extends Request
{
    /**
     * Class constants
     */
    const ROOT_NAME = 'AcquirerTrxReq';

    /**
     * @var int Amount in eurocents
     */
    private $amount;

    /**
     * @var string Purchase ID
     */
    private $purchaseId;

    /**
     * @var string Currency Code
     */
    private $currency;

    /**
     * @var string Language
     */
    private $language;

    /**
     * @var string Transaction description
     */
    private $description;

    /**
     * @var string Transaction entrance code
     */
    private $entranceCode;

    /**
     * @var string Merchant return URL
     */
    private $returnUrl;

    /**
     * @var string Issuing bank code
     */
    private $issuer;

    /**
     * @var \DateTime Transaction expiration time
     */
    private $expiresAt;

    /**
     * @var string Transaction expiration period
     */
    private $expirationPeriod;

    /**
     * TransactionRequest constructor.
     *
     * @param IDeal $ideal
     */
    public function __construct(IDeal $ideal)
    {
        parent::__construct($ideal, self::ROOT_NAME);
        $this->setLanguage(IDeal::DUTCH);
        $this->setCurrency(IDeal::EURO);
        $this->setExpirationPeriod('15 minutes');
    }

    /**
     * Sets the transaction amount
     *
     * @param int $cents Amount in eurocents
     */
    public function setAmount($cents)
    {
        $this->amount = $cents;
    }

    /**
     * Sets transaction purchase ID
     *
     * @param string $id Purchase ID
     */
    public function setPurchaseId($id)
    {
        $this->purchaseId = $id;
    }

    /**
     * Sets transaction currency code
     *
     * @param string $currency Currency code
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * Sets transaction language
     *
     * @param string $lang Language
     */
    public function setLanguage($lang)
    {
        $this->language = $lang;
    }

    /**
     * Sets transaction description
     *
     * @param string $description Description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Sets transaction entrance code
     *
     * @param string $code Entrance code
     */
    public function setEntranceCode($code)
    {
        $this->entranceCode = $code;
    }

    /**
     * Sets merchant return URL
     *
     * @param string $url Merchant return URL
     */
    public function setReturnUrl($url)
    {
        $this->returnUrl = $url;
    }

    /**
     * Set Issuing bank code
     *
     * @param string $id Issuer ID
     */
    public function setIssuer($id)
    {
        $this->issuer = $id;
    }

    /**
     * Presign transaction request
     */
    protected function preSign()
    {
        $this->merchant->appendChild($this->createElement('merchantReturnURL', $this->returnUrl));

        $issuer = $this->createElement('Issuer');
        $issuer->appendChild($this->createElement('issuerID', $this->issuer));
        $this->root->insertBefore($issuer, $this->merchant);

        $transaction = $this->createElement('Transaction');
        $transaction->appendChild($this->createElement('purchaseID', $this->purchaseId));
        $transaction->appendChild($this->createElement('amount', $this->getInternalAmount()));
        $transaction->appendChild($this->createElement('currency', $this->currency));
        $transaction->appendChild($this->createElement('expirationPeriod', $this->getExpirationPeriod()));
        $transaction->appendChild($this->createElement('language', $this->language));
        $transaction->appendChild($this->createElement('description', $this->description));
        $transaction->appendChild($this->createElement('entranceCode', $this->entranceCode));
        $this->root->appendChild($transaction);
    }

    /**
     * Retrieve amount in decimal notation
     *
     * @return string
     */
    public function getInternalAmount()
    {
        $cents = sprintf('%03d', $this->amount);
        $coins = substr($cents, 0, -2);
        $cents = substr($cents, -2);
        return sprintf('%s.%s', $coins, $cents);
    }

    /**
     * Get expiration period
     *
     * @return string
     */
    public function getExpirationPeriod()
    {
        return $this->expirationPeriod;
    }

    /**
     * Sets expiration period
     *
     * @param string $period
     */
    public function setExpirationPeriod($period)
    {
        $expires = new DateTime("now + {$period}");
        $now     = new DateTime('now');
        $diff    = $expires->diff($now, true);

        $stat = 'P';
        if ($diff->y > 0) {
            $stat .= $diff->y . 'Y';
        }
        if ($diff->m > 0) {
            $stat .= $diff->m . 'M';
        }
        if ($diff->d > 0) {
            $stat .= $diff->d . 'D';
        }

        $stat .= 'T';
        if ($diff->h > 0) {
            $stat .= $diff->h . 'H';
        }

        if ($diff->i > 0) {
            $stat .= $diff->i . 'M';
        }

        if ($diff->s > 0) {
            $stat .= $diff->s . 'S';
        }

        $this->expiresAt = $expires->format('Y-m-d H:i:s');
        $this->expirationPeriod = $stat;
    }

    /**
     * Get transaction language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Get transaction description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get transaction merchant return URL
     *
     * @return string
     */
    public function getReturnUrl()
    {
        return $this->returnUrl;
    }

    /**
     * Get transaction purchase ID
     *
     * @return string
     */
    public function getPurchaseId()
    {
        return $this->purchaseId;
    }

    /**
     * Get transaction currency code
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Get transaction entrance code
     *
     * @return string
     */
    public function getEntranceCode()
    {
        return $this->entranceCode;
    }

    /**
     * Get transaction issuing bank code
     *
     * @return string
     */
    public function getIssuer()
    {
        return $this->issuer;
    }

    /**
     * Get transaction amount in eurocents
     *
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Get transaction expiration date
     *
     * @return DateTime
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }
}
