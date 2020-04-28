<?php

/**
 *
 * @author           sbhatnagar
 * @date             06/08/19
 */

namespace App\Services\Interfaces;

/**
 * Interface IEmailServices
 * @package App\Services\Interfaces
 */
interface IEmailServices
{
    /**
     * @param array $requestData
     *
     * @return void
     */
    public function sendEmail(array $requestData): void ;

    /**
     * @param $status
     *
     * @return object
     */
    public function getEmails(string $status = null): object ;
}