<?php
/**
 *
 * @author           sbhatnagar
 * @date             6/1/19
 */

namespace App\Repositories;

use App\Models\EmailModel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Class EmailRepository
 * @package App\Repositories
 */
class EmailRepository
{

    /**
     * @var EmailModel
     */
    private $emails;

    /**
     *
     * Create the event listener.
     *
     * @param EmailModel $emails
     */
    public function __construct(EmailModel $emails)
    {
        $this->emails = $emails;
    }

    /**
     * Storing Emails which is going through our system from queue. We are adding request and response data in success
     * and a failure case as well.
     *
     * @param $request
     * @param $response
     * @param $status
     *
     * @return bool
     */
    public function saveData($request, $response, $status): bool
    {
        $this->emails->request_data  = json_encode($request);
        $this->emails->response_data = json_encode($response);
        $this->emails->status        = $status;
        $this->emails->save();
        return true;
    }


    /**
     * Write now we are supporting only one week data in the system.
     *
     * @param $date
     *
     * @param $status
     *
     * @return object|null
     */
    public function getEmailsDataWithInWeek($date, $status): ?object
    {
        return Cache::remember('emails-' . $status . '-' . $date->format('Y-m-dH:i:s'), config('mail.cache.content'),
            function () use ($date, $status) {
                $emailData = $this->emails->getEmailsWithInWeek($date, $status)->get();
                $count     = $emailData->count();

                return ($count > 0) ? $emailData : null;
            });
    }
}
