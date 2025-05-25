<?php

namespace App\Enums;

class InvoiceStatus
{
    public const PAID = 'paid';
    public const UNPAID = 'unpaid';
    public const FAILED = 'failed';

    public static function all(): array
    {
        return [
            self::PAID,
            self::UNPAID,
            self::FAILED,
        ];
    }

    /**
     * Map status dari Xendit ke status internal
     */
    public static function fromXendit(string $status): string
    {
        return match (strtoupper($status)) {
            'PAID', 'SETTLED' => self::PAID,
            'FAILED', 'EXPIRED' => self::FAILED,
            'PENDING' => self::UNPAID,
            default => self::UNPAID,
        };
    }
}
