<?php

namespace AppBundle\Enum;


abstract class UserPreferenceEnum
{
    const PREFERENCE_1_CROISSANTS = "1 croissant";
    const PREFERENCE_2_CROISSANTS = "2 croissants";
    const PREFERENCE_1_PAIN_AU_CHOCOLAT = "1 pains au chocolat";
    const PREFERENCE_2_PAIN_AU_CHOCOLAT = "2 pains au chocolat";
    const PREFERENCE_CROISSANT_PAIN_AU_CHOCOLAT = "mix";

    // TODO add translation
    /** @var array */
    protected static $typeName = [
        self::PREFERENCE_1_CROISSANTS => "Un croissant",
        self::PREFERENCE_2_CROISSANTS => "Deux croissants",
        self::PREFERENCE_1_PAIN_AU_CHOCOLAT => "Un pain au chocolat",
        self::PREFERENCE_2_PAIN_AU_CHOCOLAT => "Deux pains au chocolat",
        self::PREFERENCE_CROISSANT_PAIN_AU_CHOCOLAT => "Un croissant et un pain au chocolat",
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
            self::PREFERENCE_1_CROISSANTS,
            self::PREFERENCE_2_CROISSANTS,
            self::PREFERENCE_1_PAIN_AU_CHOCOLAT,
            self::PREFERENCE_2_PAIN_AU_CHOCOLAT,
            self::PREFERENCE_CROISSANT_PAIN_AU_CHOCOLAT,
        ];
    }

    /**
     * @return array<string>
     */
    public static function getFullNameList()
    {
        return [
            static::$typeName[self::PREFERENCE_1_CROISSANTS],
            static::$typeName[self::PREFERENCE_2_CROISSANTS],
            static::$typeName[self::PREFERENCE_1_PAIN_AU_CHOCOLAT],
            static::$typeName[self::PREFERENCE_2_PAIN_AU_CHOCOLAT],
            static::$typeName[self::PREFERENCE_CROISSANT_PAIN_AU_CHOCOLAT],
        ];
    }
}