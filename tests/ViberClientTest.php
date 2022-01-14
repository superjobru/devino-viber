<?php
declare(strict_types=1);

namespace superjob\devino\tests;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use superjob\devino\content\TextContent;
use superjob\devino\Message;
use superjob\devino\message\SendRequest;
use superjob\devino\ResponseDecoder;
use superjob\devino\ViberClient;

class ViberClientTest extends TestCase
{
    /**
     * @var ViberClient|MockObject
     */
    private $client;

    protected function setUp(): void
    {
        parent::setUp();

        $responseDecoder = $this->createMock(ResponseDecoder::class);

        $this->client = $this->getMockBuilder(ViberClient::class)
                             ->setMethods(['makeRequest', 'getResponseDecoder'])
                             ->disableOriginalConstructor()
                             ->getMock();

        $this->client->method('getResponseDecoder')->willReturn($responseDecoder);
    }

    /**
     * @dataProvider providerTestSendRequest
     *
     * @param array $messages
     *
     * @throws \superjob\devino\exception\BadResponseCodeException
     * @throws \superjob\devino\exception\BadResponseFormatException
     * @throws \superjob\devino\exception\BadStatusException
     */
    public function testSendRequest(array $messages)
    {
        $this->client->expects(static::once())
                     ->method('makeRequest')
                     ->with(
                         'send',
                         new SendRequest($messages)
                     )
                     ->willReturn(
                         $this->createMock(Response::class)
                     );

        $this->client->send($messages);
    }

    public function providerTestSendRequest(): array
    {
        return [
            [
                [new Message('', '', '', new TextContent(''))],
            ]
        ];
    }
}