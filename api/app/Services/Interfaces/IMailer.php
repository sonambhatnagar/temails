<?php

/**
 *
 * @author           sbhatnagar
 * @date             6/1/19
 */

namespace App\Services\Interfaces;

/**
 * Interface IMailer
 * @package App\Services\Interfaces
 */
interface IMailer
{
    /**
     * @param IMail $mail
     *
     * @return bool
     */
    public function send(IMail $mail): bool;

}