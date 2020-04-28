<?php

use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Event;
use App\Services\EmailServices;
use App\Repositories\EmailRepository;
use App\Services\Interfaces\IMail;
use App\Services\MailJetEmailService;
use App\Services\SendGridEmailService;
use App\Jobs\EmailJob;
use App\Events\EmailSentEvent;
use App\Exceptions\SendMailException;

/**
 * Class SendEmailTest
 */
class SendEmailTest extends TestCase
{
    /**
     *
     */
    const API_TOKEN = '5d14454300cad0083743747691f56b40';
    /**
     *
     */
    const EXPECTED_CODE = 200;

    public function setUp(): void
    {
        parent::setUp();

        $this->mockRepo = $this->createMock(EmailRepository::class);
        $this->mockMail = $this->createMock(IMail::class);

        $this->mailer1 = $this->getMockBuilder(MailJetEmailService::class)
            ->setMethods(['send', 'getResponse'])
            ->getMock();

        $this->mailer2 = $this->getMockBuilder(SendGridEmailService::class)
            ->setMethods(['send', 'getResponse'])
            ->getMock();
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->get('/');

        $this->assertEquals(
            $this->app->version(), $this->response->getContent()
        );
    }

    /** test Entry Point of the application working or not
     * @return void
     */
    public function testVersion()
    {
        $this->get('/api/');
        $content = json_decode($this->response->getContent());

        $this->assertEquals(
            self::EXPECTED_CODE, $this->response->getStatusCode()
        );
        $this->assertEquals('v1', $content->API_Version);
    }

    /**
     *  Test get-emails route functionality and expected result
     *  Commented as don't want to push every time while calling test.
     * @return void
     */
    public function testGetEmails()
    {

        $response = $this->json('Get', '/api/get-emails', [],
            ['Api-Auth-Bypass-Token' => self::API_TOKEN]);
        $content  = json_decode($this->response->getContent());
        $response->assertResponseOk();
        $this->assertEquals('success', $content->status);


    }


    /**
     * Test Send-email route and expected result
     * @return void
     */
    public function testPushToQueue()
    {
        $request = [
            'to'      =>
                [
                    [
                        'email' => 'sonam.bhatnagar5@gmail.com',
                        'name'  => 'sonam B',
                    ]
                ],
            'subject' => 'Mock You',
            'content' => 'Dear Customer! its a mock content!',
            'type'    => 'text/plain',
        ];
        Queue::fake();
        Queue::assertNothingPushed();
        $response = $this->json('POST', '/api/send-email', $request,
            ['Api-Auth-Bypass-Token' => self::API_TOKEN]);
        $content  = json_decode($this->response->getContent());
        Queue::assertPushed(EmailJob::class);

        $response->assertResponseOk();

        // Test emails request pushed to queue.
        $this->assertEquals('true', $content->pushToQueue);
    }


    /**
     * Test Invalid input json  body request expected results
     *
     * @return void
     */
    public function testValidateTos()
    {
        $request = [
            'tos'     =>
                [
                    [
                        'email' => 'sonam.bhatnagar5@gmail.com',
                        'name'  => 'sonam B',
                    ]
                ],
            'subject' => 'Mock You',
            'content' => 'Dear Customer! its a mock content!',
            'type'    => 'text/plain',
        ];
        $this->json('POST', '/api/send-email', $request,
            ['Api-Auth-Bypass-Token' => self::API_TOKEN]);
        $content = json_decode($this->response->getContent());
        $this->assertNotEquals(
            self::EXPECTED_CODE, $this->response->getStatusCode()
        );

        $this->assertEquals('The to field is required.', $content->errors->to[0]);
    }

