<?php

namespace CMPayments\IDeal\Request;

use CMPayments\IDeal\IDeal;

/**
 * Class DirectoryRequest
 * @package CMPayments\IDeal\Request
 */
class DirectoryRequest extends Request
{
    /**
     * Class constants
     */
    const ROOT_NAME = 'DirectoryReq';

    /**
     * DirectoryRequest constructor.
     *
     * @param IDeal $ideal
     */
    public function __construct(IDeal $ideal)
    {
        parent::__construct($ideal, self::ROOT_NAME);
    }
}
