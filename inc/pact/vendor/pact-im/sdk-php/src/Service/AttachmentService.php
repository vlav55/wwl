<?php

namespace Pact\Service;

use Pact\Exception\FileNotFoundException;
use Pact\Exception\InvalidArgumentException;
use Pact\Http\Factory;
use Pact\Http\Methods;
use Psr\Http\Message\StreamInterface;

class AttachmentService extends AbstractService
{
    public const SERVICE_ENDPOINT = 'companies/%s/conversations/%s/messages/attachments';

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
     * @param Resource|StreamInterface file to upload
     */
    private function attachLocalFile(int $companyId, int $conversationId, $file)
    {
        $body = Factory::multipartStreamBuilder();
        $body->addResource('file', $file);

        $boundary = $body->getBoundary();
        return $this->request(
            Methods::POST,
            static::SERVICE_ENDPOINT,
            [$companyId, $conversationId],
            $body->build(),
            [],
            ['Content-Type' => 'multipart/form-data; boundary="'.$boundary.'"']
        );
    }

    private function attachRemoteFile(int $companyId, int $conversationId, $url)
    {
        $body = ['file_url' => $url];
        return $this->request(
            Methods::POST,
            static::SERVICE_ENDPOINT,
            [$companyId, $conversationId],
            $body
        );
    }

    /**
     * Сreates an attachment which can be sent in message
     * @link https://pact-im.github.io/api-doc/#upload-attachments
     * 
     * @param int id of the company
     * @param int id of the conversation
     * @param Resource|StreamInterface|string file to upload
     * @return Json|null
     */
    public function uploadFile(int $companyId, int $conversationId, $file)
    {
        if (!is_string($file) && !is_resource($file) && !is_a($file, StreamInterface::class)) {
            $msg = 'Attachment must be string or resource or StreamInterface';
            throw new InvalidArgumentException($msg);
        }

        if (is_string($file)) {
            if (filter_var($file, FILTER_VALIDATE_URL)) {
                return $this->attachRemoteFile($companyId, $conversationId, $file);
            } 
            $file = realpath($file);
            if ($file === false) {
                throw new FileNotFoundException("File '$file' not found");
            } 
            $file = fopen($file, 'r');
        }

        return $this->attachLocalFile($companyId, $conversationId, $file);
    }
}
