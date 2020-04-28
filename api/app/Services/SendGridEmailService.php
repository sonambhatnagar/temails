<?php
/**
 *
 * @author           sbhatnagar
 * @date             6/1/19
 */

namespace App\Services;

use App\Services\Interfaces\IMail;
use App\Services\Interfaces\IMailer;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;


/**
 * Class SendGridEmailService
 * @package App\Services
 */
class SendGridEmailService implements IMailer
{
    /**
     *
     */
    const SEND_URI = "mail/send";
    /**
     *
     */
    const METHOD = 'POST';
    /**
     *
     */
    const TYPE_HTML = 'HTML';
    /**
     * @var mixed
     */
    protected $config;

    /**
     * @var
     */
    protected $response;
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
     * SendGridEmailService constructor.
     */
    public function __construct()
    {
        $this->config = config("mail.mailers.sendGrid");
    }

    /**
     *
     */
    private function setUri(): void
    {
        $this->url = $this->config['baseUri'] . self::SEND_URI;
        Log::info('EMail Service Provider URI : ' . $this->url);
    }


    /**
     * @return void
     */
    public function setHeaders(): void
    {
        $this->bodyData['headers'] = [
            'authorization' => 'Bearer ' . $this->config['apiKey'],
            'Content-Type'  => 'application/json'
        ];
    }

    /**
     * @param IMail $mail
     *
     * @return void
     */
    private function setBody(IMail $mail): void
    {
        $body['from']               = $mail->getSender();
        $body['subject']            = $mail->getSubject();
        $body['content'][]          = ['type' => $mail->getBody()['type'], 'value' => $mail->getBody()['content']];
        $body['personalizations'][] = $mail->getReceivers();
        $this->bodyData['body']     = json_encode($body);
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
            Log::info('Api-Transaction-Id' . ':' . $response->getHeader('X-Message-Id')[0]);
            $code   = $response->getStatusCode();
            $result = $response->getReasonPhrase();
            Log::info('Status Code ' . $code);
            Log::info('Result Text ' . $result);

        } catch (\Exception $e) {
            Log::error('Error occurred in '. SendGridEmailService::class . ' while sending email '. $e->getCode().' :' . json_encode($e->getMessage()));
            return false;
        }

        $this->response = ['status' => $result, 'code' => $code, 'provider' => SendGridEmailService::class];

        return true;
    }


}