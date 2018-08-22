<?php
declare(strict_types=1);

namespace superjob\devino\exception;

use Exception;
use Psr\Http\Message\ResponseInterface;

class BadResponseCodeException extends Exception
{
    /**
     * BadResponseCodeException constructor.
     *
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        parent::__construct('Bad response code: ' . $response->getStatusCode());
    }
}