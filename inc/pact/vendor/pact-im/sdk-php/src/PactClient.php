<?php

namespace Pact;

use Pact\Service\ServiceFactory;

/**
 * @property \Pact\Service\ConversationService $conversations
 * @property \Pact\Service\MessageService $messages
 * @property \Pact\Service\AttachmentService $attachments
 * @property \Pact\Service\CompanyService $companies
 * @property \Pact\Service\ChannelService $channels
 * @property \Pact\Service\MessageDeliveryJobService $jobs
 */
class PactClient extends PactClientBase
{
    /** @var ServiceFactory */
    protected $services = null;

    public function __construct($config)
    {
        parent::__construct($config);

        $this->services = new ServiceFactory($this);
    }

    public function __get($serviceName)
    {
        return $this->services->{$serviceName};
    }
}
