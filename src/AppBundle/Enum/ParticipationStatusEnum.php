<?php

namespace AppBundle\Enum;


abstract class ParticipationStatusEnum
{
    const STATUS_FAILED    = "failed"; // Participation failed
    const STATUS_DONE      = "done"; // Participation complete
    const STATUS_PENDING   = "pending"; // Participant accept, waiting confirmation from other that the mission is done
    const STATUS_ASKING    = "asking"; // Wait participant approval

    /** @var array */
    protected static $typeName = [
        self::STATUS_FAILED    => 'Échec',
        self::STATUS_DONE      => 'Terminé',
        self::STATUS_PENDING   => 'En attente de la confirmation de la mission',
        self::STATUS_ASKING    => 'En attente de l\'approbation du participant',
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
            self::STATUS_FAILED,
            self::STATUS_DONE,
            self::STATUS_PENDING,
            self::STATUS_ASKING,
        ];
    }
}