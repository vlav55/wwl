<?php

namespace Pact\Tests\Service;

use DateTime;
use DateTimeInterface;
use Pact\Exception\InvalidArgumentException;
use Pact\Http\Methods;
use Pact\Service\ChannelService;

class ChannelServiceTest extends ServiceTestCase
{
    protected static $serviceClass = ChannelService::class;

    /** @var ChannelService */
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

    public function test_get_channels_returns_valid_json()
    {
        $this->expectedMethod = Methods::GET;
        $this->expectedUrl = $this->formatEndpoint('', [$this->companyId]);
            
        $response = $this->service->getChannels(
            $this->companyId
        );
        $this->assertSame('ok', $response->status);
    }

    /**
     * @dataProvider dataset_get_channels_with_valid_sort_returns_valid_json
     */
    public function test_get_channels_with_valid_sort_returns_valid_json($sort)
    {
        $this->expectedMethod = Methods::GET;
        $query = ['sort_direction' => $sort];
        $this->expectedUrl = $this->formatEndpoint('', [$this->companyId], $query);
        $response = $this->service->getChannels(
            $this->companyId,
            null,
            null,
            $sort
        );
        $this->assertSame('ok', $response->status);
    }

    public function dataset_get_channels_with_valid_sort_returns_valid_json()
    {
        return [
            ['asc'], ['desc']
        ];
    }

    public function test_get_channels_with_invalid_sort_throws_invalid_argument()
    {
        $sort = 'asdf';
        $query = ['sort_direction' => $sort];
        $this->expectedMethod = Methods::GET;
        $this->expectedUrl = $this->formatEndpoint('', [$this->companyId], $query);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Sorting parameter must be "asc" or "desc". "'. $sort .'" given');
        $response = $this->service->getChannels(
            $this->companyId,
            null,
            null,
            $sort
        );
    }

