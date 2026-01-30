<?php

namespace Pact\Service;

use DateTimeInterface;
use Pact\Exception\InvalidArgumentException;
use Pact\Http\Methods;
use Pact\Service\AbstractService;

class ChannelService extends AbstractService
{
    public const SERVICE_ENDPOINT = 'companies/%s/channels';

    /**
     * @param array Route parameters validation method
     * @throws InvalidArgumentException
     */
    protected function validateRouteParams($params)
    {
        [$companyId] = $params;
        $this->validator->_($companyId<0, 'Id of company must be greater or equal than 0');
    }

    /**
     * This method returns all the company channels.
     * @link https://pact-im.github.io/api-doc/#get-all-channels
     *
     * @param int $companyId Id of the company
     * @param string $from Next page token geted from last request. Not valid or empty token return first page
     * @param int $per Number of elements per page. Default: 50
     * @param string $sort Change sorting direction (sorting by id). Avilable values: asc, desc. Default: asc.
     */
    public function getChannels(int $companyId, string $from = null, int $per = null, string $sort = null)
    {
        $this->validator->_(strlen($from)>255, 'Parameter 2 must be length less or equal 255');
        $this->validator->between($per, 1, 100);
        $this->validator->sort($sort);

        $query = [
            'from' => $from,
            'per' => $per,
            'sort_direction' => $sort
        ];
        return $this->request(Methods::GET, static::SERVICE_ENDPOINT, [$companyId], null, $query);
    }

    /**
     * Unified method that can create channel in company.
     * @link https://pact-im.github.io/api-doc/#create-new-channel
     * @note You can connect only one channel per one company for each provider.
     *       Contact with support if you want to use more than one channel
     *
     * @param int $companyId Id of the company
     * @param string $provider
     * @param array $parameters
     */
    public function createChannelUnified(int $companyId, string $provider, array $parameters = [])
    {
        $this->validator->_(strlen($provider)==0, 'Provider must be not empty string');
        $body = array_merge(
            ['provider' => $provider],
            $parameters
        );
        return $this->request(Methods::POST, static::SERVICE_ENDPOINT, [$companyId], $body);
    }

    /**
     * This method create a new channel in the company using token.
     * @link https://pact-im.github.io/api-doc/#create-new-channel
     * @note List of supported channels that can be created by token
     *       you can see in link above
     *
     * @param int $companyId Id of the company
     * @param string $provider (facebook, viber, vk, ...)
     * @param string $token
     * @return Json|null
     */
    public function createChannelByToken(int $companyId, string $provider, string $token)
    {
        $this->validator->_(strlen($token)==0, 'Token must be not empty string');
        return $this->createChannelUnified($companyId, $provider, ['token' => $token]);
    }

    /**
     * This method create a new channel for WhatsApp
     * @link https://pact-im.github.io/api-doc/#create-new-channel
     *
     * @param int $companyId Id of the company
     * @param DateTimeInterface $syncMessagesFrom Only messages created after will be synchronized
     * @param bool $doNotMarkAsRead Do not mark chats as read after synchronization
     * @return Json|null
     */
    public function createChannelWhatsApp(
        int $companyId,
        DateTimeInterface $syncMessagesFrom = null,
        bool $doNotMarkAsRead = null
    ) {
        if ($syncMessagesFrom !== null) {
            $syncMessagesFrom = $syncMessagesFrom->getTimestamp();
        }
        $body = [
            'sync_messages_from' => $syncMessagesFrom,
            'do_not_mark_as_read' => $doNotMarkAsRead
        ];
        return $this->createChannelUnified($companyId, 'whatsapp', $body);
    }

    /**
     * This method create a new channel for WhatsApp
     * @link https://pact-im.github.io/api-doc/#create-new-channel
     *
     * @param int $companyId Id of the company
     * @param string $login Instagram login
     * @param string $password Instagram passowrd
     * @param DateTimeInterface $syncMessagesFrom Only messages created after will be synchronized
     * @param bool $syncComments
     * @return Json|null
     */
    public function createChannelInstagram(
        int $companyId,
        string $login,
        string $password,
        DateTimeInterface $syncMessagesFrom = null,
        bool $syncComments = null
    ) {
        $this->validator->_(strlen($login)==0, 'Login must be not empty string');
        $this->validator->_(strlen($password)==0, 'Password must be not empty string');
        if ($syncMessagesFrom !== null) {
            $syncMessagesFrom = $syncMessagesFrom->getTimestamp();
        }
        $body = [
            'login' => $login,
            'password' => $password,
            'sync_messages_from' => $syncMessagesFrom,
            'sync_comments' => $syncComments
        ];
        return $this->createChannelUnified($companyId, 'instagram', $body);
    }

