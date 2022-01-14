<?php
declare(strict_types=1);

namespace superjob\devino\tests;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Utils;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
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
     * @var Response|MockObject
     */
    private $response;

    protected function setUp(): void
    {
        parent::setUp();
        $this->responseDecoder = new ResponseDecoder();
        $this->response = $this->getMockBuilder(Response::class)
                               ->setMethods(['getBody', 'getStatusCode'])
                               ->disableOriginalConstructor()
                               ->getMock();
    }

    private function getStreamInterfaceMock(string $responseText): StreamInterface
    {
        return new class($responseText) implements StreamInterface {
            private $a;
            public function __construct($a)
            {
                $this->a = $a;
            }
            public function __toString()
            {
                return (string) $this->a;
            }

            public function close()
            {
            }

            public function detach()
            {
            }

            public function getSize()
            {
            }

            public function tell()
            {
            }

            public function eof()
            {
            }

            public function isSeekable()
            {
            }

            public function seek($offset, $whence = SEEK_SET)
            {
            }

            public function rewind()
            {
            }

            public function isWritable()
            {
            }

            public function write($string)
            {
            }

            public function isReadable()
            {
            }

            public function read($length)
            {
            }

            public function getContents()
            {
            }

            public function getMetadata($key = null)
            {
            }
        };
    }

    /**
     * @dataProvider providerTestFailDecodeResponse
     *
     * @param string $responseText
     * @param int $statusCode
     * @param string $exception
     *
     * @throws BadResponseCodeException
     * @throws BadResponseFormatException
     * @throws BadStatusException
     */
    public function testFailDecodeResponse(string $responseText, int $statusCode, string $exception): void
    {
        $this->expectException($exception);
        $this->response->method('getBody')->willReturn($this->getStreamInterfaceMock($responseText));
        $this->response->method('getStatusCode')->willReturn($statusCode);
        $this->responseDecoder->decodeSendResponse($this->response);
    }

    public function providerTestFailDecodeResponse(): array
    {
        return [
            ['', 400, BadResponseCodeException::class],
            ['', 200, BadResponseFormatException::class],
            [Utils::jsonEncode(['status' => 'error']), 200, BadStatusException::class],
            [Utils::jsonEncode(['status' => 'ok']), 200, BadResponseFormatException::class],
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
     * @param array $expected
     *
     * @throws BadResponseCodeException
     * @throws BadResponseFormatException
     * @throws BadStatusException
     */
    public function testSuccessDecodeSendResponse(string $responseText, array $expected): void
    {
        $this->response->method('getBody')->willReturn($this->getStreamInterfaceMock($responseText));
        $this->response->method('getStatusCode')->willReturn(200);
        static::assertEquals(
            $expected,
            $this->responseDecoder->decodeSendResponse($this->response)
        );
    }

    public function providerTestSuccessDecodeSendResponse(): array
    {
        return [
            [Utils::jsonEncode(['status' => 'ok', 'messages' => []]), []],

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
     * @param array $expected
     *
     * @throws BadResponseCodeException
     * @throws BadResponseFormatException
     * @throws BadStatusException
     */
    public function testSuccessDecodeStatusResponse(string $responseText, array $expected): void
    {
        $this->response->method('getBody')->willReturn($this->getStreamInterfaceMock($responseText));
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
                '{"status":"ok","messages":[{"providerId":"3158611117333282818","code":"ok","smsStates":[{"id":"583465579822710798","status":"delivered"}]},{"providerId":"3158611117333282819","code":"ok","status":"read","statusAt":"2016-08-10 15:28:50"}]}',
                [
                    new SmsStatesResponse('ok', '3158611117333282818', [
                        new SmsState('583465579822710798', 'delivered')
                    ]),
                    new StatusResponse('ok', '3158611117333282819', 'read', '2016-08-10 15:28:50'),
                ]
            ],
            [
                '{"status":"ok","messages":[{"providerId":"3158611117333282816","code":"ok","smsStates":[{"id":"583465579822710784","status":"delivered"},{"id":"583465579822710785","status":"delivered"}]},{"providerId":"3158611117333282818","code":"ok","smsStates":[{"id":"583465579822710798","status":"delivered"}]},{"providerId":"3158611117333282819","code":"ok","status":"read","statusAt":"2016-08-10 15:28:50"}]}',
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