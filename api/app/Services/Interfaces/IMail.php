<?php

/**
 *
 * @author           sbhatnagar
 * @date             6/1/19
 */

namespace App\Services\Interfaces;

/**
 * Interface IMail
 * @package App\Services\Interfaces
 */
interface IMail
{
    /**
     * @param array $to
     *
     * @return void
     */
    public function setTo(array $to): void;

    /**
     * @param array $bcc
     *
     * @return void
     */
    public function setBcc(array $bcc = []): void;

    /**
     * @param array $cc
     *
     * @return void
     */
    public function setCc(array $cc = []): void;

    /**
     * @param string|null $subject
     *
     * @return void
     */
    public function setSubject(string $subject): void;

    /**
     * @param $fromEmail
     * @param $fromName
     *
     * @return void
     */
    public function setFrom($fromEmail, $fromName): void;

    /**
     * @param        $content
     * @param string $type
     *
     * @return void
     */
    public function setBody($content, string $type = 'text'): void;

    /**
     * @return array
     */
    public function getBody(): array;

    /**
     * @return array
     */
    public function getReceivers(): array;

    /**
     * @return string
     */
    public function getSubject(): string;

    /**
     * @return array
     */
    public function getSender(): array;

}