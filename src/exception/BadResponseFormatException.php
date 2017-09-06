<?php
declare(strict_types=1);

namespace superjob\devino\exception;

use Exception;
use GuzzleHttp\Message\ResponseInterface;

class BadResponseFormatException extends Exception
{
    public function __construct(ResponseInterface $response, string $message = '')
    {
        if ('' !== $message) {
            $message .= PHP_EOL;
        }

        parent::__construct($message . 'Response: ' . (string) $response->getBody());
    }
}