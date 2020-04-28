<?php

namespace App\Console\Commands;

use App\Exceptions\InvalidPayloadException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use App\Exceptions\InvalidContentTypeException;
use App\Jobs\EmailJob;
use Illuminate\Support\Facades\Validator;

/**
 * Class SendEmail
 * @package App\Console\Commands
 */
class SendEmail extends Command
{
    /**
     *
     */
    const VALID_TYPE = ["text/html", "text/plain"];
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:email {toName} {toEmail} {subject} {content} {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Console command needs param like "toName: (as a string), "toEmail: (as a valid email), subject: (as a string), content: (content as plain or a html content for email body) and type: (a valid email type either text/plain or text/html)." example data: ' . "php artisan send:email 'abc123@gmail.com' 'abc' 'Your email Subject! ' 'Dear Xyz, welcome to Send Email Services!!' 'text/plain'";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     * @throws InvalidContentTypeException
     * @throws InvalidPayloadException
     */
    public function handle(): void
    {
        $toName  = $this->argument('toName');
        $toEmail = $this->argument('toEmail');
        $subject  = $this->argument('subject');
        $content  = $this->argument('content');
        $type     = $this->argument('type');

        $data = $this->prepareJson($toName, $toEmail, $subject, $content, $type);
        $this->validateInput($data);
        Queue::push(new EmailJob($data));
        $this->info('Email has been pushed to queue.');
    }


    /**
     *
     * @param $toName
     * @param $toEmail
     * @param $subject
     * @param $content
     * @param $type
     *
     * @return array
     */
    private function prepareJson($toName, $toEmail, $subject, $content, $type): array
    {
        $data = [];

        $data['to'][0]['email'] = $toName;
        $data['to'][0]['name']  = $toEmail;
        $data['subject']        = $subject;
        $data['content']        = $content;
        $data['type']           = $type;

        return $data;
    }


    /**
     * @param $request
     *
     * @return bool
     * @throws InvalidContentTypeException
     * @throws InvalidPayloadException
     */
    private function validateInput($request): bool
    {

        $validator = Validator::make([
            'to'         => $request['to'],
            'to.*.email' => $request['to'][0]['email'],
            'subject'    => $request['subject'],
            'content'    => $request['content'],
        ], [
            'to'         => ['required'],
            'to.*.email' => ['required', 'email'],
            'subject'    => ['required'],
            'content'    => ['required'],
        ]);

        if ($validator->fails()) {
            $this->warn('Inputs are not valid. See error messages below:');

            foreach ($validator->errors()->all() as $error) {
                Log::info('Error while validating console command send:email param' . $error);
                throw new InvalidPayloadException($error);
            }

            return true;
        }

        if (!in_array($request['type'], self::VALID_TYPE)) {
            Log::info($request['type'] . ' is not a valid content type.');
            throw new InvalidContentTypeException('Please Provide a Valid Type of Content either ' . self::VALID_TYPE[0] . ' or ' . self::VALID_TYPE[1]);
        }

        return true;
    }
}
