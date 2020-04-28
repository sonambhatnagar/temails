<?php

namespace App\Listeners;

use App\Events\EmailSentEvent;
use App\Repositories\EmailRepository;
use Illuminate\Support\Facades\Log;

/**
 * Class EmailSentNotificationListener
 * @package App\Listeners
 */
class EmailSentNotificationListener
{
    /**
     * @var EmailRepository
     */
    private $emailRepository;

    /**
     * Create the event listener.
     *
     * @param EmailRepository $emailRepository
     */
    public function __construct(EmailRepository $emailRepository)
    {
        $this->emailRepository = $emailRepository;
    }

    /**
     * Handle the event.
     *
     * @param EmailSentEvent $event
     *
     * @return void
     */
    public function handle(EmailSentEvent $event)
    {
        $request                     = $event->getRequestData();
        $response                    = $event->getResponseData();
        $status                      = $event->getStatus();
        try{
            $this->emailRepository->saveData($request, $response, $status);
        }catch (\Exception $e){
            Log::error('Error while saving data in the email_status table: ' . $e->getMessage());
        }

    }
}