    /**
     * This method updates existing channel in the company
     * @link https://pact-im.github.io/api-doc/#update-channel
     *
     * @param int $companyId
     * @param int $conversationId
     * @param array $parameters
     * @return Json|null
     */
    public function updateChannel(int $companyId, int $conversationId, array $parameters = [])
    {
        $this->validator->_($conversationId<0, 'Id of conversation must be greater or equal than 0');
        return $this->request(
            Methods::PUT,
            static::SERVICE_ENDPOINT . '/%s',
            [$companyId, $conversationId],
            $parameters
        );
    }

    /**
     * This method updates instagramm channel
     * @link https://pact-im.github.io/api-doc/#update-channel
     *
     * @param int $companyId
     * @param int $conversationId
     * @param string $login Instagram login
     * @param string $password Instagram password
     * @return Json|null
     */
    public function updateChannelInstagram(
        int $companyId,
        int $conversationId,
        string $login,
        string $password
    ) {
        $this->validator->_(strlen($login)==0, 'Login must be not empty string');
        $this->validator->_(strlen($password)==0, 'Password must be not empty string');
        $body = [
            'login' => $login,
            'password' => $password
        ];
        return $this->updateChannel($companyId, $conversationId, $body);
    }

    /**
     * This method updates channels that using tokens to auth
     * @link https://pact-im.github.io/api-doc/#update-channel
     * @note List of supported channels that can be created by token
     *       you can see in link above
     *
     * @param int $companyId
     * @param int $conversationId
     * @param string $token
     * @return Json|null
     */
    public function updateChannelToken(int $companyId, int $conversationId, string $token)
    {
        $this->validator->_(strlen($token)==0, 'Token must be not empty string');
        return $this->updateChannel($companyId, $conversationId, ['token'=>$token]);
    }

    /**
     * Send first message to whatsapp (business)
     * @link https://pact-im.github.io/api-doc/#how-to-write-first-message-to-whatsapp-business
     *
     * @param int $companyId Id of the company
     * @param int $channelId Id of the conversation
     * @param string $phone
     * @param string $templateId
     * @param string $templateLanguage
     * @param array $templateParameters
     */
    public function sendWhatsAppTemplateMessage(
        int $companyId,
        int $channelId,
        string $phone,
        string $templateId,
        string $templateLanguage,
        array $templateParameters
    ) {

        $this->validator->_(strlen($phone) === 0, 'phone must be not empty string');
        $this->validator->_(strlen($templateId) === 0, 'templateId must be not empty string');
        $this->validator->_(strlen($templateLanguage) === 0, 'templateLanguage must be not empty string');
        $this->validator->_(!is_array($templateParameters), 'templateParameters must be array');

        $template = [
            'id' => $templateId,
            'language_code' => $templateLanguage,
            'parameters' => $templateParameters,
        ];

        $body = [
            'phone' => $phone,
            'template' => $template,
        ];

        return $this->request(
            Methods::POST,
            static::SERVICE_ENDPOINT . '/%s/conversations',
            [$companyId, $channelId],
            $body
        );
    }

    /**
     * Send first message to whatsapp
     * @link https://pact-im.github.io/api-doc/#how-to-write-first-message-to-whatsapp
     *
     * @param int $companyId Id of the company
     * @param int $channelId Id of the conversation
     * @param string $phone Phone number
     * @param string $message Message text
     */
    public function sendFirstWhatsAppMessage(int $companyId, int $channelId, string $phone, string $message)
    {
        $this->validator->_(strlen($phone) === 0, 'Phone must be not empty string');

        $body = [
            'phone' => $phone,
            'message' => $message,
        ];

        return $this->request(
            Methods::POST,
            static::SERVICE_ENDPOINT . '/%s/conversations',
            [$companyId, $channelId],
            $body
        );
    }

    /**
     * Method deletes (disables) the channel
     * @link https://pact-im.github.io/api-doc/#delete-channel
     *
     * @param int $companyId Id of the company
     * @param int $channelId Id of the conversation
     */
    public function deleteChannel(int $companyId, int $channelId)
    {
        return $this->request(
            Methods::DELETE,
            static::SERVICE_ENDPOINT . '/%s',
            [$companyId, $channelId]
        );
    }

    /**
     * @link https://pact-im.github.io/api-doc/#request-code-instagram-only
     *
     * @param int $companyId Id of the compnay
     * @param int $channelId Id of the channel
     * @param array $parameters
     * @return Json|null
     */
    public function requestChannelCode(
        int $companyId,
        int $channelId,
        array $parameters
    ) {
        return $this->request(
            Methods::POST,
            static::SERVICE_ENDPOINT . '/%s/request_code',
            [$companyId, $channelId],
            $parameters
        );
    }

    /**
     * @link https://pact-im.github.io/api-doc/#confirm-code-instagram-only
     *
     * @param int $companyId Id of the compnay
     * @param int $channelId Id of the channel
     * @param array $parameters
     * @return Json|null
     */
    public function confirmChannelCode(
        int $companyId,
        int $channelId,
        array $parameters
    ) {
        return $this->request(
            Methods::POST,
            static::SERVICE_ENDPOINT . '/%s/confirm',
            [$companyId, $channelId],
            $parameters
        );
    }
}
