<?php

namespace CMPayments\IDeal\Exception;

use CMPayments\IDeal\Response\ErrorResponse;

/**
 * Class ResponseException
 * @package CMPayments\IDeal\Exception
 */
class ResponseException extends IDealException
{
    /**
     * @var ErrorResponse iDEAL response
     */
    protected $response;

    /**
     * ResponseException constructor.
     *
     * @param ErrorResponse $response
     */
    public function __construct(ErrorResponse $response)
    {
        $this->response = $response;
    }

    /**
     * Get iDEAL response
     *
     * @return ErrorResponse
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Verify iDEAL response signature
     *
     * @param bool $throwException
     *
     * @return bool
     */
    public function verify($throwException = false)
    {
        return $this->response->verify($throwException);
    }

    /**
     * Get response error code
     *
     * @return string
     */
    public function getErrorCode()
    {
        return $this->response->getErrorCode();
    }

    /**
     * Get response error message
     *
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->response->getErrorMessage();
    }

    /**
     * Get response error details
     *
     * @return string
     */
    public function getErrorDetail()
    {
        return $this->response->getErrorDetail();
    }

    /**
     * Get response suggested action
     *
     * @return string
     */
    public function getSuggestedAction()
    {
        return $this->response->getSuggestedAction();
    }

    /**
     * Get response consumer message
     *
     * @return string
     */
    public function getConsumerMessage()
    {
        return $this->response->getConsumerMessage();
    }
}
