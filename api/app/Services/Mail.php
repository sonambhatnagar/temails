<?php

/**
 *
 * @author           sbhatnagar
 * @date             6/1/19
 */

namespace App\Services;

use App\Services\Interfaces\IMail;


/**
 * Class Mail
 * @package App\Services
 */
class Mail implements IMail
{

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
     * @var array
     */
    private $sender = [];
    /**
     * @var array
     */
    private $receivers = [];
    /**
     * @var
     */
    private $subject;

    /**
     * @var
     */
    private $body;


    /**
     * @param $fromEmail
     * @param $fromName
     *
     * @return mixed|void
     */
    public function setFrom($fromEmail, $fromName): void
    {
        $this->sender['Email'] = $fromEmail;
        $this->sender['Name']  = $fromName;
    }

    /**
     * @param array $to
     */
    public function setTo(array $to): void
    {
        foreach ($to as $key => $value) {
            $this->receivers['To'][$key]['Email'] = $value['email'];
            $this->receivers['To'][$key]['Name']  = $value['name'];
        }

    }

    /**
     * @param array $bcc
     */
    public function setBcc(array $bcc = []): void
    {
        foreach ($bcc as $key => $value) {
            $this->receivers['Bcc'][$key]['Email'] = $value['email'];
            $this->receivers['Bcc'][$key]['Name']  = $value['name'];
        }
    }

    /**
     * @param array $cc
     */
    public function setCc(array $cc = []): void
    {
        foreach ($cc as $key => $value) {
            $this->receivers['Cc'][$key]['Email'] = $value['email'];
            $this->receivers['Cc'][$key]['Name']  = $value['name'];
        }
    }

    /**
     * @param string $subject
     */
    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }


    /**
     * @param        $content
     * @param string $type
     *
     * @return mixed|void
     */
    public function setBody($content, string $type = 'text'): void
    {
        $this->body =
            [
                'content' => $content,
                'type'    => $type
            ];
    }

    /**
     * @return bool|mixed
     */
    public function getBody(): array
    {
        return $this->body;
    }

    /**
     * @return array
     */
    public function getSender(): array
    {

        return $this->sender;

    }


    /**
     * @return array
     */
    public function getReceivers(): array
    {
        return $this->receivers;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }
}