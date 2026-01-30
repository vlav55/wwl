<?php

namespace Pact\Service;

use Pact\Exception\InvalidArgumentException;
use Pact\Http\Methods;
use PHPUnit\Util\Json;

class MessageDeliveryJobService extends AbstractService
{
    public const SERVICE_ENDPOINT = 'companies/%s/channels/%s/jobs/%s';

    /**
     * This method return info about message delivery job
     * @link https://pact-im.github.io/api-doc/?shell#message-delivery-jobs
     *
     * @param int id of the company
     * @param int id of the channel
     * @param int id of the job
     * @return Json|null
     */
    public function getJob(int $companyId = null, int $channelId = null, int $jobId = null)
    {
        $this->validator->_($companyId<0, 'Id of company must be greater or equal than 0');
        $this->validator->_($channelId<0, 'Id of channel must be greater or equal than 0');
        $this->validator->_($jobId<0, 'Id of job must be greater or equal than 0');

        return $this->request(
            Methods::GET,
            static::SERVICE_ENDPOINT,
            [$companyId, $channelId, $jobId]
        );
    }
}
