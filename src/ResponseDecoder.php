<?php
declare(strict_types=1);

namespace superjob\devino;

use GuzzleHttp\Exception\ParseException;
use GuzzleHttp\Message\ResponseInterface;
use RuntimeException;
use superjob\devino\exception\BadResponseCodeException;
use superjob\devino\exception\BadResponseFormatException;
use superjob\devino\exception\BadStatusException;
use superjob\devino\message\SendResponse;
use superjob\devino\message\SmsState;
use superjob\devino\message\SmsStatesResponse;
use superjob\devino\message\StatusResponse;

class ResponseDecoder
{
    private const STATUS_OK = 'ok';
    private const CODE_OK  = 200;

    /**
     * @param ResponseInterface $response
     *
     * @return array
     * @throws BadResponseCodeException
     * @throws RuntimeException
     * @throws BadResponseFormatException
     * @throws BadStatusException
     */
    protected function decodeResponse(ResponseInterface $response): array
    {
        if (static::CODE_OK !== (int) $response->getStatusCode()) {
            throw new BadResponseCodeException($response);
        }

        try {
            $content = $response->json();
        } catch (ParseException $e) {
            throw new BadResponseFormatException($response);
        }

        $status = $content['status'] ?? '';
        if (static::STATUS_OK !== $status) {
            throw new BadStatusException($status);
        }

        if (!isset($content['messages']) || !is_array($content['messages'])) {
            throw new BadResponseFormatException(
                $response,
                'Message field either not found or is not array'
            );
        }

        return $content['messages'];
    }

    /**
     * @param ResponseInterface $response
     *
     * @return SendResponse[]
     * @throws BadResponseCodeException
     * @throws RuntimeException
     * @throws BadResponseFormatException
     * @throws BadStatusException
     */
    public function decodeSendResponse(ResponseInterface $response): array
    {
        $content = $this->decodeResponse($response);

        return array_map(
            function ($message) use ($response) {
                $this->checkResponseMessage($response, $message);
                return new SendResponse($message['code'], (string) $message['providerId']);
            },
            $content
        );
    }

    protected function checkResponseMessage(ResponseInterface $response, array $message): void
    {
        if (!isset($message['code'], $message['providerId'])) {
            throw new BadResponseFormatException($response, 'Code and providerId should present in response');
        }
    }

    /**
     * @param ResponseInterface $response
     *
     * @return StatusResponse[]|SmsStatesResponse[]
     * @throws BadResponseCodeException
     * @throws RuntimeException
     * @throws BadResponseFormatException
     * @throws BadStatusException
     */
    public function decodeStatusResponse(ResponseInterface $response): array
    {
        $content = $this->decodeResponse($response);

        return array_map(
            function ($message) use ($response) {
                $this->checkResponseMessage($response, $message);

                $message['code'] = (string) $message['code'];
                $message['providerId'] = (string) $message['providerId'];

                if (isset($message['smsStates']) && is_array($message['smsStates'])) {
                    $smsStates = array_map(
                        function ($item) {
                            return new SmsState($item['id'], $item['state']);
                        },
                        $message['smsStates']
                    );

                    return new SmsStatesResponse($message['code'], $message['providerId'], $smsStates);
                }

                return new StatusResponse(
                    $message['code'],
                    $message['providerId'],
                    $message['status'],
                    $message['statusAt']
                );
            },
            $content
        );
    }
}