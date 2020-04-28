<?php
/**
 *
 * @author           sbhatnagar
 * @date             6/1/19
 */

namespace App\Http\Controllers;

use App\Exceptions\InvalidContentTypeException;
use App\Jobs\EmailJob;
use App\Services\Interfaces\IEmailServices;
use App\Transformers\ResponseInfoTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Log;

/**
 * Class EmailController
 * @package App\Http\Controllers
 */
class EmailController extends Controller
{

    /**
     * @var ResponseInfoTransformer
     */
    private $responseInfoTransformer;
    /**
     * @var IEmailServices
     */
    private $emailServices;

    /**
     * @const Valid Type
     */
    const VALID_TYPE = ["text/html", "text/plain"];

    /**
     * Create a new controller instance.
     *
     * @param ResponseInfoTransformer $responseInfoTransformer
     * @param IEmailServices          $emailServices
     */
    public function __construct(ResponseInfoTransformer $responseInfoTransformer, IEmailServices $emailServices)
    {
        $this->responseInfoTransformer = $responseInfoTransformer;
        $this->emailServices           = $emailServices;
    }


    /**
     * @ Entry point of the api service.
     *
     * @return array
     */
    public function version(): array
    {
        return $this->responseInfoTransformer->entryResponse();
    }


    /**
     * @Function to get Email json data and push job to the queue.
     *
     * @param Request $request
     *
     * @return array
     * @throws InvalidContentTypeException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function queueEmail(Request $request): array
    {

        $requestJson = $request->json()->all();
        $this->validateInput($request);

        //Push request data to Queue.
        Queue::push(new EmailJob($requestJson));

        // Update the record from EmailTransformer
        return $this->responseInfoTransformer->emailQueueResponse();
    }

    /**
     * @ Returns list of email sent through this service. status param valid inputs ['sent', 'bounced', 'error',
     *   'failed']
     *
     * @param null $status
     *
     * @return array
     */
    public function getEmails($status = null): array
    {
        // Call services to get emails based on requested status
        $response = $this->emailServices->getEmails($status);

        return $this->responseInfoTransformer->emailsListResponse($response);
    }

    /**
     * @ Request json data validator
     *
     * @param $request
     *
     * @return bool
     * @throws InvalidContentTypeException
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateInput($request): bool
    {

        $rules = [
            'to'         => 'required',
            'to.*.email' => 'required|email',
            'subject'    => 'required',
            'content'    => 'required',
            'type'       => 'required'
        ];
        $this->validate($request, $rules);

        if (!in_array($request['type'], self::VALID_TYPE)) {
            Log::error($request['type'] . ' is not a valid content type.');
            throw new InvalidContentTypeException('Please Provide a Valid Type of Content either ' . self::VALID_TYPE[0] . ' or ' . self::VALID_TYPE[1]);
        }

        return true;
    }

}
