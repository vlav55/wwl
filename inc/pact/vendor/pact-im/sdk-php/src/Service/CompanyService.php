<?php

namespace Pact\Service;

use Pact\Http\Methods;

class CompanyService extends AbstractService
{
    public const SERVICE_ENDPOINT = '/companies';

    /**
     * This method return list of all user companies
     * @link https://pact-im.github.io/api-doc/#companies
     * 
     * @param string $from Next page token geted from last request. 
     *               Not valid or empty token return first page
     * @param int $per Number of elements per page. Default: 50
     * @param string $sort Change sorting direction. Available values: asc, desc. Default: asc.
     * @return Json|null
     */
    public function getCompanies(string $from = null, int $per = null, string $sort = null)
    {
        $this->validator->between($per, 1, 100, 'Number of fetching elements must be between 1 and 100.');
        $this->validator->sort($sort);

        $query = ['from' => $from, 'per' => $per, 'sort_direction' => $sort];

        return $this->request(
            Methods::GET, 
            static::SERVICE_ENDPOINT,
            [],
            null,
            $query
        );
    }

    /**
     * This method updates specific company attributes
     * @link https://pact-im.github.io/api-doc/#get-all-companies
     * 
     * @param int $companyId Id of the company for update
     * @param string $name Company name
     * @param string $phone Official company phone number of contact person
     * @param string $description Company description
     * @param string $webhook_url Endpoint for webhooks
     * @return Json|null
     */
    public function updateCompany(
        int $companyId, 
        string $name = null, 
        string $phone = null, 
        string $description = null, 
        string $webHookUrl = null
    ) {
        $this->validator->_(strlen($name) === 0, 'Name must be non-empty string');
        $this->validator->_(strlen($name) > 255, 'Name length must be less than 256 symbols');

        $body = [
            'name' => $name,
            'phone' => $phone,
            'description' => $description,
            'webhook_url' => $webHookUrl
        ];

        return $this->request(
            Methods::PUT, 
            static::SERVICE_ENDPOINT . '/%s', 
            [$companyId],
            $body
        );
    }

    /**
     * This method creates a new company for user
     * @link https://pact-im.github.io/api-doc/#update-company
     * 
     * @param string $name Company name
     * @param string $phone Official company phone number of contact person
     * @param string $description Company description
     * @param string $webhook_url Endpoint for webhooks
     * @return Json|null
     */
    public function createCompany(
        string $name, 
        string $phone = null, 
        string $description = null, 
        string $webHookUrl = null
    ) {
        $this->validator->_(strlen($name) === 0, 'Name must be non-empty string');
        $this->validator->_(strlen($name) > 255, 'Name length must be less than 256 symbols');

        $body = [
            'name' => $name,
            'phone' => $phone,
            'description' => $description,
            'webhook_url' => $webHookUrl
        ];

        return $this->request(
            Methods::POST, 
            static::SERVICE_ENDPOINT,
            [],
            $body
        );
    }
    
    /**
     * Gets WhatsApp Business templates
     * @link https://pact-im.github.io/api-doc/#waba-templates
     * 
     * @param int id of the company
     * @param string Next page token geted from last request. 
     * Not valid or empty token return first page
     * @param int Number of elements per page. Min 1, max 100, default: 50
     * @param string Change sorting direction. Available values: asc, desc. Default: asc.
     * @return Json|null
     */
    public function getWabaTemplates(int $companyId, ?string $from=null, ?int $per=null, ?string $sort=null)
    {
        $this->validator->_(strlen($from)>255, 'From identificator length must be less or equal 255');
        $this->validator->between($per, 1, 100);
        $this->validator->sort($sort);

        $query = ['from' => $from, 'per' => $per, 'sort_direction' => $sort];

        return $this->request(
            Methods::GET,
            static::SERVICE_ENDPOINT . '/waba_templates',
            [$companyId],
            null,
            $query
        );
    }
}
