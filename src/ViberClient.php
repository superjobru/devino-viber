<?php
declare(strict_types=1);

namespace superjob\devino;

use GuzzleHttp\Client;
use GuzzleHttp\Message\ResponseInterface;
use InvalidArgumentException;
use RuntimeException;
use superjob\devino\exception\BadResponseCodeException;
use superjob\devino\exception\BadResponseFormatException;
use superjob\devino\exception\BadStatusException;
use superjob\devino\message\IRequest;
use superjob\devino\message\SendRequest;
use superjob\devino\message\SendResponse;
use superjob\devino\message\StatusRequest;
use superjob\devino\message\StatusResponse;

/**
 * Devino Viber HTTP Client according to http://devino-documentation.readthedocs.io/viber-resender.html
 */
class ViberClient
{
    private const URL = 'https://viber.devinotele.com:444/';
    private const ENDPOINT_SEND = 'send';
    private const ENDPOINT_STATUS = 'status';
    /**
     * @var Client
     */
    protected $client;
    /**
     * @var string
     */
    protected $login;
    /**
     * @var string
     */
    protected $password;
    /**
     * @var bool
     */
    protected $authenticated = false;
    /**
     * @var ResponseDecoder
     */
    protected $responseDecoder;

    /**
     * ViberClient constructor.
     *
     * @param Client $client
     * @param string $login
     * @param string $password
     */
    public function __construct(Client $client, string $login, string $password)
    {
        $this->client = $client;
        $this->login = $login;
        $this->password = $password;
    }

    protected function getAuthHeader(): string
    {
        return 'Basic ' . base64_encode("{$this->login}:{$this->password}");
    }

    /**
     * @param array $messages
     * @param bool  $resendSms
     *
     * @return SendResponse[]
     * @throws InvalidArgumentException
     * @throws BadResponseCodeException
     * @throws RuntimeException
     * @throws BadStatusException
     * @throws BadResponseFormatException
     */
    public function send(array $messages, bool $resendSms = false): array
    {
        $response = $this->makeRequest(self::ENDPOINT_SEND, new SendRequest($messages, $resendSms));

        return $this->getResponseDecoder()->decodeSendResponse($response);
    }

    /**
     * @param int[] $messageIds
     *
     * @return StatusResponse[]
     * @throws BadResponseCodeException
     * @throws BadStatusException
     * @throws BadResponseFormatException
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public function status(array $messageIds): array
    {
        $response = $this->makeRequest(self::ENDPOINT_STATUS, new StatusRequest($messageIds));

        return $this->getResponseDecoder()->decodeStatusResponse($response);
    }

    /**
     * @param string   $endpoint
     * @param IRequest $request
     *
     * @return \GuzzleHttp\Message\FutureResponse|ResponseInterface|\GuzzleHttp\Ring\Future\FutureInterface|null
     */
    protected function makeRequest(string $endpoint, IRequest $request)
    {
        $clientRequest = $this->client->createRequest(
            'POST',
            self::URL . $endpoint,
            ['json' => $request]
        );
        $clientRequest->setHeader('Authorization', $this->getAuthHeader());

        return $this->client->send($clientRequest);
    }

    protected function getResponseDecoder(): ResponseDecoder
    {
        if (null === $this->responseDecoder) {
            $this->responseDecoder = new ResponseDecoder();
        }
        return $this->responseDecoder;
    }
}