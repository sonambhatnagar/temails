<?php

/**
 *
 * @author           sbhatnagar
 * @date             6/1/19
 */

namespace App\Services;

use App\Events\EmailSentEvent;
use App\Exceptions\SendMailException;
use App\Repositories\EmailRepository;
use App\Services\Interfaces\IEmailServices;
use App\Services\Interfaces\IMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


/**
 * Class EmailServices
 * @package App\Services
 */
class EmailServices implements IEmailServices
{
    /**
     * @var EmailRepository
     */
    private $emailRepository;
    /**
     * @var mixed
     */
    private $config;
    /**
     * @var array
     */
    private $mailProvider = [];
    /**
     * @var IMail
     */
    private $mail;

    /**
     *
     */
    const SENT_STATUS = 'sent';
    /**
     *
     */
    const FAILED_STATUS = 'failed';


    /**
     * EmailServices constructor.
     *
     * @param EmailRepository $emailRepository
     * @param IMail           $mail
     * @param array           $mailProvider (IMailer type)
     */
    public function __construct(EmailRepository $emailRepository, IMail $mail, array $mailProvider)
    {
        $this->emailRepository = $emailRepository;
        $this->mail            = $mail;
        $this->config          = config('mail.mailers');
        $this->mailProvider    = $mailProvider;
    }

    /**
     * For Now we are returning one week data from the system.
     *
     * @param string $status
     *
     * @return object
     */
    public function getEmails(string $status = null): object
    {
        $date = Carbon::now()->setTimezone('Europe/Amsterdam');
        // Mock data if you get empty data from Repo.
        $data      = (object)['status' => 'success', 'data' => ['message' => "No Emails available in System."]];
        $emailData = $this->emailRepository->getEmailsDataWithInWeek($date, $status);

        return (!$emailData) ? $data : $emailData;
    }


    /**
     * @param array $data
     *
     * @return void
     * @throws \Exception
     */
    public function sendEmail(array $data): void
    {
        $this->mail->setTo($data['to']);

        if (isset($data['bcc'])) {
            $this->mail->setBcc($data['bcc']);
        }

        // add cc if passed in the payloads array.
        if (isset($data['cc'])) {
            $this->mail->setCc($data['cc']);
        }

        // Add subject to the email.
        $this->mail->setSubject($data['subject']);

        // Set Content and its type, currently supported are plain text and html.
        $this->mail->setBody($data['content'], $data['type']);


        foreach ($this->mailProvider as $mailer) {
            if ($mailer->send($this->mail) === true) {
                $response = $mailer->getResponse();
                event(new EmailSentEvent($data, $response, self::SENT_STATUS));
                return;
            }
        }

        event(new EmailSentEvent($data, ['message' => 'Sending mail failed through all providers'],
            self::FAILED_STATUS));

        Log::error('Sending mail failed through all providers');
        throw new SendMailException('Sending mail failed through all providers');

    }

}