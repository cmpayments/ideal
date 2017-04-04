<?php

namespace CMPayments\IDeal\Exception;

use CMPayments\IDeal\Response\Response;

/**
 * Class NoSuccessException
 * @package CMPayments\IDeal\Exception
 */
class NoSuccessException extends IDealException
{
    /**
     * @var Response iDEAL response
     */
    protected $response;

    /**
     * NoSuccessException constructor.
     *
     * @param Response $response
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * Get iDEAL response
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}
