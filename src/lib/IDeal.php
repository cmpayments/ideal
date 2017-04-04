<?php

namespace CMPayments\IDeal;

use CMPayments\IDeal\Exception;
use CMPayments\IDeal\Request;
use CMPayments\IDeal\Response;
use DOMDocument;
use XMLSecurityDSig;
use XMLSecurityKey;

/**
 * Class IDeal
 * @package CMPayments\IDeal
 */
class IDeal
{
    /**
     * Class constants
     */
    const VERSION            = "3.3.1";
    const EURO               = 'EUR';
    const DUTCH              = 'nl';
    const ENGLISH            = 'en';
    const SUCCESS            = 'Success';
    const CANCELLED          = 'Cancelled';
    const EXPIRED            = 'Expired';
    const FAILURE            = 'Failure';
    const OPEN               = 'Open';
    const DEFAULT_EXPIRATION = '15 minutes';

    /**
     * @var int Merchant ID
     */
    private $merchantId;

    /**
     * @var int Merchant sub ID
     */
    private $subId;

    /**
     * @var string Merchant private key (PKCS string representation)
     */
    private $merchantPrivateKey;

    /**
     * @var string Merchant certificate (PKCS string representation)
     */
    private $merchantCertificate;

    /**
     * @var string Acquirer certificate (PKCS string representation)
     */
    private $acquirerCertificate;

    /**
     * @var string Base URL endpoint (unified for all request types)
     */
    private $baseUrl;

    /**
     * @var string Transaction URL
     */
    private $transactionUrl;

    /**
     * @var string Status URL
     */
    private $statusUrl;

    /**
     * @var string Directory URL
     */
    private $directoryUrl;

    /**
     * @var string expiration period
     */
    private $expirationPeriod;

    /**
     * @var string HTTP proxy URL
     */
    private $proxyUrl;

    /**
     * @var bool Use SSL certificate verification
     */
    private $verification = true;

    /**
     * @var bool Automatically verify
     */
    private $autoVerify = true;

    /**
     * @var bool Fail on status
     */
    private $failOnStatus = false;

    /**
     * IDeal constructor.
     *
     * @param string $baseUrl Base URL endpoint (unified for all request types)
     */
    public function __construct($baseUrl = null)
    {
        if ($this->baseUrl = $baseUrl) {
            $this->setTransactionUrl($baseUrl);
            $this->setStatusUrl($baseUrl);
            $this->setDirectoryUrl($baseUrl);
        }
    }

    /**
     * Sets Merchant ID and sub ID
     *
     * @param     $merchantId
     * @param int $subId
     */
    public function setMerchant($merchantId, $subId = 0)
    {
        $this->merchantId = $merchantId;
        $this->subId = $subId;
    }

