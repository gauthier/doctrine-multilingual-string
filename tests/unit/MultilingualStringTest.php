<?php

use Codeception\Test\Unit;
use Gauthier\MultilingualString\MultilingualString;

class MultilingualStringTest extends Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;


    // tests
    public function testBasicFeatures()
    {
        $availableLanguages = ['fr', 'en', 'de', 'it', 'nl'];
        MultilingualString::setAvailableLanguages($availableLanguages);

        $this->tester->assertEquals('fr', MultilingualString::getDefaultLanguage());

        MultilingualString::setFallbackLanguage('en');
        MultilingualString::setRoute('nl', 'de');


        $translations = ['fr' => 'ceci est du français', 'en' => 'this is english', 'de' => 'es ist Deutsch'];
        $string = new MultilingualString($translations);

        // test default translation
        $this->tester->assertEquals('ceci est du français', $string->getValue());

        // test given language translation
        $this->tester->assertEquals('es ist Deutsch', $string->getValue('de'));

        // test fallback translation
        $this->tester->assertEquals('this is english', $string->getValue('it'));

        // test routed translation
        $this->tester->assertEquals('es ist Deutsch', $string->getValue('nl'));

        // test default language explicit setting
        MultilingualString::setDefaultLanguage('fr');
        $this->tester->assertEquals('ceci est du français', $string->getValue());

    }
}
