<?php

namespace CMPayments\IDeal\Response;

/**
 * Class ErrorResponse
 * @package CMPayments\IDeal\Response
 */
class ErrorResponse extends Response
{
    /**
     * Get unique iDEAL error code
     *
     * @return string
     */
    public function getErrorCode()
    {
        return $this->singleValue('//i:errorCode');
    }

    /**
     * Get error message corresponding to the error code
     *
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->singleValue('//i:errorMessage');
    }

    /**
     * Get acquirer specific error details
     *
     * @return string
     */
    public function getErrorDetail()
    {
        return $this->singleValue('//i:errorDetail');
    }

    /**
     * Get acquirer specified suggested action
     *
     * @return string
     */
    public function getSuggestedAction()
    {
        return $this->singleValue('//i:suggestedAction');
    }

    /**
     * Get standardised recommendation message for consumer
     *
     * @return string
     */
    public function getConsumerMessage()
    {
        return $this->singleValue('//i:consumerMessage');
    }
}
