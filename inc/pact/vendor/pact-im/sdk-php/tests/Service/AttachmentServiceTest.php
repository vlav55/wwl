<?php

namespace Pact\Tests\Service;

use Pact\Exception\FileNotFoundException;
use Pact\Exception\InvalidArgumentException;
use Pact\Http\Methods;
use Pact\Service\AttachmentService;

class AttachmentServiceTest extends ServiceTestCase
{
    protected static $serviceClass = AttachmentService::class;

    /** @var AttachmentService */
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
     * @dataProvider dataset_valid_upload_with_resource
     */
    public function test_valid_upload_with_resource($file)
    {
        $this->expectedMethod = Methods::POST;
        $this->expectedUrl = $this->formatEndpoint('', 
            [$this->companyId, $this->conversationId]
        );

        $this->setUpMocks(
                $this->callback(function($body) {
                    if (!is_array($body)) {
                        return true;
                    }
                    
                    $this->assertArrayHasKey('file', $body);
                    $this->assertIsResource($body['file']);
                    return true;
                })
            );
        $response = $this->service->uploadFile(
            $this->companyId,
            $this->conversationId,
            $file
        );
        $this->assertSame('ok', $response->status);
    }
    
    public function dataset_valid_upload_with_resource()
    {
        return [
            'Resource' => [fopen(__DIR__.'/../data/fennec.png', 'r')],
            'File path' => [__DIR__.'/../data/fennec.png']
        ];
    }

    /**
     * @dataProvider dataset_valid_upload_with_url
     */
    public function test_valid_upload_with_url($file)
    {
        $this->expectedMethod = Methods::POST;
        $this->expectedUrl = $this->formatEndpoint('', 
            [$this->companyId, $this->conversationId]
        );

        $this->setUpMocks(
                $this->callback(function($body) {
                    parse_str($body, $body);
                    $this->assertArrayHasKey('file_url', $body);
                    $this->assertIsString($body['file_url']);
                    $this->assertTrue(
                        (bool)filter_var($body['file_url'], FILTER_VALIDATE_URL),
                        'file_url contains invalid url'
                    );
                    return true;
                })
            );
        
        $response = $this->service->uploadFile(
            $this->companyId,
            $this->conversationId,
            $file
        );
        $this->assertSame('ok', $response->status);
    }
    
    public function dataset_valid_upload_with_url()
    {
        return [
            'Url' => ['https://fennecs.io/purr-fennec.png']
        ];
    }

    public function test_attempt_attach_non_existing_file_throws_file_not_found()
    {
        $this->expectException(FileNotFoundException::class);
        $this->expectExceptionMessageMatches('/^File .+? not found/');
        
        $response = $this->service->uploadFile(
            $this->companyId,
            $this->conversationId,
            __DIR__.'/../not-existing.file'
        );
        $this->assertSame('ok', $response->status);
    }

    public function test_invalid_attachment_throws_invalid_argument()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Attachment must be string or resource or StreamInterface');
        
        $response = $this->service->uploadFile(
            $this->companyId,
            $this->conversationId,
            []
        );
        $this->assertSame('ok', $response->status);
    }
}
