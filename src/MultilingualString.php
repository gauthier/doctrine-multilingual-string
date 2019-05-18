<?php


namespace Gauthier\MultilingualString;


class MultilingualString
{
    protected static $availableLanguages = [];

    protected static $currentLanguage;

    protected static $defaultLanguage;

    protected static $routes = [];
    /**
     * @var array
     */
    protected $translations = [];

    /**
     * MultilingualString constructor.
     * @param array $value
     */
    public function __construct(array $translations = [])
    {
        $this->hydrate($translations);
    }


    public static function addAvailableLanguage(string $language)
    {
        if(!in_array($language, self::$availableLanguages)) {
            self::$availableLanguages[] = $language;
        }
    }

    public static function setAvailableLanguages(array $languages)
    {
        self::$availableLanguages = [];
        foreach ($languages as $language) {
            self::addAvailableLanguage($language);
        }
    }

    /**
     * @param string $language
     * @throws MultilingualStringException
     */
    public static function setDefaultLanguage(string $language)
    {
        self::validateLanguage($language);

        self::$defaultLanguage = $language;
    }

    /**
     * @param string $language
     * @throws MultilingualStringException
     */
    public static function setCurrentLanguage(string $language)
    {
        self::validateLanguage($language);

        self::$currentLanguage = $language;
    }

    /**
     * @param string $language
     * @return bool
     * @throws MultilingualStringException
     */
    public static function validateLanguage(string $language)
    {
        if(!in_array($language, self::$availableLanguages)) {
            throw new MultilingualStringException(sprintf('Cannot set "%s" as default language because this is not one of declared available languages (%s)', $language, implode(', ', self::$availableLanguages)));
        }

        return true;
    }

    public function hydrate(array $translations) {
        $defaultValues = array_flip(self::$availableLanguages);
        array_walk($defaultValues, function(&$value) { $value = '';});
        $this->translations = $translations + $defaultValues;
    }

    public function extract()
    {
        return $this->translations;
    }
}

