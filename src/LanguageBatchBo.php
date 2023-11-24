<?php

namespace Language;

use Language\Services\AppletLanguageXmlFileGenerator;
use Language\Services\LanguageFileGenerator;

/**
 * Business logic related to generating language files.
 */
class LanguageBatchBo
{
    /**
     * Language files generation
     *
     * @return void
     * @throws \Exception
     */
    public static function generateLanguageFiles(): void
    {
        $applications = Config::get('system.translated_applications');

        /**
         * Generate language files
         */
        $languageFileGenerator = new LanguageFileGenerator();
        $languageFileGenerator->generateFiles($applications);
    }

    /**
     * Gets the language files for the applet and puts them into the cache.
     *
     * @return void
     * @throws Exception   If there was an error.
     */
    public static function generateAppletLanguageXmlFiles(): void
    {
        $applets = ['memberapplet' => 'JSM2_MemberApplet'];

        /**
         * Generate applet language xml files
         */
        $appletLanguageXml = new AppletLanguageXmlFileGenerator();
        $appletLanguageXml->generateFiles($applets);
    }
}
