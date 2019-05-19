<?php


namespace Gauthier\MultilingualString;


/**
 * Class MultilingualString
 * @package Gauthier\MultilingualString
 */
class MultilingualString
{
    /**
     * @var array
     */
    protected static $availableLanguages = [];

    /**
     * @var
     */
    protected static $defaultLanguage;

    /**
     * @var array
     */
    protected static $routes = [];

    /**
     * @var string
     */
    protected static $fallbackLanguage;

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


    /**
     * @param string $language
     */
    public static function addAvailableLanguage(string $language)
    {
        if (!in_array($language, self::$availableLanguages)) {
            self::$availableLanguages[] = $language;
        }
    }

    /**
     * @param array $languages
     */
    public static function setAvailableLanguages(array $languages)
    {
        self::$availableLanguages = [];
        foreach ($languages as $language) {
            self::addAvailableLanguage($language);
        }
    }

    public static function setRoute(string $from, string $to)
    {
        self::$routes[$from] = $to;
    }

    public static function setFallbackLanguage(string $language)
    {
        self::validateLanguage($language);

        self::$fallbackLanguage = $language;
    }

    /**
     * @return mixed
     * @throws MultilingualStringException
     */
    protected static function getCurrentLanguage()
    {
        if(empty(self::$currentLanguage)) {
            self::$currentLanguage = self::getDefaultLanguage();
        }

        return self::$currentLanguage;
    }

    /**
     * @param $lang
     * @return mixed|null
     */
    public static function getRoutedLanguage($lang)
    {
        return self::$routes[$lang] ?? null;
    }

    /**
     * @return mixed|string
     * @throws MultilingualStringException
     */
    public static function getFallbackLanguage()
    {
        if (empty(self::$fallbackLanguage)) {
            return self::getDefaultLanguage();
        }

        return self::$fallbackLanguage;
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
     * @return bool
     * @throws MultilingualStringException
     */
    public static function validateLanguage(string $language)
    {
        if (!in_array($language, self::$availableLanguages)) {
            throw new MultilingualStringException(sprintf('Cannot set "%s" as default language because this is not one of declared available languages (%s)',
                $language, implode(', ', self::$availableLanguages)));
        }

        return true;
    }

    /**
     * @param array $translations
     */
    public function hydrate(array $translations)
    {
        $defaultValues = array_flip(self::$availableLanguages);
        array_walk($defaultValues, function (&$value) {
            $value = '';
        });
        $this->translations = $translations + $defaultValues;
    }

    /**
     * @return mixed
     * @throws MultilingualStringException
     */
    public static function getDefaultLanguage()
    {
        if (empty(self::$defaultLanguage)) {

            if (count(self::$availableLanguages)) {
                return self::$availableLanguages[0];
            } else {
                throw new MultilingualStringException('No languages available');
            }
        }

        return self::$defaultLanguage;
    }

    /**
     * @return array
     */
    public function extract()
    {
        return $this->translations;
    }

    /**
     * @param null $lang
     * @throws MultilingualStringException
     */
    public function getValue($lang = null)
    {

        if (is_null($lang)) {
            $lang = self::getDefaultLanguage();
        }

        if (empty($this->translations[$lang])) {
            $didTryFallbackLanguage = ($lang == self::getFallbackLanguage());
            // try translation routes
            while ($lang = self::getRoutedLanguage($lang)) {
                if (!empty($this->translations[$lang])) {
                    return $this->translations[$lang];
                }
                if (!$didTryFallbackLanguage) {
                    $didTryFallbackLanguage = ($lang == self::getFallbackLanguage());
                }
            }

            // look for fallback translation
            if (!$didTryFallbackLanguage) {
                if (!empty($this->translations[self::getFallbackLanguage()])) {
                    return $this->translations[self::getFallbackLanguage()];
                }
            }

            return null;
        }

        return $this->translations[$lang];


    }
}

