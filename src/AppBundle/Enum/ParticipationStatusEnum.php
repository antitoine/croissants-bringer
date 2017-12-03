<?php

namespace AppBundle\Enum;


abstract class ParticipationStatusEnum
{
    const STATUS_DONE      = "done";
    const STATUS_VALIDATED = "validated";
    const STATUS_PENDING   = "pending";

    /** @var array */
    protected static $typeName = [
        self::STATUS_DONE      => 'Terminé',
        self::STATUS_VALIDATED => 'Validé',
        self::STATUS_PENDING   => 'En attente',
    ];

    /**
     * @param  string $statusShortName
     * @return string
     */
    public static function getStatusName($statusShortName)
    {
        if (!isset(static::$typeName[$statusShortName])) {
            return "Unknown type ($statusShortName)";
        }

        return static::$typeName[$statusShortName];
    }

    /**
     * @return array<string>
     */
    public static function getAvailableStatus()
    {
        return [
            self::STATUS_DONE,
            self::STATUS_VALIDATED,
            self::STATUS_PENDING,
        ];
    }
}