    /**
     * Test missing required json body request expected results
     * @return void
     */
    public function testValidateSubject()
    {
        $request = [
            'to'      =>
                [
                    [
                        'email' => 'sonam.bhatnagar5@gmail.com',
                        'name'  => 'sonam B',
                    ]
                ],
            'content' => 'Dear Customer! its a mock content!',
            'type'    => 'text/plain',
        ];
        $this->json('POST', '/api/send-email', $request,
            ['Api-Auth-Bypass-Token' => self::API_TOKEN]);
        $content = json_decode($this->response->getContent());
        $this->assertNotEquals(
            self::EXPECTED_CODE, $this->response->getStatusCode()
        );
        $this->assertEquals('The subject field is required.', $content->errors->subject[0]);
    }

    /**
     *  Test missing required json body request expected results
     * @return void
     */
    public function testValidateContent()
    {
        $request = [
            'to'      =>
                [
                    [
                        'email' => 'sonam.bhatnagar5@gmail.com',
                        'name'  => 'sonam B',
                    ]
                ],
            'subject' => 'Mock You',
            'type'    => 'text',
        ];
        $this->json('POST', '/api/send-email', $request,
            ['Api-Auth-Bypass-Token' => self::API_TOKEN]);
        $content = json_decode($this->response->getContent());
        $this->assertNotEquals(
            self::EXPECTED_CODE, $this->response->getStatusCode()
        );

        $this->assertEquals('The content field is required.', $content->errors->content[0]);
    }

    /**
     *  Test Invalid type key json body request expected results
     * @return void
     */
    public function testValidateContentType()
    {
        $request = [
            'to'      =>
                [
                    [
                        'email' => 'sonam.bhatnagar5@gmail.com',
                        'name'  => 'sonam B',
                    ]
                ],
            'subject' => 'Mock You',
            'content' => 'Dear passenger! May the delivery force be with you!',
            'type'    => 'text',
        ];

        $this->json('POST', '/api/send-email', $request,
            ['Api-Auth-Bypass-Token' => self::API_TOKEN]);
        $content = json_decode($this->response->getContent());
        $this->assertNotEquals(
            self::EXPECTED_CODE, $this->response->getStatusCode()
        );

        $this->assertEquals('Please Provide a Valid Type of Content either text/html or text/plain',
            $content->message);
    }


    /**
     * Test SendEmail() code by mocking  Mail provider to get overridden send() response as we don't
     * want to call actual api call and get the email.
     * @return void
     * @throws Exception
     */
    public function testSendEmail()
    {
        $request = [
            'to'      =>
                [
                    [
                        'email' => 'sonam.bhatnagar5@gmail.com',
                        'name'  => 'casanova',
                    ]
                ],
            'subject' => 'Test Mock!!!!',
            'content' => 'Dear Customer! its a mock content!',
            'type'    => 'text/plain',
        ];

        $this->mailer1->method('send')->willReturn(true);

        $this->mailer2->method('send')->willReturn(true);

        $service = new \App\Services\EmailServices($this->mockRepo, $this->mockMail, [$this->mailer1, $this->mailer2]);

        Event::fake();
        $response = $service->sendEmail($request);
        Event::assertDispatched(EmailSentEvent::class);
        $this->assertNull($response);
    }

    /**
     * Test SendEmail() Exception code by mocking  Mail provider to get overridden send() response as we don't
     * want to call actual api call and get the email.
     * @return void
     * @throws Exception
     */
    public function testSendEmailException()
    {
        $request = [
            'to'      =>
                [
                    [
                        'email' => 'sonam.bhatnagar5@gmail.com',
                        'name'  => 'casanova',
                    ]
                ],
            'subject' => 'Test Mock!!!!',
            'content' => 'Dear Customer! its a mock content!',
            'type'    => 'text/plain',
        ];

        $this->mailer1->method('send')->willReturn(false);

        $this->mailer2->method('send')->willReturn(false);

        Event::fake();
        $service = new EmailServices($this->mockRepo, $this->mockMail, [$this->mailer1, $this->mailer2]);
        $this->expectException(SendMailException::class);
        $service->sendEmail($request);
        Event::assertDispatched(EmailSentEvent::class);
    }


}
