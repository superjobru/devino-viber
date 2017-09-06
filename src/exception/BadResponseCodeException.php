<?php
declare(strict_types=1);

namespace superjob\devino\exception;

use Exception;

class BadResponseCodeException extends Exception
{
    /**
     * BadResponseCodeException constructor.
     *
     * @param string $responseCode
     */
    public function __construct($responseCode)
    {
        parent::__construct("Bad response code: $responseCode");
    }
}