<?php
declare(strict_types=1);

namespace superjob\devino\exception;

use Exception;

class BadStatusException extends Exception
{
    public function __construct(string $status)
    {
        parent::__construct("Bad response status: $status");
    }
}