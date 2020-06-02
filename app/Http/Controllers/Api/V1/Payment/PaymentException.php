<?php

namespace App\Http\Controllers\Api\V1\Payment;

class PaymentException extends \Exception
{
    const UNKNOWN = 0;
    const INVALID_SIGNATURE = 1;
    const PAYMENT_REJECTED = 2;
    const CONFIRMATION_FAILED = 3;
    const RETURN_FAILED = 4;
    const STATUS_FAILED = 5;
    const CARD_LIST_FAILED = 6;
    const CARD_DELETION_FAILED = 7;
    const SERVICE_UNAVAILABLE = 8;

    public function getFormattedLogMessage()
    {
        switch ($this->getCode())
        {
            case self::INVALID_SIGNATURE:
                return "Invalid signature.";
            case self::PAYMENT_REJECTED:
                return "ProjectPayment rejected. Reason: " . $this->getMessage();
            case self::CONFIRMATION_FAILED:
                return "Confirmation failed. Message: "  . $this->getMessage();
            case self::RETURN_FAILED:
                return "Return failed. Message: "  . $this->getMessage();
            case self::STATUS_FAILED:
                return "Status query failed. Message: "  . $this->getMessage();
            case self::CARD_LIST_FAILED:
                return "Card list query failed. Message: "  . $this->getMessage();
            case self::CARD_DELETION_FAILED:
                return "Card deletion failed. Message: "  . $this->getMessage();
            case self::SERVICE_UNAVAILABLE:
                return "Service unavailable.";
        }
    }

    public function getErrorCode()
    {
        switch ($this->getCode())
        {
            case self::INVALID_SIGNATURE:
                return "INVALID_SIGNATURE";
            case self::PAYMENT_REJECTED:
                return "PAYMENT_REJECTED";
            case self::CONFIRMATION_FAILED:
                return "CONFIRMATION_FAILED";
            case self::RETURN_FAILED:
                return "RETURN_FAILED";
            case self::STATUS_FAILED:
                return "STATUS_FAILED";
            case self::CARD_LIST_FAILED:
                return "CARD_LIST_FAILED";
            case self::CARD_DELETION_FAILED:
                return "CARD_DELETION_FAILED";
            case self::SERVICE_UNAVAILABLE:
                return "SERVICE_UNAVAILABLE";
            default:
                return "UNKNOWN";
        }
    }
}
