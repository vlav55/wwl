<?php

namespace Pact\Tests\Service;

use Pact\Exception\InvalidArgumentException;
use Pact\Http\Methods;
use Pact\Service\CompanyService;

class CompanyServiceTest extends ServiceTestCase
{
    protected static $serviceClass = CompanyService::class;

    /** @var CompanyService */
    protected $service;

    /** @var int $companyId */
    private $companyId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->companyId = random_int(1, 500);
    }

    public function test_get_waba_templates_returns_valid_json()
    {
        $this->expectedMethod = Methods::GET;
        $this->expectedUrl = $this->formatEndpoint('/waba_templates', [$this->companyId]);

        $response = $this->service->getWabaTemplates(
            $this->companyId
        );
        $this->assertSame('ok', $response->status);
    }

    /**
     * @dataProvider dataset_get_waba_templates_with_valid_sort_returns_valid_json
     */
    public function test_get_waba_templates_with_valid_sort_returns_valid_json($sort)
    {
        $this->expectedMethod = Methods::GET;
        $query = ['sort_direction' => $sort];
        $this->expectedUrl = $this->formatEndpoint('/waba_templates', [$this->companyId], $query);
        $response = $this->service->getWabaTemplates(
            $this->companyId,
            null,
            null,
            $sort
        );
        $this->assertSame('ok', $response->status);
    }

    public function dataset_get_waba_templates_with_valid_sort_returns_valid_json()
    {
        return [
            ['asc'], ['desc']
        ];
    }

    public function test_get_waba_templates_with_invalid_sort_throws_invalid_argument()
    {
        $sort = 'asdf';
        $query = ['sort_direction' => $sort];
        $this->expectedMethod = Methods::GET;
        $this->expectedUrl = $this->formatEndpoint('/waba_templates', [$this->companyId], $query);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Sorting parameter must be "asc" or "desc". "'. $sort .'" given');
        $response = $this->service->getWabaTemplates(
            $this->companyId,
            null,
            null,
            $sort
        );
    }

    /**
     * @dataProvider dataset_invalid_name_length
     */
    public function test_create_company_with_invalid_name_length_throws_invalid_argument($name, $msg)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($msg);
        $this->expectedMethod = Methods::POST;
        $this->expectedUrl = $this->formatEndpoint();

        $this->service->createCompany($name);
    }

    /**
     * @dataProvider dataset_company
     */
    public function test_create_company($name, $phone, $description, $webhookUrl)
    {
        $this->expectedMethod = Methods::POST;
        $this->expectedUrl = $this->formatEndpoint();

        $this->setUpMocks(
            $this->callback(function($body) use ($name, $phone, $description, $webhookUrl) {
                parse_str($body, $body);

                $this->assertArrayHasKey('name', $body);
                $this->assertSame($name, $body['name']);

                $this->assertArrayHasKey('phone', $body);
                $this->assertSame($phone, $body['phone']);

                $this->assertArrayHasKey('description', $body);
                $this->assertSame($description, $body['description']);

                $this->assertArrayHasKey('webhook_url', $body);
                $this->assertSame($webhookUrl, $body['webhook_url']);

                return true;
            })
        );

        $response = $this->service->createCompany($name, $phone, $description, $webhookUrl);
        $this->assertSame('ok', $response->status);
    }

    /**
     * @dataProvider dataset_invalid_name_length
     */
    public function test_update_company_with_invalid_name_length_throws_invalid_argument($name, $msg)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($msg);
        $this->expectedMethod = Methods::PUT;
        $this->expectedUrl = $this->formatEndpoint('/%s', [$this->companyId]);

        $this->service->updateCompany($this->companyId, $name);
    }

    public function dataset_invalid_name_length()
    {
        return [
            'Long name' => [str_repeat('a', 256), 'Name length must be less than 256 symbols'],
            'Empty name' => ['', 'Name must be non-empty string']
        ];
    }

    /**
     * @dataProvider dataset_company
     */
    public function test_update_company($name, $phone, $description, $webhookUrl)
    {
        $this->expectedMethod = Methods::PUT;
        $this->expectedUrl = $this->formatEndpoint('/%s', [$this->companyId]);

        $this->setUpMocks(
            $this->callback(function($body) use ($name, $phone, $description, $webhookUrl) {
                parse_str($body, $body);
                $this->assertArrayHasKey('name', $body);
                $this->assertSame($name, $body['name']);

                $this->assertArrayHasKey('phone', $body);
                $this->assertSame($phone, $body['phone']);

                $this->assertArrayHasKey('description', $body);
                $this->assertSame($description, $body['description']);

                $this->assertArrayHasKey('webhook_url', $body);
                $this->assertSame($webhookUrl, $body['webhook_url']);

                return true;
            })
        );
        $response = $this->service->updateCompany($this->companyId, $name, $phone, $description, $webhookUrl);
        $this->assertSame('ok', $response->status);
    }

    public function dataset_company()
    {
        return [
            ['new-name-of-company', 'new-phone', 'new-description', 'new-webhook']
        ];
    }

    public function test_get_companies()
    {
        $this->expectedMethod = Methods::GET;
        $this->expectedUrl = $this->formatEndpoint();

        $response = $this->service->getCompanies();
        $this->assertSame('ok', $response->status);
    }


    /**
     * @dataProvider dataset_get_companies_with_valid_sorting
     */
    public function test_get_companies_with_valid_sorting($sort)
    {
        $this->expectedMethod = Methods::GET;
        $query = ['sort_direction' => $sort];
        $this->expectedUrl = $this->formatEndpoint('', [], $query);
        $response = $this->service->getCompanies(null, null, $sort);
        $this->assertSame('ok', $response->status);
    }

    public function dataset_get_companies_with_valid_sorting()
    {
        return [
            ['asc'], ['desc']
        ];
    }

    public function test_get_companies_with_invalid_sort_throws_invalid_argument()
    {
        $sort = 'asdf';
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Sorting parameter must be "asc" or "desc". "'. $sort .'" given');
        $response = $this->service->getCompanies(null, null, $sort);
    }

    public function test_get_companies_with_invalid_fetch_count_throws_invalid_argument()
    {
        foreach ([0, 101] as $fetchCount) {
            try {
                $response = $this->service->getCompanies(null, $fetchCount);
                $this->fail('Exception not thrown');
            } catch (InvalidArgumentException $e) {
                $this->addToAssertionCount(1);
            }
        }
    }
}
