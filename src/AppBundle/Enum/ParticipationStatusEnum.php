<?php

namespace AppBundle\Enum;


abstract class ParticipationStatusEnum
{
    const STATUS_DONE      = "done"; // Participation complete
    const STATUS_PENDING   = "pending"; // Participant accept, waiting comfirmation from other that the mission is done
    const STATUS_WAITING   = "asking"; // Wait participant approval

    /** @var array */
    protected static $typeName = [
        self::STATUS_DONE      => 'Terminé',
        self::STATUS_PENDING   => 'En attente de réalisation de la mission',
        self::STATUS_WAITING   => 'En attente de l\'approbation du participant',
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
            self::STATUS_PENDING,
            self::STATUS_WAITING,
        ];
    }
}