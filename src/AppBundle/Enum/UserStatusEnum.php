<?php

namespace AppBundle\Enum;


abstract class UserStatusEnum
{
    const STATUS_EMPLOYED = "employed";
    const STATUS_TRAINEE  = "trainee";
    const STATUS_TRIAL    = "trial";

    // TODO add translation
    /** @var array */
    protected static $typeName = [
        self::STATUS_EMPLOYED => 'Employé',
        self::STATUS_TRAINEE  => 'Stagiaire',
        self::STATUS_TRIAL    => 'En période d\'essai',
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
            self::STATUS_EMPLOYED,
            self::STATUS_TRAINEE,
            self::STATUS_TRIAL,
        ];
    }

    /**
     * @return array<string>
     */
    public static function getFullNameList()
    {
        return [
            static::$typeName[self::STATUS_EMPLOYED],
            static::$typeName[self::STATUS_TRAINEE],
            static::$typeName[self::STATUS_TRIAL],
        ];
    }
}