    public function test_create_channel_with_empty_provider_throws_invalid_argument()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Provider must be not empty string');
        $response = $this->service->createChannelUnified(
            $this->companyId,
            ''
        );
    }

    /**
     * @dataProvider dataset_create_channel_by_token_returns_valid_json
     */
    public function test_create_channel_by_token_returns_valid_json($provider, $superSecretProviderToken0w0)
    {
        $this->expectedMethod = Methods::POST;
        $this->expectedUrl = $this->formatEndpoint('', [$this->companyId]);
        
        $this->setUpMocks(
            $this->callback(function($body) use ($superSecretProviderToken0w0, $provider)
            {
                parse_str($body, $body);
                $this->assertIsArray($body);
                $this->assertArrayHasKey('provider', $body);
                $this->assertSame($body['provider'], $provider);

                $this->assertArrayHasKey('token', $body);
                $this->assertSame($body['token'], $superSecretProviderToken0w0);
                return true;
            })
        );

        $response = $this->service->createChannelByToken(
            $this->companyId,
            $provider,
            $superSecretProviderToken0w0
        );
        $this->assertSame('ok', $response->status);
    }

    public function dataset_create_channel_by_token_returns_valid_json()
    {
        return [
            ['facebook', 'super_secret_facebook_token'],
            ['vkontakte', 'super_secret_vkontakte_token'],
            ['telegram', 'super_secret_telegram_token'],
            ['viber', 'super_secret_viber_token']
        ];
    }

    public function test_create_channel_by_token_with_empty_token_throws_invalid_argument()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Token must be not empty string');
        $response = $this->service->createChannelByToken(
            $this->companyId,
            'some-provider',
            ''
        );
    }

    /**
     * @dataProvider dataset_create_channel_whatsapp
     */
    public function test_create_channel_whatsapp(?DateTimeInterface $syncMessagesFrom=null, ?bool $doNotMarkAsRead = null)
    {
        $provider = 'whatsapp';
        $this->expectedMethod = Methods::POST;
        $this->expectedUrl = $this->formatEndpoint('', [$this->companyId]);

        $this->setUpMocks(
            $this->callback(function($body) use ($provider, $syncMessagesFrom, $doNotMarkAsRead)
            {
                parse_str($body, $body);
                $this->assertArrayHasKey('provider', $body);
                $this->assertEquals($body['provider'], $provider);

                if ($syncMessagesFrom !== null) {
                    $this->assertArrayHasKey('sync_messages_from', $body);
                    $this->assertEquals($body['sync_messages_from'], $syncMessagesFrom->getTimestamp());
                }
                if ($doNotMarkAsRead !== null) {
                    $this->assertArrayHasKey('do_not_mark_as_read', $body);
                    $this->assertEquals($body['do_not_mark_as_read'], $doNotMarkAsRead);
                }

                return true;
            })
        );

        $response = $this->service->createChannelWhatsApp(
            $this->companyId,
            $syncMessagesFrom,
            $doNotMarkAsRead
        );
        $this->assertSame('ok', $response->status);
    }

    public function dataset_create_channel_whatsapp()
    {
        return [
            [new DateTime(), null],
            [null, true],
        ];
    }

    /**
     * @dataProvider dataset_create_channel_whatsapp
     */
    public function test_create_channel_instagram(?DateTimeInterface $syncMessagesFrom=null, ?bool $syncComments = null)
    {
        $provider = 'instagram';
        $login = 'qwerty';
        $passw = 'azerty';
        $this->expectedMethod = Methods::POST;
        $this->expectedUrl = $this->formatEndpoint('', [$this->companyId]);

        $this->setUpMocks(
            $this->callback(function($body) use ($provider, $login, $passw, $syncMessagesFrom, $syncComments)
            {
                parse_str($body, $body);
                $this->assertArrayHasKey('provider', $body);
                $this->assertEquals($body['provider'], $provider);

                $this->assertArrayHasKey('login', $body);
                $this->assertEquals($body['login'], $login);

                $this->assertArrayHasKey('password', $body);
                $this->assertEquals($body['password'], $passw);

                if ($syncMessagesFrom !== null) {
                    $this->assertArrayHasKey('sync_messages_from', $body);
                    $this->assertEquals($body['sync_messages_from'], $syncMessagesFrom->getTimestamp());
                }
                if ($syncComments !== null) {
                    $this->assertArrayHasKey('sync_comments', $body);
                    $this->assertEquals($body['sync_comments'], $syncComments);
                }

                return true;
            })
        );

        $response = $this->service->createChannelInstagram(
            $this->companyId,
            $login,
            $passw,
            $syncMessagesFrom,
            $syncComments
        );
        $this->assertSame('ok', $response->status);
    }

    public function dataset_create_channel_instagram()
    {
        return [
            [new DateTime(), null],
            [null, true],
        ];
    }

    public function test_create_channel_instagram_with_empty_login_throws_invalid_argument()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Login must be not empty string');

        $response = $this->service->createChannelInstagram(
            $this->companyId,
            '',
            'pass'
        );
    }

    public function test_create_channel_instagram_with_empty_password_throws_invalid_argument()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Password must be not empty string');

        $response = $this->service->createChannelInstagram(
            $this->companyId,
            'login',
            ''
        );
    }

    public function test_update_channel()
    {
        $this->expectedMethod = Methods::PUT;
        $this->expectedUrl = $this->formatEndpoint('/%s', [$this->companyId, $this->conversationId]);

        $response = $this->service->updateChannel(
            $this->companyId,
            $this->conversationId,
            []
        );
        $this->assertSame('ok', $response->status);
    }

    public function test_update_channel_with_negative_id_throws_invalid_argument()
    {
        $this->expectedMethod = Methods::PUT;
        $this->expectedUrl = $this->formatEndpoint('/%s', [$this->companyId, $this->conversationId]);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Id of conversation must be greater or equal than 0');

        $response = $this->service->updateChannel(
            $this->companyId,
            -1,
            []
        );
    }

    public function test_update_channel_instagram_with_empty_login_throws_invalid_argument()
    {
        $this->expectedMethod = Methods::PUT;
        $this->expectedUrl = $this->formatEndpoint('/%s', [$this->companyId, $this->conversationId]);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Login must be not empty string');

        $this->service->updateChannelInstagram(
            $this->companyId,
            $this->conversationId,
            '',
            'pass'
        );
    }

    public function test_update_channel_instagram_with_empty_pass_throws_invalid_argument()
    {
        $this->expectedMethod = Methods::PUT;
        $this->expectedUrl = $this->formatEndpoint('/%s', [$this->companyId, $this->conversationId]);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Password must be not empty string');

        $this->service->updateChannelInstagram(
            $this->companyId,
            $this->conversationId,
            'login',
            ''
        );
    }

    public function test_update_channel_by_empty_token_throws_invalid_argument()
    {
        $this->expectedMethod = Methods::PUT;
        $this->expectedUrl = $this->formatEndpoint('/%s', [$this->companyId, $this->conversationId]);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Token must be not empty string');

        $this->service->updateChannelToken(
            $this->companyId,
            $this->conversationId,
            ''
        );
    }


    public function test_send_first_message_whatsapp()
    {
        $phone = '88005553535';
        $message = 'Hello world!';
        // Send first message for whatsapp uses differend endpoint
        $endpoint = 'companies/%s/channels/%s/conversations';
        $this->expectedMethod = Methods::POST;
        $this->expectedUrl = $this->service->formatEndpoint(
                $endpoint, 
                [$this->companyId, $this->conversationId], 
                []
            );

        $this->setUpMocks(
            $this->callback(function($body) use ($phone, $message) {
                parse_str($body, $body);
                $this->assertArrayHasKey('phone', $body);
                $this->assertSame($body['phone'], $phone);

                $this->assertArrayHasKey('message', $body);
                $this->assertSame($body['message'], $message);
                return true;
            })
        );
        
        $response = $this->service->sendFirstWhatsAppMessage(
            $this->companyId,
            $this->conversationId,
            $phone,
            $message
        );
        $this->assertSame('ok', $response->status);
    }

    public function test_send_first_message_whatsapp_with_empty_phone_throws_invalid_argument()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Phone must be not empty string');

        // Send first message for whatsapp uses differend endpoint
        $endpoint = 'companies/%s/channels/%s/conversations';
        $this->expectedMethod = Methods::POST;
        $this->expectedUrl = $this->service->formatEndpoint(
                $endpoint, 
                [$this->companyId, $this->conversationId], 
                []
            );
        
        $response = $this->service->sendFirstWhatsAppMessage(
            $this->companyId,
            $this->conversationId,
            '',
            'Hello world!'
        );
    }

    /**
     * @dataProvider dataset_send_first_message_whatsapp_with_templates_should_be_ok
     */
    public function test_send_first_message_whatsapp_with_template(
        string $phone,
        string $templateId,
        string $templateLanguage,
        array $templateParameters
    ) {
        // Send first message for whatsapp uses differend endpoint
        $endpoint = 'companies/%s/channels/%s/conversations';
        $this->expectedMethod = Methods::POST;
        $this->expectedUrl = $this->service->formatEndpoint(
            $endpoint, 
            [$this->companyId, $this->conversationId], 
            []
        );
        
        $response = $this->service->sendWhatsAppTemplateMessage(
            $this->companyId,
            $this->conversationId,
            $phone,
            $templateId,
            $templateLanguage,
            $templateParameters
        );

        $this->assertSame('ok', $response->status);
    }

    public function dataset_send_first_message_whatsapp_with_templates_should_be_ok()
    {
        return [
            'Common request with single parameter' => [
                '+78005553535',
                'welcomeTemplate',
                'ru',
                ['имя'],
            ],
            'Common request with multiple parameters' => [
                '+78005553535',
                'welcomeTemplate',
                'ru',
                ['имя', 'город'],
            ],
            'Common request with no template parameters' => [
                '+78005553535',
                'welcomeTemplate',
                'ru',
                [],
            ],
        ];
    }

    /**
     * @dataProvider dataset_send_first_message_whatsapp_with_wrong_template_settings_throws_invalid_argument
     */
    public function test_send_first_message_whatsapp_business_with_wrong_template_settings_throws_invalid_argument(
        string $phone,
        string $templateId,
        string $templateLanguage,
        array $templateParameters,
        string $expectedExceptionMessage
    ) {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        // Send first message for whatsapp uses differend endpoint
        $endpoint = 'companies/%s/channels/%s/conversations';
        $this->expectedMethod = Methods::POST;
        $this->expectedUrl = $this->service->formatEndpoint(
                $endpoint, 
                [$this->companyId, $this->conversationId], 
                []
            );
        
        $response = $this->service->sendWhatsAppTemplateMessage(
            $this->companyId,
            $this->conversationId,
            $phone,
            $templateId,
            $templateLanguage,
            $templateParameters
        );
    }

    public function dataset_send_first_message_whatsapp_with_wrong_template_settings_throws_invalid_argument()
    {
        return [
            'Empty phone' => [
                '',
                'welcomeTemplate',
                'ru',
                ['имя'],
                'phone must be not empty string'
            ],
            'Empty templateId' => [
                '+78005553535',
                '',
                'ru',
                ['имя'],
                'templateId must be not empty string'
            ],
            'Empty language' => [
                '+78005553535',
                'welcomeTemplate',
                '',
                ['имя'],
                'templateLanguage must be not empty string'
            ],
        ];
    }

    public function test_delete_channel()
    {
        $this->expectedMethod = Methods::DELETE;
        $this->expectedUrl = $this->formatEndpoint('/%s', [$this->companyId, $this->conversationId]);

        $response = $this->service->deleteChannel(
            $this->companyId,
            $this->conversationId
        );
        $this->assertSame('ok', $response->status);
    }

    public function test_request_channel_code()
    {
        $this->expectedMethod = Methods::POST;
        $this->expectedUrl = $this->formatEndpoint('/%s/request_code', [$this->companyId, $this->conversationId]);

        $response = $this->service->requestChannelCode(
            $this->companyId,
            $this->conversationId,
            []
        );
        $this->assertSame('ok', $response->status);
    }

    public function test_confirm_channel_code()
    {
        $this->expectedMethod = Methods::POST;
        $this->expectedUrl = $this->formatEndpoint('/%s/confirm', [$this->companyId, $this->conversationId]);

        $response = $this->service->confirmChannelCode(
            $this->companyId,
            $this->conversationId,
            []
        );
        $this->assertSame('ok', $response->status);
    }
}
