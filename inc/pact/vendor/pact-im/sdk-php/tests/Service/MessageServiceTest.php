<?php

namespace Pact\Tests\Service;

use Pact\Exception\InvalidArgumentException;
use Pact\Http\Methods;
use Pact\Service\MessageService;

class MessageServiceTest extends ServiceTestCase
{
    protected static $serviceClass = MessageService::class;

    /** @var MessageService */
    protected $service;

    /** @var int $companyId */
    private $companyId;

    /** @var int $conversationId */
    private $conversationId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpMocks();
        $this->companyId = random_int(1, 500);
        $this->conversationId = random_int(1, 500);
    }

    public function test_get_messages()
    {
        $this->expectedMethod = Methods::GET;
        $this->expectedUrl = $this->formatEndpoint('', [$this->companyId, $this->conversationId]);
            
        $response = $this->service->getMessages(
            $this->companyId,
            $this->conversationId
        );
        $this->assertSame('ok', $response->status);
    }

    /**
     * @dataProvider dataset_get_messages_with_valid_sorting
     */
    public function test_get_messages_with_valid_sorting($sort)
    {
        $this->expectedMethod = Methods::GET;
        $query = ['sort' => $sort];
        $this->expectedUrl = $this->formatEndpoint('', [$this->companyId, $this->conversationId], $query);
        $response = $this->service->getMessages(
            $this->companyId,
            $this->conversationId,
            null,
            null,
            $sort
        );
        $this->assertSame('ok', $response->status);
    }

    public function dataset_get_messages_with_valid_sorting()
    {
        return [
            ['asc'], ['desc']
        ];
    }

    public function test_get_messages_with_invalid_sort_throws_invalid_argument()
    {
        $sort = 'asdf';
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Sorting parameter must be "asc" or "desc". "'. $sort .'" given');
        $response = $this->service->getMessages(
            $this->companyId,
            $this->conversationId,
            null,
            null,
            $sort
        );
    }

    public function test_get_messages_with_invalid_fetch_count_throws_invalid_argument()
    {
        foreach ([0, 101] as $fetchCount) {
            try {
                $response = $this->service->getMessages(
                    $this->companyId,
                    $this->conversationId,
                    null,
                    $fetchCount
                );
                $this->fail('Exception not thrown');
            } catch (InvalidArgumentException $e) {
                $this->addToAssertionCount(1);
            }
        }
    }

    public function test_send_message()
    {
        $this->expectedMethod = Methods::POST;
        $this->expectedUrl = $this->formatEndpoint('', [$this->companyId, $this->conversationId]);
        foreach([null, []] as $attachments) {
            $response = $this->service->sendMessage(
                $this->companyId,
                $this->conversationId,
                'Message body',
                $attachments
            );
            $this->assertSame('ok', $response->status);
        }
    }

    public function test_send_message_with_invalid_attachments_throws_invalid_argument()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Attachment must be integer');
        $response = $this->service->sendMessage(
            $this->companyId,
            $this->conversationId,
            'Message body',
            [1.5]
        );
        $this->assertSame('ok', $response->status);
    }
}
