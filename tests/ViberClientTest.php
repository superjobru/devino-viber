<?php
declare(strict_types=1);

namespace superjob\devino\tests;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use superjob\devino\content\TextContent;
use superjob\devino\Message;
use superjob\devino\message\SendRequest;
use superjob\devino\ResponseDecoder;
use superjob\devino\ViberClient;

class ViberClientTest extends TestCase
{
    /**
     * @var ViberClient|PHPUnit_Framework_MockObject_MockObject
     */
    private $client;

    protected function setUp()
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
     */
    public function testSendRequest(array $messages)
    {
        $this->client->expects(static::exactly(1))
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