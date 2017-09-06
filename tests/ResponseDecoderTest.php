<?php
declare(strict_types=1);

namespace superjob\devino\tests;

use GuzzleHttp\Message\Response;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use superjob\devino\exception\BadResponseCodeException;
use superjob\devino\exception\BadResponseFormatException;
use superjob\devino\exception\BadStatusException;
use superjob\devino\message\SendResponse;
use superjob\devino\message\SmsState;
use superjob\devino\message\SmsStatesResponse;
use superjob\devino\message\StatusResponse;
use superjob\devino\ResponseDecoder;

class ResponseDecoderTest extends TestCase
{
    /**
     * @var ResponseDecoder
     */
    private $responseDecoder;
    /**
     * @var Response|PHPUnit_Framework_MockObject_MockObject
     */
    private $response;

    protected function setUp()
    {
        parent::setUp();
        $this->responseDecoder = new ResponseDecoder();
        $this->response = $this->getMockBuilder(Response::class)
                               ->setMethods(['getBody', 'getStatusCode'])
                               ->disableOriginalConstructor()
                               ->getMock();
    }

    /**
     * @dataProvider providerTestFailDecodeResponse
     *
     * @param string $responseText
     * @param int    $statusCode
     * @param string $exception
     */
    public function testFailDecodeResponse(string $responseText, int $statusCode, string $exception): void
    {
        $this->expectException($exception);
        $this->response->method('getBody')->willReturn($responseText);
        $this->response->method('getStatusCode')->willReturn($statusCode);
        $this->responseDecoder->decodeSendResponse($this->response);
    }

    public function providerTestFailDecodeResponse(): array
    {
        return [
            ['', 400, BadResponseCodeException::class],
            ['', 200, BadResponseFormatException::class],
            [json_encode(['status' => 'error']), 200, BadStatusException::class],
            [json_encode(['status' => 'ok']), 200, BadResponseFormatException::class],
            [
                '{"status":"ok","messages":[{"providerId":54321}]}',
                200,
                BadResponseFormatException::class
            ],
            [
                '{"status":"ok","messages":[{"code":"ok"}]}',
                200,
                BadResponseFormatException::class
            ],
        ];
    }

    /**
     * @dataProvider providerTestSuccessDecodeSendResponse
     *
     * @param string $responseText
     * @param array  $expected
     */
    public function testSuccessDecodeSendResponse(string $responseText, array $expected): void
    {
        $this->response->method('getBody')->willReturn($responseText);
        $this->response->method('getStatusCode')->willReturn(200);
        static::assertEquals(
            $expected,
            $this->responseDecoder->decodeSendResponse($this->response)
        );
    }

    public function providerTestSuccessDecodeSendResponse(): array
    {
        return [
            [json_encode(['status' => 'ok', 'messages' => []]), []],

            [
                '{"status":"ok","messages":[{"providerId":54321,"code":"ok"}]}',
                [
                    new SendResponse('ok', '54321'),
                ]
            ],
        ];
    }

    /**
     * @dataProvider providerTestSuccessDecodeStatusResponse
     *
     * @param string $responseText
     * @param array  $expected
     */
    public function testSuccessDecodeStatusResponse(string $responseText, array $expected): void
    {
        $this->response->method('getBody')->willReturn($responseText);
        $this->response->method('getStatusCode')->willReturn(200);
        static::assertEquals(
            $expected,
            $this->responseDecoder->decodeStatusResponse($this->response)
        );
    }

    public function providerTestSuccessDecodeStatusResponse(): array
    {
        return [
            [
                '{"status":"ok","messages":[{"providerId":"3158611117333282818","code":"ok","smsStates":[{"id":"583465579822710798","state":"delivered"}]},{"providerId":"3158611117333282819","code":"ok","status":"read","statusAt":"2016-08-10 15:28:50"}]}',
                [
                    new SmsStatesResponse('ok', '3158611117333282818', [
                        new SmsState('583465579822710798', 'delivered')
                    ]),
                    new StatusResponse('ok', '3158611117333282819', 'read', '2016-08-10 15:28:50'),
                ]
            ],
            [
                '{"status":"ok","messages":[{"providerId":"3158611117333282816","code":"ok","smsStates":[{"id":"583465579822710784","state":"delivered"},{"id":"583465579822710785","state":"delivered"}]},{"providerId":"3158611117333282818","code":"ok","smsStates":[{"id":"583465579822710798","state":"delivered"}]},{"providerId":"3158611117333282819","code":"ok","status":"read","statusAt":"2016-08-10 15:28:50"}]}',
                [
                    new SmsStatesResponse('ok', '3158611117333282816', [
                        new SmsState('583465579822710784', 'delivered'),
                        new SmsState('583465579822710785', 'delivered'),
                    ]),
                    new SmsStatesResponse('ok', '3158611117333282818', [
                        new SmsState('583465579822710798', 'delivered')
                    ]),
                    new StatusResponse('ok', '3158611117333282819', 'read', '2016-08-10 15:28:50'),
                ]
            ],
        ];
    }
}