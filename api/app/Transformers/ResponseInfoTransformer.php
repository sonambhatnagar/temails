<?php
/**
 *
 * @author           sbhatnagar
 * @date             6/1/19
 */

namespace App\Transformers;

/**
 * Class ResponseInfoTransformer
 * @package App\Transformers
 */
class ResponseInfoTransformer
{
    /**
     * @return array
     */
    public function emailQueueResponse(): array
    {
        return [
            'status'      => 'success',
            'pushToQueue' => 'true',
            'message'     => 'Email Pushed to Queue.'
        ];

    }

    /**
     * @return array
     */
    public function entryResponse(): array
    {
        return [
            'API_Version' => 'v1',
            'Name'        => 'Transactional email API',
            'Environment' => config('mail.env')
        ];
    }

    /**
     *
     * @param $list
     *
     * @return array
     */
    public function emailsListResponse($list): array
    {

        $data = [];

        if (isset($list->status)) {
            return (array)$list;
        }

        $data['status'] = 'success';

        foreach ($list as $key => $value) {
            $data['data'][$key]['id']           = $list[$key]['id'];
            $data['data'][$key]['sent_date']    = $this->convertToApiTimeZone($list[$key]['created_at']);
            $data['data'][$key]['email_data']   = json_decode($list[$key]['request_data']);
            $data['data'][$key]['email_status'] = $list[$key]['status'];

        }

        return $data;

    }


    /**
     * @convert Timezone in the response.
     *
     * @param $date
     *
     * @return null|string
     */
    private function convertToApiTimeZone($date): string
    {

        if ($date == '0000-00-00 00:00:00') {
            return null;
        }
        $dateTime = new \DateTime($date);

        return $dateTime->setTimezone(new \DateTimeZone('Europe/Amsterdam'))->format('Y-m-d H:i:s');
    }

}