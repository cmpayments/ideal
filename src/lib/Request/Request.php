<?php

namespace CMPayments\IDeal\Request;

use CMPayments\IDeal\IDeal;
use DOMImplementation;
use XMLSecurityDSig;

/**
 * Class Request
 * @package CMPayments\IDeal\Request
 */
class Request
{
    /**
     * Class constants
     */
    const XMLNS = "http://www.idealdesk.com/ideal/messages/mer-acq/3.3.1";

    /**
     * @var IDeal iDEAL client
     */
    protected $ideal;

    /**
     * @var \DOMDocument XML payload
     */
    protected $doc;

    /**
     * @var string Root node
     */
    protected $root;

    /**
     * @var bool Signed indicator
     */
    private $signed;

    /**
     * @var string Merchant
     */
    protected $merchant;

    /**
     * Request constructor.
     *
     * @param IDeal $ideal
     * @param       $rootName
     */
    public function __construct(IDeal $ideal, $rootName)
    {
        $this->ideal = $ideal;
        $this->signed = false;
        $this->createDocument($rootName);
    }

    /**
     * Get iDEAL client
     *
     * @return IDeal
     */
    public function getIdeal()
    {
        return $this->ideal;
    }

    /**
     * Create XML request
     *
     * @param string $rootName XML root node
     */
    private function createDocument($rootName)
    {
        $implementor = new DOMImplementation();
        $this->doc = $implementor->createDocument(self::XMLNS, $rootName);

        $this->doc->version = '1.0';
        $this->doc->encoding = 'UTF-8';
        $this->doc->formatOutput = true;
        $this->doc->preserveWhiteSpace = false;
        $this->doc->formatOutput = false;

        $this->root = $this->doc->documentElement;
        $this->root->setAttribute('version', IDeal::VERSION);

        // add timestamp request is created
        $now = gmdate('Y-m-d\TH:i:s.000\Z');
        $created = $this->createElement('createDateTimestamp', $now);
        $this->root->appendChild($created);

        // add merchant information
        $this->merchant = $this->createElement('Merchant');
        $this->merchant->appendChild(
            $this->createElement(
                'merchantID',
                sprintf('%09d', $this->ideal->getMerchantId())
            )
        );
        $this->merchant->appendChild($this->createElement('subID', $this->ideal->getSubId()));
        $this->root->appendChild($this->merchant);
    }

    /**
     * Create XML namespaced element
     *
     * @param      $name
     * @param null $value
     *
     * @return \DOMElement
     */
    protected function createElement($name, $value = null)
    {
        if ($value === null) {
            $element = $this->doc->createElementNS(self::XMLNS, $name);
        } else {
            $element = $this->doc->createElementNS(self::XMLNS, $name, $value);
        }
        return $element;
    }

    /**
     * Sign XML request
     */
    public function sign()
    {
        $this->preSign();
        $key = $this->ideal->getMerchantPrivateKey();

        // sign document
        $dsig = new XMLSecurityDSig();
        $dsig->setCanonicalMethod(XMLSecurityDSig::EXC_C14N);
        $dsig->addReference($this->doc, XMLSecurityDSig::SHA256, ['http://www.w3.org/2000/09/xmldsig#enveloped-signature'], ['force_uri' => true]);
        $dsig->sign($key, $this->root);
        $signature = $dsig->sigNode;

        // add keyinfo
        $thumbprint = $this->ideal->getMerchantCertificate()->getX509Thumbprint();
        $keyName = $dsig->createNewSignNode('KeyName', strtoupper($thumbprint));
        $keyInfo = $dsig->createNewSignNode('KeyInfo');
        $keyInfo->appendChild($keyName);
        $signature->appendChild($keyInfo);

        $this->signed = true;
    }

    /**
     * Presign XML request
     */
    protected function preSign()
    {
        // do nothing in standard implementation
    }

    /**
     * Returns signed status of request
     *
     * @return bool
     */
    public function isSigned()
    {
        return $this->signed;
    }

    /**
     * Get request payload as XML document
     *
     * @return \DOMDocument
     */
    public function getDocument()
    {
        return $this->doc;
    }

    /**
     * Get request payload as string
     *
     * @return string
     */
    public function getDocumentString()
    {
        return $this->getDocument()->saveXML(null, LIBXML_NOEMPTYTAG);
    }

    /**
     * Send request to iDEAL acquirer
     *
     * @return \CMPayments\IDeal\Response\DirectoryResponse|\CMPayments\IDeal\Response\ErrorResponse|\CMPayments\IDeal\Response\StatusResponse|\CMPayments\IDeal\Response\TransactionResponse|null
     */
    public function send()
    {
        return $this->ideal->send($this);
    }
}
