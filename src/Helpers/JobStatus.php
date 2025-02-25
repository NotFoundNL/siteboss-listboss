<?php

declare(strict_types=1);

namespace NotFound\ListBoss\Helpers;

/**
 * THE ORIGINAL FILE IS IN LISTBOSS REPOSITORY
 *
 * https://github.com/NotFoundNL/listboss/blob/main/server/site/app/JobStatus.php
 *
 * This file will be the new location for the JobStatus enum.
 */
enum JobStatus: int
{
    // Before starting
    case CREATED = 1;
    case UPDATED = 2;
    case PREVIEW_SENT = 3;

    // After starting
    case STARTED = 11;
    case STOPPED = 12;
    case CANCELED = 13; // US Spelling

    // All done
    case FINISHED = 50;

    // Busy
    case PREVIEW_WAIT = 101;
    case BUSY = 102;

    // Error cases
    case ERROR = 201;

    public static function list(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_column(self::cases(), 'name')
        );
    }

    public function name(): string
    {
        return self::list()[$this->value];
    }

    public function started(): bool
    {
        return $this->value >= self::STARTED->value;
    }

    public static function tryFromName(string $name): ?static
    {
        foreach (self::cases() as $case) {
            if ($case->name === $name) {
                return $case;
            }
        }

        return null;
    }

    public function getReadableName()
    {
        switch ($this->name) {
            case 'CREATED':
            case 'UPDATED':
                return 'Nog niet verzonden';
            case 'PREVIEW_SENT':
                return 'Voorbeeld is verzonden';

                // After starting
            case 'STARTED':
                return 'Verzending is gestart';
            case 'STOPPED':
                return 'Verzending is gestopt';
            case 'CANCELED':
                return 'Verzending is geannuleerd.';
                // All done
            case 'FINISHED':
                return 'Verzending geslaagd';
                // Busy
            case 'PREVIEW_WAIT':
                return 'Preview wordt verzonden';
            case 'BUSY':
                return 'Bezig';

                // Error cases
            default:
                return 'Fout opgetreden, neem contact op';
        }
    }
}
