<?php

namespace Pact\Tests\Service;

use Pact\Exception\InvalidArgumentException;
use Pact\Http\Methods;
use Pact\Service\MessageDeliveryJobService;

class MessageDeliveryJobServiceTest extends ServiceTestCase
{
    protected static $serviceClass = MessageDeliveryJobService::class;

    /** @var MessageDeliveryJobService */
    protected $service;

    /** @var int $companyId */
    private $companyId;

    /** @var int $channelId */
    private $channelId;

    /** @var int $jobId */
    private $jobId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->companyId = random_int(1, 500);
        $this->channelId = random_int(1, 500);
        $this->jobId = random_int(1, 500);
    }

    public function test_valid_get_job()
    {
        $this->expectedMethod = Methods::GET;
        $this->expectedUrl = $this->formatEndpoint('', 
            [$this->companyId, $this->channelId, $this->jobId]
        );

        $this->setUpMocks();

        $response = $this->service->getJob(
            $this->companyId,
            $this->channelId,
            $this->jobId
        );

        $this->assertSame('ok', $response->status);
    }
    
    /**
     * @dataProvider dataset_invalid_arguments_throw_exception
     */
    public function test_invalid_arguments_throw_exception(
        int $companyId,
        int $channelId,
        int $jobId,
        string $expectedException,
        string $expectedMessage
    ): void {
        $this->expectException($expectedException);
        $this->expectExceptionMessage($expectedMessage);

        $this->service->getJob(
            $companyId,
            $channelId,
            $jobId
        );
    }

    public function dataset_invalid_arguments_throw_exception()
    {
        $exceptionTemplate = 'Id of %s must be greater or equal than 0';
        return [
            'Company id < 0' => [
                -1,
                1,
                2,
                InvalidArgumentException::class,
                sprintf($exceptionTemplate, 'company')
            ],
            'Channel id < 0' => [
                1,
                -1,
                2,
                InvalidArgumentException::class,
                sprintf($exceptionTemplate, 'channel')
            ],
            'Job id < 0' => [
                1,
                2,
                -1,
                InvalidArgumentException::class,
                sprintf($exceptionTemplate, 'job')
            ],
        ];
    }
}
