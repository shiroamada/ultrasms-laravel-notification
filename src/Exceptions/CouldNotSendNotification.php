<?php

namespace NotificationChannels\UltraSms\Exceptions;

use DomainException;
use Exception;

class CouldNotSendNotification extends Exception
{
    /**
     * Thrown when recipient's phone number is missing.
     *
     * @return static
     */
    public static function missingRecipient()
    {
        return new static('Notification was not sent. Phone number is missing.');
    }

    /**
     * Thrown when content length is greater than 800 characters.
     *
     * @return static
     */
    public static function contentLengthLimitExceeded()
    {
        return new static(
            'Notification was not sent. Content length may not be greater than 800 characters.'
        );
    }

    /**
     * Thrown when we're unable to communicate with smsc.ru.
     *
     * @param  DomainException  $exception
     *
     * @return static
     */
    public static function exceptionUltraSmsRespondedWithAnError(DomainException $exception)
    {
        return new static(
            "UltraSms responded with an error '{$exception->getCode()}: {$exception->getMessage()}'"
        );
    }

    /**
     * Thrown when we're unable to communicate with smsc.ru.
     *
     * @param  Exception  $exception
     *
     * @return static
     */
    public static function couldNotCommunicateWithUltraSms(Exception $exception)
    {
        return new static("The communication with ultrasms failed. Reason: {$exception->getMessage()}");
    }
}
