<?php

/**
 *
 * @author           sbhatnagar
 * @date             6/1/19
 */

namespace App\Services;

use App\Services\Interfaces\IMail;
use App\Services\Interfaces\IMailer;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

/**
 * Class MailJetEmailService
 * @package App\Services
 */
class MailJetEmailService implements IMailer
{
    /**
     *
     */
    const SEND_URI = "send";
    /**
     *
     */
    const METHOD = 'POST';
    /**
     *
     */
    const TYPE_HTML = 'HTML';

    /**
     * @var
     */
    protected $response;
    /**
     * @var mixed
     */
    protected $config;
    /**
     * @var
     */
    protected $code;

    /**
     * @var
     */
    private $url;
    /**
     * @var array
     */
    private $bodyData = [];


    /**
     * MailJetEmailService constructor.
     */
    public function __construct()
    {
        $this->config = config("mail.mailers.mailJet");
    }

    /**
     * @return array
     */
    public function getResponse(): array
    {
        return $this->response;
    }

    /**
     * @param IMail $mail
     *
     * @return bool
     * @throws \Exception
     */
    public function send(IMail $mail): bool
    {
        $this->setUri();
        $this->setHeaders();
        $mail->setFrom($this->config['fromEmail'], $this->config['fromName']);
        $this->setBody($mail);
        $client = new Client();

        try {

            Log::info('Request Body' . ':' . json_encode($this->bodyData));
            $response = $client->request(self::METHOD, $this->url, $this->bodyData);

            $response->getHeaders();

            Log::info('Api-Transaction-Id' . ':' . $response->getHeader('X-Mj-Request-Guid')[0]);
            $code   = $response->getStatusCode();
            $result = $response->getReasonPhrase();
            Log::info('Status Code ' . $code);
            Log::info('Result Text ' . $result);

        } catch (\Exception $e) {
            Log::error('Error occurred in '. MailJetEmailService::class . ' while sending email '. $e->getCode().' :' . json_encode($e->getMessage()));
            return false;
        }

        $this->response = ['status' => $result, 'code' => $code, 'provider' => MailJetEmailService::class];

        return true;
    }

    /**
     * @return void
     */
    private function setUri(): void
    {
        $this->url = $this->config['baseUri'] . self::SEND_URI;
        Log::info('EMail Service Provider URI : ' . $this->url);
    }

    /**
     * @return void
     */
    private function setHeaders(): void
    {
        $this->bodyData['headers'] = ['Content-Type' => 'application/json'];
        $this->bodyData['auth']    = [$this->config['apiKey'], $this->config['apiSecret']];
    }


    /**
     * @param IMail $mail
     *
     * @return void
     */
    private function setBody(IMail $mail): void
    {

        $body['Messages']       = [
            [
                'From'     => $mail->getSender(),
                "Subject"  => $mail->getSubject(),
                'TextPart' => $mail->getBody()['content'],
                "HTMLPart" => $mail->getBody()['content']
            ] + $mail->getReceivers()
        ];
        $this->bodyData['body'] = json_encode($body);
    }


}