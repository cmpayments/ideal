<?php

namespace CMPayments\IDeal\Response;

use CMPayments\IDeal\Request;
use CMPayments\IDeal\Exception;
use CMPayments\IDeal\IDeal;
use DOMDocument;
use DOMNode;
use DOMXPath;
use DateTime;

/**
 * Class Response
 * @package CMPayments\IDeal\Response
 */
class Response
{
    /**
     * @var DOMDocument Response XML document
     */
    protected $doc;

    /**
     * @var \DOMElement Internal XML root element
     */
    protected $root;

    /**
     * @var IDeal iDEAL client
     */
    protected $ideal;

    /**
     * @var DOMXPath internal XML XPath element
     */
    protected $xpath;

    /**
     * @var bool Response is verified
     */
    private $isVerified;

    /**
     * @var bool Response verification is completed
     */
    private $verificationCompleted;

    /**
     * @var Request\Request iDEAL request
     */
    protected $request;

    /**
     * Response constructor.
     *
     * @param IDeal           $ideal
     * @param Request\Request $request
     * @param DOMDocument     $document
     */
    public function __construct(IDeal $ideal, Request\Request $request, DOMDocument $document)
    {
        $this->doc = $document;
        $this->root = $this->doc->documentElement;
        $this->ideal = $ideal;
        $this->xpath = new DOMXPath($this->doc);
        $rootNamespace = $this->doc->lookupNamespaceUri($this->doc->namespaceURI);
        $this->xpath->registerNamespace('i', $rootNamespace);
        $this->verificationCompleted = false;
        $this->request = $request;
    }

    /**
     * Get iDEAL request
     *
     * @return Request\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Get response XML object
     *
     * @return DOMDocument
     */
    public function getDocument()
    {
        return $this->doc;
    }

    /**
     *
     * Verify XML signature
     *
     * @param bool $throwException
     *
     * @return bool
     * @throws Exception\SecurityException
     */
    public function verify($throwException = false)
    {
        if ($this->verificationCompleted === false) {
            $cert = $this->ideal->getAcquirerCertificate();
            $this->isVerified = $this->ideal->verify($this->doc, $cert, $throwException);
            $this->verificationCompleted = true;
        }

        if ($throwException && $this->isVerified === false) {
            throw new Exception\SecurityException('Could not validate response');
        }
        return $this->isVerified;
    }

    /**
     * Get Date/Time from response
     *
     * @return DateTime
     */
    public function getDateTime()
    {
        return new DateTime($this->singleValue('//i:createDateTimestamp'));
    }

    /**
     * Query the internal XML response object
     *
     * @param              $query
     * @param DOMNode|null $node
     *
     * @return \DOMNodeList
     */
    protected function query($query, DOMNode $node = null)
    {
        if ($node === null) {
            return $this->xpath->query($query);
        } else {
            return $this->xpath->query($query, $node);
        }
    }

    /**
     * Get a single XML node from the internal XML response object
     *
     * @param              $query
     * @param DOMNode|null $node
     *
     * @return \DOMElement
     * @throws Exception\InvalidXMLException
     */
    protected function single($query, DOMNode $node = null)
    {
        $nodes = $this->query($query, $node);
        if ($nodes->length <= 0) {
            throw new Exception\InvalidXMLException(sprintf('Could not find node matching query "%s"', $query));
        }
        return $nodes->item(0);
    }

    /**
     * Extract the value from an XML node
     *
     * @param              $query
     * @param DOMNode|null $node
     *
     * @return string
     */
    protected function singleValue($query, DOMNode $node = null)
    {
        return $this->single($query, $node)->nodeValue;
    }

}
