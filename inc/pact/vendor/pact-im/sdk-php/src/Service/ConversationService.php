<?php

namespace Pact\Service;

use Pact\Http\Methods;

class ConversationService extends AbstractService
{
    public const SERVICE_ENDPOINT = '/companies/%s/conversations';

    /**
     * @param array Route parameters validation method
     * @throws InvalidArgumentException
     * @todo move some part of this method outside of class
     */
    protected function validateRouteParams($params)
    {
        $this->validator->_($params[0]<0, 'Id of company must be greater or equal than 0');

        if (key_exists(1, $params) && is_int($params[1])) {
            $this->validator->_($params[1]<0, 'Id of conversation must be greater or equal than 0');
        }
    }

    /**
     * Gets all conversations
     * @link https://pact-im.github.io/api-doc/#get-all-conversations
     * 
     * @param int id of the company
     * @param string Next page token geted from last request. 
     * Not valid or empty token return first page
     * @param int Number of elements per page. Min 1, max 100, default: 50
     * @param string Change sorting direction. Available values: asc, desc. Default: asc.
     * @return Json|null
     */
    public function getConversations(int $companyId, ?string $from=null, ?int $per=null, ?string $sort=null)
    {
        $this->validator->_(strlen($from)>255, 'From identificator length must be less or equal 255');
        $this->validator->between($per, 1, 100);
        $this->validator->sort($sort);

        $query = ['from' => $from, 'per' => $per, 'sort_direction' => $sort];

        return $this->request(Methods::GET, static::SERVICE_ENDPOINT, [$companyId], null, $query);
    }

    /**
     * Creates new conversation
     * This endpoint creates conversation in the company
     * @link https://pact-im.github.io/api-doc/#create-new-conversation
     * 
     * @param int id of the company
     * @param string conversation provider (e.g. "whatsapp")
     * @param array provider related params (e.g. for whatsapp is ["phone": "<phonenum>"])
     * @return Json|null
     */
    public function createConversation(int $companyId, string $provider, array $providerParams)
    {
        $body = array_merge(["provider" => $provider], $providerParams);
        return $this->request(
            Methods::POST, 
            static::SERVICE_ENDPOINT, 
            [$companyId],
            $body
        );
    }

    /**
     * Retrives conversation details from server
     * @link https://pact-im.github.io/api-doc/#get-conversation-details
     * 
     * @param int id of company
     * @param int id of conversation
     * @return Json|null
     */
    public function getDetails(int $companyId, int $conversationId) 
    {
        return $this->request(
            Methods::GET,
            static::SERVICE_ENDPOINT . '/%s',
            [$companyId, $conversationId]
        );
    }

    /**
     * Update assignee for conversation
     * This endpoint update assignee of conversation in the company using whatsapp channel
     * @link https://pact-im.github.io/api-doc/#update-assignee-for-conversation
     * 
     * @param int id of company
     * @param int id of conversation
     * @param int id of user
     * @return Json|null
     */
    public function updateAssignee(int $companyId, int $conversationId, int $assigneeId) 
    {
        $this->validator->_($assigneeId <= 0, 'Assignee id must be greater than 0');
        
        $body = ["assignee_id" => $assigneeId];
        return $this->request(
            Methods::PUT,
            static::SERVICE_ENDPOINT . '/%s/assign',
            [$companyId, $conversationId],
            $body
        );
    }
}
