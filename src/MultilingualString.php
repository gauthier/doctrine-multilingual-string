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
    public function __construct(?array $translations = [])
    {
        $this->hydrate($translations ?? []);
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

    /**
     * @param string $from
     * @param string $to
     * @throws MultilingualStringException
     */
    public static function setRoute(string $from, string $to)
    {
        self::validateLanguage($from);
        self::validateLanguage($to);
        self::$routes[$from] = $to;
    }

    /**
     * @param string $language
     * @throws MultilingualStringException
     */
    public static function setFallbackLanguage(string $language)
    {
        self::validateLanguage($language);

        self::$fallbackLanguage = $language;
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
            throw new MultilingualStringException(sprintf('Language "%s" is not one of declared available languages (%s)',
                $language, implode(', ', self::$availableLanguages)));
        }

        return true;
    }

    /**
     * @param array $translations
     * @throws MultilingualStringException
     */
    public function hydrate(array $translations)
    {
        $defaultValues = array_flip(self::$availableLanguages);
        array_walk($defaultValues, function (&$value) {
            $value = '';
        });
        foreach($translations + $defaultValues as $lang => $translation) {
            $this->addTranslation($lang, $translation);
        }
    }

    /**
     * @param $lang
     * @param $translation
     * @throws MultilingualStringException
     */
    public function addTranslation($lang, $translation)
    {
        self::validateLanguage($lang);
        $this->translations[$lang] = $translation;
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
    public function getValue($language = null)
    {


        if (is_null($language)) {
            $language = self::getDefaultLanguage();
        }

        self::validateLanguage($language);

        if (empty($this->translations[$language])) {
            $didTryFallbackLanguage = ($language == self::getFallbackLanguage());
            // try translation routes
            while ($language = self::getRoutedLanguage($language)) {
                if (!empty($this->translations[$language])) {
                    return $this->translations[$language];
                }
                if (!$didTryFallbackLanguage) {
                    $didTryFallbackLanguage = ($language == self::getFallbackLanguage());
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

        return $this->translations[$language];


    }

    /**
     * @return array
     */
    public static function getAvailableLanguages(): array
    {
        return self::$availableLanguages;
    }

    /**
     * @return array
     */
    public static function getRoutes(): array
    {
        return self::$routes;
    }

    /**
     * @return array
     */
    public function getTranslations(): array
    {
        return $this->translations;
    }

    /**
     * @return string
     * @throws MultilingualStringException
     */
    public function __toString()
    {
        return $this->getValue();
    }


}