    /**
     * Sets merchant private key
     *
     * @param      $key
     * @param null $passphrase
     * @param bool $isFile
     */
    public function setMerchantPrivateKey($key, $passphrase = null, $isFile = true)
    {
        $this->merchantPrivateKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, ['type' => 'private']);
        $this->merchantPrivateKey->passphrase = $passphrase;
        $this->merchantPrivateKey->loadKey($key, $isFile);
    }

    /**
     * Sets merchant certificate
     *
     * @param      $key
     * @param bool $isFile
     */
    public function setMerchantCertificate($key, $isFile = true)
    {
        $this->merchantCertificate = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, ['type' => 'public']);
        $this->merchantCertificate->loadKey($key, $isFile, true);
    }

    /**
     * Sets acquirer certificate
     *
     * @param      $key
     * @param bool $isFile
     */
    public function setAcquirerCertificate($key, $isFile = true)
    {
        $this->acquirerCertificate = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, ['type' => 'public']);
        $this->acquirerCertificate->loadKey($key, $isFile, true);
    }

    /**
     * Get expiration period of the transaction
     *
     * @return string
     */
    public function getExpirationPeriod()
    {
        return $this->expirationPeriod;
    }

    /**
     * Set expiration period of the transaction
     *
     * @param $expirationPeriod
     */
    public function setExpirationPeriod($expirationPeriod)
    {
        $this->expirationPeriod = $expirationPeriod;
    }

    /**
     * Get HTTP proxy URL
     *
     * @return string
     */
    public function getProxyUrl()
    {
        return $this->proxyUrl;
    }

    /**
     * Set HTTP proxy URL
     *
     * @param $proxyUrl
     */
    public function setProxyUrl($proxyUrl)
    {
        $this->proxyUrl = $proxyUrl;
    }

    /**
     * Disables SSL verification
     */
    public function disableVerification()
    {
        $this->verification = false;
    }

    /**
     * Disables automatic verification
     */
    public function disableAutoVerify()
    {
        $this->autoVerify = false;
    }

    /**
     * Indicates if SSL verification is disabled
     *
     * @return bool
     */
    public function verificationDisabled()
    {
        return !$this->verification;
    }

    /**
     * Indicates if automatic verification is enabled
     *
     * @return bool
     */
    public function doesAutoVerify()
    {
        return $this->autoVerify;
    }

    /**
     * Sets failure mode on unsuccessful return status
     *
     * @param bool $fail
     */
    public function failOnStatusNotSuccess($fail = true)
    {
        $this->failOnStatus = $fail;
    }

    /**
     * Return merchant ID
     *
     * @return int
     */
    public function getMerchantId()
    {
        return $this->merchantId;
    }

    /**
     * Return merchant sub ID
     *
     * @return int
     */
    public function getSubId()
    {
        return $this->subId;
    }

    /**
     * Return merchant private key
     *
     * @return string
     */
    public function getMerchantPrivateKey()
    {
        return $this->merchantPrivateKey;
    }

    /**
     * Return merchant certificate
     *
     * @return string
     */
    public function getMerchantCertificate()
    {
        return $this->merchantCertificate;
    }

    /**
     * Return merchant acquirer certificate
     *
     * @return string
     */
    public function getAcquirerCertificate()
    {
        return $this->acquirerCertificate;
    }

    /**
     * Return base URL
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Return transaction URL
     *
     * @return string
     */
    public function getTransactionUrl()
    {
        return $this->transactionUrl;
    }

    /**
     * Set transaction URL
     *
     * @param string $transactionUrl
     *
     * @return IDeal
     */
    public function setTransactionUrl($transactionUrl)
    {
        $this->transactionUrl = $transactionUrl;

        return $this;
    }

    /**
     * Return status URL
     *
     * @return string
     */
    public function getStatusUrl()
    {
        return $this->statusUrl;
    }

    /**
     * Set status URL
     *
     * @param string $statusUrl
     *
     * @return IDeal
     */
    public function setStatusUrl($statusUrl)
    {
        $this->statusUrl = $statusUrl;

        return $this;
    }

    /**
     * Return directory URL
     *
     * @return string
     */
    public function getDirectoryUrl()
    {
        return $this->directoryUrl;
    }

    /**
     * Set directory URL
     *
     * @param string $directoryUrl
     *
     * @return IDeal
     */
    public function setDirectoryUrl($directoryUrl)
    {
        $this->directoryUrl = $directoryUrl;

        return $this;
    }

    /**
     * Set client options
     *
     * @param array $parameters
     *
     * @return $this
     */
    public function setOptions(array $parameters)
    {
        $mandatoryParameters = [
            'acquirerCertificate',
            'merchantId',
            'merchantSubId',
            'merchantCertificate',
            'merchantPrivateKey'
        ];

        foreach ($mandatoryParameters as $parameter) {
            if (!array_key_exists($parameter, $parameters)) {
                throw new \UnexpectedValueException('Mandatory parameter: ' . $parameter . ' is not set!');
            }
        }

        $this->setAcquirerCertificate(
            $parameters['acquirerCertificate'],
            !$this->isPkcsFormat($parameters['acquirerCertificate'])
        );
        $this->setMerchant(
            $parameters['merchantId'],
            $parameters['merchantSubId']
        );
        $this->setMerchantCertificate(
            $parameters['merchantCertificate'],
            !$this->isPkcsFormat($parameters['merchantCertificate'])
        );
        $this->setMerchantPrivateKey(
            $parameters['merchantPrivateKey'],
            empty($parameters['password']) ? null : $parameters['password'],
            !$this->isPkcsFormat($parameters['merchantPrivateKey'])
        );

        if (!empty($parameters['transactionUrl'])) {
            $this->setTransactionUrl($parameters['transactionUrl']);
        }

        if (!empty($parameters['statusUrl'])) {
            $this->setStatusUrl($parameters['statusUrl']);
        }

        if (!empty($parameters['directoryUrl'])) {
            $this->setDirectoryUrl($parameters['directoryUrl']);
        }

        return $this;
    }

    /**
     * Get a list of all issuing banks
     *
     * @return array
     */
    public function getIssuers()
    {
        $request  = $this->createDirectoryRequest();
        $response = $request->send();

        return $response->getAllIssuers();
    }


    /**
     * Create an iDEAL directory request
     *
     * @return Request\DirectoryRequest
     */
    public function createDirectoryRequest()
    {
        return new Request\DirectoryRequest($this);
    }

    /**
     * Starts an iDEAL transaction
     *
     * @param $issuerId
     * @param $returnUrl
     * @param $purchaseId
     * @param $amount
     * @param $description
     * @param string $expirationPeriod The expiration period of the new transaction
     *
     * @return Response\DirectoryResponse|Response\ErrorResponse|Response\StatusResponse|Response\TransactionResponse|null
     */
    public function startTransAction($issuerId, $returnUrl, $purchaseId, $amount, $description, $expirationPeriod = self::DEFAULT_EXPIRATION)
    {
        $transactionRequest = $this->createTransactionRequest(
            $issuerId,
            $returnUrl,
            $purchaseId,
            $amount,
            $description,
            $expirationPeriod
        );

        return $this->send($transactionRequest);
    }

    /**
     * Create an iDEAL transaction request
     *
     * @param string $issuer           The issuing bank of the end user
     * @param string $returnUrl        The URL to return to after a transaction
     * @param string $id               Purchase ID
     * @param int    $amount           The amount to be retrieved in eurocents
     * @param string $description      Description of the transaction
     * @param string $expirationPeriod The expiration period of the new transaction
     *
     * @return Request\TransactionRequest
     */
    public function createTransactionRequest($issuer, $returnUrl, $id, $amount, $description, $expirationPeriod = self::DEFAULT_EXPIRATION)
    {
        $request = new Request\TransactionRequest($this);
        $request->setIssuer($issuer);
        $request->setReturnUrl($returnUrl);
        $request->setPurchaseId($id);
        $request->setAmount($amount);
        $request->setDescription($description);
        $request->setEntranceCode($this->getRandom(40));
        $request->setExpirationPeriod($expirationPeriod);

        return $request;
    }

    /**
     * Retrieve an iDEAL transaction status
     *
     * @param $transactionId
     *
     * @return Response\DirectoryResponse|Response\ErrorResponse|Response\StatusResponse|Response\TransactionResponse|null
     */
    public function getTransactionStatus($transactionId)
    {
        $statusRequest = $this->createStatusRequest($transactionId);

        return $this->send($statusRequest);
    }


    /**
     * Create an iDEAL transaction status request
     *
     * @param $transactionId
     *
     * @return Request\StatusRequest
     */
    public function createStatusRequest($transactionId)
    {
        $request = new Request\StatusRequest($this);
        $request->setTransactionId($transactionId);
        return $request;
    }

    /**
     * Send an actual request to the iDEAL acquirer
     *
     * @param Request\Request $request
     *
     * @return Response\DirectoryResponse|Response\ErrorResponse|Response\StatusResponse|Response\TransactionResponse|null
     */
    public function send(Request\Request $request)
    {
        if (!$request->isSigned()) {
            $request->sign();
        }

        // determine correct URL
        switch(1) {
            case $request instanceof Request\TransactionRequest:
                $url = $this->getTransactionUrl();
                break;
            case $request instanceof Request\StatusRequest:
                $url = $this->getStatusUrl();
                break;
            case $request instanceof Request\DirectoryRequest:
                $url = $this->getDirectoryUrl();
                break;
            default:
                $url = $this->getBaseUrl();
        }

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request->getDocumentString());
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: text/xml; charset=utf-8'
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, $this->verification);


        if ($this->proxyUrl != null) {
            curl_setopt($curl, CURLOPT_PROXY, $this->proxyUrl);
        }

        $response = curl_exec($curl);

        if ($this->proxyUrl != null) {
            // Clear up proxy response:
            if (stripos($response, "HTTP/1.0 200 Connection established\r\n\r\n") !== false) {
                $response = str_ireplace("HTTP/1.0 200 Connection established\r\n\r\n", '', $response);
            }

            if (stripos($response, "HTTP/1.1 200 Connection established\r\n\r\n") !== false) {
                $response = str_ireplace("HTTP/1.1 200 Connection established\r\n\r\n", '', $response);
            }
        }

        if (stripos($response, "HTTP/1.1 100 Continue\r\n\r\n") !== false) {
            $response = str_ireplace("HTTP/1.1 100 Continue\r\n\r\n", '', $response);
        }

        // Split headers and body:
        list($headers, $body) = explode("\r\n\r\n", $response, 2);

        // Explode headers
        $headers = explode("\r\n", $headers);

        return $this->handleResult($request, $headers, $body);
    }

    /**
     * Handle iDEAL acquirer response to a request
     *
     * @param Request\Request $request
     * @param                 $headers
     * @param                 $document
     *
     * @return Response\DirectoryResponse|Response\ErrorResponse|Response\StatusResponse|Response\TransactionResponse|null
     * @throws Exception\InvalidXMLException
     * @throws Exception\NoSuccessException
     * @throws Exception\ResponseException
     * @throws Exception\UnknownResponseException
     */
    protected function handleResult(Request\Request $request, $headers, $document)
    {
        $doc = new DOMDocument();
        if (@$doc->loadXML($document)) {
            $response = null;
            switch ($doc->documentElement->tagName) {
                case 'AcquirerErrorRes':
                    $response = new Response\ErrorResponse($this, $request, $doc);
                    break;
                case 'DirectoryRes':
                    $response = new Response\DirectoryResponse($this, $request, $doc);
                    break;
                case 'AcquirerTrxRes':
                    $response = new Response\TransactionResponse($this, $request, $doc);
                    break;
                case 'AcquirerStatusRes':
                    $response = new Response\StatusResponse($this, $request, $doc);
                    break;
                default:
                    throw new Exception\UnknownResponseException();
            }

            if ($this->doesAutoVerify()) {
                $response->verify(true);
            }

            if ($response instanceof Response\ErrorResponse) {
                throw new Exception\ResponseException($response);
            }

            if ($this->failOnStatus && $response instanceof Response\StatusResponse) {
                if ($response->getStatus() !== self::SUCCESS) {
                    throw new Exception\NoSuccessException($response);
                }
            }
            return $response;
        }

        throw new Exception\InvalidXMLException();
    }

    /**
     * Verify the digital signature of a payload
     *
     * @param DOMDocument    $document
     * @param XMLSecurityKey $cert
     * @param bool           $throwException
     *
     * @return bool
     * @throws Exception\SecurityException
     */
    public function verify(DOMDocument $document, XMLSecurityKey $cert, $throwException = false)
    {
        if (!$this->verification) {
            return true;
        } else {
            $dsig = new XMLSecurityDSig();
            $signature = $dsig->locateSignature($document);
            if (!$signature) {
                if ($throwException) {
                    throw new Exception\SecurityException('No signature element');
                }
                return false;
            }

            $dsig->canonicalizeSignedInfo();
            if (!$dsig->validateReference()) {
                if ($throwException) {
                    throw new Exception\SecurityException('Reference for signature invalid');
                }
                return false;
            }

            if (!$dsig->verify($cert)) {
                if ($throwException) {
                    throw new Exception\SecurityException('Invalid signature');
                }
                return false;
            }
            return true;
        }
    }

    /**
     * Simple check to determine if a string is likely to represent PKCS PEM data
     *
     * @param string $value
     *
     * @return bool
     */
    private function isPkcsFormat($value)
    {
        return (strpos(strtoupper(trim($value)), '-----BEGIN') === 0);
    }

    /**
     * Generates a random string
     *
     * @param int $length
     *
     * @return string
     */
    private function getRandom($length = 40)
    {
        $keys = array_merge(range(0,9), range('a', 'z'));
        $key = '';
        for($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }
        return $key;
    }
}
