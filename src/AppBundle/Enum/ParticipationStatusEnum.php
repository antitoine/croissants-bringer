<?php

namespace AppBundle\Enum;


abstract class ParticipationStatusEnum
{
    const STATUS_FAILED    = "failed"; // Participation failed
    const STATUS_DONE      = "done"; // Participation complete
    const STATUS_PENDING   = "pending"; // Participant accepted, waiting confirmation from other that the mission is done
    const STATUS_REFUSED   = "refused"; // Participant refused the participation
    const STATUS_ASKING    = "asking"; // Wait participant approval

    // TODO add translation
    /** @var array */
    protected static $typeName = [
        self::STATUS_FAILED    => 'Échec',
        self::STATUS_DONE      => 'Terminé',
        self::STATUS_PENDING   => 'En attente de la confirmation du succès de la mission',
        self::STATUS_REFUSED   => 'Refus du participant',
        self::STATUS_ASKING    => 'En attente de l\'approbation du participant',
    ];

    /**
     * @param  string $statusShortName
     * @return string
     */
    public static function getFullName($statusShortName)
    {
        if (!isset(static::$typeName[$statusShortName])) {
            return "Unknown type ($statusShortName)";
        }

        return static::$typeName[$statusShortName];
    }

    /**
     * @return array<string>
     */
    public static function getShortNameList()
    {
        return [
            self::STATUS_FAILED,
            self::STATUS_DONE,
            self::STATUS_PENDING,
            self::STATUS_REFUSED,
            self::STATUS_ASKING,
        ];
    }

    /**
     * @return array<string>
     */
    public static function getFullNameList()
    {
        return [
            static::$typeName[self::STATUS_FAILED],
            static::$typeName[self::STATUS_DONE],
            static::$typeName[self::STATUS_PENDING],
            static::$typeName[self::STATUS_REFUSED],
            static::$typeName[self::STATUS_ASKING],
        ];
    }
}