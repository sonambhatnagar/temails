<?php
/**
 *
 * @author           sbhatnagar
 * @date             6/1/19
 */

namespace App\Jobs;

use App\Services\Interfaces\IEmailServices;
use Illuminate\Support\Facades\Log;

/**
 * Class EmailJob
 * @package App\Jobs
 */
class EmailJob extends Job
{

    /**
     * @var int
     */
    public $tries = 1; // Number of tries allowed to process record if getting error while handling queue jobs.
    /**
     * @var array
     */
    private $data;

    /**
     *
     * Create a new job instance.
     *
     * @param array $data Payload data to put in the queue.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job with payload data.
     *
     * @param IEmailServices $emailServices
     *
     * @return void
     */
    public function handle(IEmailServices $emailServices): void
    {
        Log::info('Job Queue is successfully executed at start time - ' . microtime(true));
        $emailServices->sendEmail($this->data);

    }

    /**
     * This function will handle the failed scenario. As this system send high degree emails so a notification should
     * be sent to admin for all the failed records. The admin can further retry the fail table or may flush the record.
     */
    public function failed(): void
    {
        // Called when the job is failing...
        Log::info("Failed scenario executed.");

    }


}
