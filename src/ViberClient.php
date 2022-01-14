<?php
declare(strict_types=1);

namespace superjob\devino;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use superjob\devino\exception\BadResponseCodeException;
use superjob\devino\exception\BadResponseFormatException;
use superjob\devino\exception\BadStatusException;
use superjob\devino\message\IRequest;
use superjob\devino\message\SendRequest;
use superjob\devino\message\SendResponse;
use superjob\devino\message\SmsStatesResponse;
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
        return 'Basic ' . base64_encode("$this->login:$this->password");
    }

    /**
     * @param array $messages
     *
     * @return SendResponse[]
     * @throws InvalidArgumentException
     * @throws BadResponseCodeException
     * @throws RuntimeException
     * @throws BadStatusException
     * @throws BadResponseFormatException|GuzzleException
     */
    public function send(array $messages): array
    {
        $response = $this->makeRequest(self::ENDPOINT_SEND, new SendRequest($messages));

        return $this->getResponseDecoder()->decodeSendResponse($response);
    }

    /**
     * @param int[] $messageIds
     *
     * @return StatusResponse[]|SmsStatesResponse[]
     * @throws BadResponseCodeException
     * @throws BadStatusException
     * @throws BadResponseFormatException
     * @throws RuntimeException
     * @throws InvalidArgumentException|GuzzleException
     */
    public function status(array $messageIds): array
    {
        $response = $this->makeRequest(self::ENDPOINT_STATUS, new StatusRequest($messageIds));

        return $this->getResponseDecoder()->decodeStatusResponse($response);
    }

    /**
     * @param string $endpoint
     * @param IRequest $request
     *
     * @return ResponseInterface
     * @throws GuzzleException
     */
    protected function makeRequest(string $endpoint, IRequest $request): ResponseInterface
    {
        $url = self::URL . $endpoint;
        $options = [
            RequestOptions::JSON => $request,
            RequestOptions::HEADERS => [
                'Authorization' => $this->getAuthHeader()
            ]
        ];

        return $this->client->request('POST', $url, $options);
    }

    protected function getResponseDecoder(): ResponseDecoder
    {
        if (null === $this->responseDecoder) {
            $this->responseDecoder = new ResponseDecoder();
        }
        return $this->responseDecoder;
    }
}