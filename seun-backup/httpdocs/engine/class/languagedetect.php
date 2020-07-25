<?php

class languagedetect
{
    /**
     * Browser Locale Detection
     *
     * This functions check the HTTP_ACCEPT_LANGUAGE HTTP-Header
     * for the supported browser languages and returns an array.
     *
     * Basically HTTP_ACCEPT_LANGUAGE locales are composed by 3 elements:
     * PREFIX-SUBCLASS ; QUALITY=value
     *
     * PREFIX:      is the main language identifier
     *              (i.e. en-us, en-ca => both have prefix EN)
     * SUBCLASS:    is a subdivision for main language (prefix)
     *              (i.e. en-us runs for english - united states) IT CAN BE BLANK
     * QUALITY:     is the importance for given language
     *              primary language setting defaults to 1 (100%)
     *              secondary and further selections have a lower Q value (value <1).
     * EXAMPLE:     de-de,de;q=0.8,en-us;q=0.5,en;q=0.3
     *
     * @access public
     * @return Array containing the list of supported languages
     * @todo $_SERVER is an httprequest object...
     *        the method should be placed there and data fetch from the httprequest->method
     */

    private $supported = array(
        'en',
        'fr',
        'es');

    public function __construct()
    {
        $lang = self::getLanguage();
        $inTwoMonths = 60 * 60 * 24 * 60 + time();
        # based on that value, require the language file
        if (!isset($_COOKIE['language']) || empty($_COOKIE['language'])) {
            switch ($lang) {
                case 'es':
                    setrawcookie('language', "es", $inTwoMonths, "/", false);
                    break;

                case 'fr':
                    setrawcookie('language', "fr", $inTwoMonths, "/", false);
                    break;

                default:
                    setrawcookie('language', "en", $inTwoMonths, "/", false);
            }

            $server = $_SERVER["REQUEST_URI"];
            echo "<meta http-equiv='refresh' content='0;url=$server'>";
            exit;
        }
    }

    public function getLanguage()
    {
        # start with the default language
        $language = $this->supported[0];

        # get the list of languages supported by the browser
        $browserLanguages = self::getBrowserLanguages();

        # look if the browser language is a supported language, by checking the array entries
        foreach ($browserLanguages as $browserLanguage) {
            # if a supported language is found, set it and stop
            if (in_array($browserLanguage, $this->supported)) {
                $language = $browserLanguage;
                break;
            }
        }

        # return the found language
        return $language;
    }

    public function getBrowserLanguages()
    {
        # check if environment variable HTTP_ACCEPT_LANGUAGE exists
        if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            # if not return an empty language array
            return array();
        }

        # explode environment variable HTTP_ACCEPT_LANGUAGE at ,
        $browserLanguages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);

        # convert the headers string to an array
        $browserLanguagesSize = sizeof($browserLanguages);
        for ($i = 0; $i < $browserLanguagesSize; $i++) {
            # explode string at ;
            $browserLanguage = explode(';', $browserLanguages[$i]);
            # cut string and place into array
            $browserLanguages[$i] = substr($browserLanguage[0], 0, 2);
        }

        # remove the duplicates and return the browser languages
        return array_values(array_unique($browserLanguages));
    }


}

?>