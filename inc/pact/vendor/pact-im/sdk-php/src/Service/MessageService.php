<?php

namespace Pact\Service;

use Pact\Exception\InvalidArgumentException;
use Pact\Http\Methods;
use PHPUnit\Util\Json;

class MessageService extends AbstractService
{
    public const SERVICE_ENDPOINT = 'companies/%s/conversations/%s/messages';

    /**
     * @param array Route parameters validation method
     * @throws InvalidArgumentException
     * @todo move some part of this method outside of class
     */
    protected function validateRouteParams($params)
    {
        [$companyId, $conversationId] = $params;
        $this->validator->_($companyId<0, 'Id of company must be greater or equal than 0');
        $this->validator->_($conversationId<0, 'Id of conversation must be greater or equal than 0');
    }

    /**
     * Attachments must be integers - ids of uploaded in attachents (in Pact)
     * @param array|null Attachment list
     * @throws InvalidArgumentException
     */
    private function validateAttachments($attacments)
    {
        if ($attacments === null) {
            return;
        } 
        foreach ($attacments as $attacment) {
            $this->validator->_(!is_int($attacment), 'Attachment must be integer');
        }
    }

    /**
     * Get conversation messages
     * @link https://pact-im.github.io/api-doc/#get-conversation-messages
     * 
     * @param int id of the company
     * @param int id of the conversation
     * @param string Next page token geted from last request. 
     * Not valid or empty token return first page
     * @param int Number of elements per page. Default: 50
     * @param string We sort results by created_at. Change sorting direction. Avilable values: asc, desc. Default: asc.
     * @return Json|null
     */
    public function getMessages(int $companyId, int $conversationId, string $from=null, int $per=null, string $sort=null)
    {
        $this->validator->between($per, 1, 100, 'Number of fetching elements must be between 1 and 100.');
        $this->validator->sort($sort);

        $query = ['from' => $from, 'per' => $per, 'sort' => $sort];

        return $this->request(
            Methods::GET, 
            static::SERVICE_ENDPOINT, 
            [$companyId, $conversationId], 
            null,
            $query
        );
    }

    /**
     * @link https://pact-im.github.io/api-doc/#send-message
     * @param int id of the company
     * @param int id of the conversation
     * @param string Message text
     * @param array<int>|null attachments
     */
    public function sendMessage(int $companyId, int $conversationId, string $message = null, array $attachments = null)
    {
        $this->validateAttachments($attachments);
        
        $body = [
            'message' =>  $message,
            'attachments_ids' => $attachments
        ];
        
        return $this->request(
            Methods::POST,
            static::SERVICE_ENDPOINT,
            [$companyId, $conversationId],
            $body
        );
    }
}
