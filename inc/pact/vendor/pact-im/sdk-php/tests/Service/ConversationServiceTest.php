<?php

namespace Pact\Tests\Service;

use Pact\Exception\InvalidArgumentException;
use Pact\Http\Methods;
use Pact\Service\ConversationService;

class ConversationServiceTest extends ServiceTestCase
{   
    protected static $serviceClass = ConversationService::class;

    /** @var ConversationService */
    protected $service;

    /** @var int $companyId */
    private $companyId;

    /** @var int $conversationId */
    private $conversationId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->companyId = random_int(1, 500);
        $this->conversationId = random_int(1, 500);
    }

    /**
     * @dataProvider validDataSetGetConversation
     */
    public function test_get_conversations($from, $per, $sort)
    {
        $query = ['from' => $from, 'per' => $per, 'sort_direction' => $sort];
        $this->expectedMethod = Methods::GET;
        $this->expectedUrl = $this->formatEndpoint('', [$this->companyId], $query);

        $response = $this->service->getConversations(
            $this->companyId,
            $from,
            $per, 
            $sort
        );
        $this->assertSame('ok', $response->status);
    }

    public function validDataSetGetConversation()
    {
        return [
            [null, null, null],
            ['asdf', null, null],
            ['asdf', 50, null],
            ['asdf', 50, 'asc'],
            ['asdf', 50, 'desc'],
        ];
    }

    /**
     * @dataProvider dataset_get_conversation_with_invalid_parameters_throws_invalid_argument
     */
    public function test_get_conversation_with_invalid_parameters_throws_invalid_argument($from, $per, $sort)
    {
        $this->expectException(InvalidArgumentException::class);
            
        $response = $this->service->getConversations(
            $this->companyId,
            $from,
            $per, 
            $sort
        );
        $this->assertSame('ok', $response->status);
    }

    public function dataset_get_conversation_with_invalid_parameters_throws_invalid_argument()
    {
        $longString = str_repeat('a', 256);
        return [
            'Exception if "from" length >= 256' => [$longString, null, null],
            'Exception if "per" is outside limits' => [null, 0, null],
            'Exception if "per" is outside limits(1)' => [null, 101, null],
            'Exception if sort direction is not asc or desc' => [null, null, 'safd']
        ];
    }


    /**
     * @dataProvider dataset_create_conversation
     */
    public function test_create_conversation(string $provider, array $providerParams)
    {
        $this->expectedMethod = Methods::POST;
        $this->expectedUrl = $this->formatEndpoint('', [$this->companyId]);
            
        $response = $this->service->createConversation(
            $this->companyId,
            $provider,
            $providerParams
        );
        $this->assertSame('ok', $response->status);
    }

    public function dataset_create_conversation()
    {
        return [
            ['whatsapp', ['phone'=>'88005553535']]
        ];
    }

    public function test_get_details()
    {
        $this->expectedMethod = Methods::GET;
        $this->expectedUrl = $this->formatEndpoint('/%s', [$this->companyId, $this->conversationId]);
            
        $response = $this->service->getDetails(
            $this->companyId,
            $this->conversationId
        );
        $this->assertSame('ok', $response->status);
    }

    public function test_update_assignee()
    {
        $this->expectedMethod = Methods::PUT;
        $this->expectedUrl = $this->formatEndpoint('/%s/assign', [$this->companyId, $this->conversationId]);
            
        $response = $this->service->updateAssignee(
            $this->companyId,
            $this->conversationId,
            random_int(1, 500)
        );
        $this->assertSame('ok', $response->status);
    }

    /**
     * @dataProvider dataset_update_assignee_invalid_id
     */
    public function test_update_assignee_invalid_id_throws_invalid_argument($assigneeId)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->formatEndpoint('/%s/assign', [$this->companyId, $this->conversationId]);
            
        $response = $this->service->updateAssignee(
            $this->companyId,
            $this->conversationId,
            $assigneeId
        );
        $this->assertSame('ok', $response->status);
    }

    public function dataset_update_assignee_invalid_id()
    {
        return [
            [0]
        ];
    }
}
