<?php

namespace Language;

use Language\Services\ApiService;
use Language\Services\LanguageFile;
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

        foreach ($applications as $application => $languages) {
            echo "[APPLICATION: " . $application . "]\n";

            foreach ($languages as $language) {
                echo "\t[LANGUAGE: " . $language . "]";

                if (self::getLanguageFile($application, $language)) {
                    echo " OK\n";
                } else {
                    throw new \Exception('Unable to generate language file!');
                }
            }
        }
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
        $languageFile = new LanguageFile();

        foreach ($applets as $appletDirectory => $appletLanguageId) {
            echo " Getting > $appletLanguageId ($appletDirectory) language xmls..\n";
            $languages = self::getAppletLanguages($appletLanguageId);

            echo ' - Available languages: ' . implode(', ', $languages) . "\n";

            $path = Config::get('system.paths.root') . '/cache/flash';
            foreach ($languages as $language) {
                $xmlFile = $path . '/lang_' . $language . '.xml';

                if ($languageFile->putAppletXmlFile(
                    $xmlFile,
                    self::getAppletLanguageFile($appletLanguageId, $language)
                )
                ) {
                    echo " OK saving $xmlFile was successful.\n";
                } else {
                    throw new \Exception('Unable to save applet: (' . $appletLanguageId . ') language: (' . $language
                            . ') xml (' . $xmlFile . ')!');
                }
            }
            echo " < $appletLanguageId ($appletDirectory) language xml cached.\n";
        }

        echo "\nApplet language XMLs generated.\n";
    }

    /**
     * Gets the language file for the given language and stores it.
     *
     * @param  string  $application  The name of the application.
     * @param  string  $language     The identifier of the language.
     *
     * @return bool   The success of the operation.
     * @throws CurlException   If there was an error during the download of the language file.
     *
     */
    protected static function getLanguageFile(
        string $application,
        string $language
    ): bool {
        $languageFile = new LanguageFile();

        $languageResponse = ApiService::getLanguageFile($language);

        try {
            self::checkForApiErrorResult($languageResponse);
        } catch (\Exception $e) {
            throw new \Exception('Error during getting language file: (' . $application . '/' . $language . ')');
        }

        // If we got correct data we store it.
        $destination = self::getLanguageCachePath($application) . $language . '.php';

        // If there is no folder yet, we'll create it.
        return $languageFile->putLanguageFile(
            $destination,
            $languageResponse['data']
        );
    }

    /**
     * Gets the directory of the cached language files.
     *
     * @param  string  $application  The application.
     *
     * @return string   The directory of the cached language files.
     */
    protected static function getLanguageCachePath(
        string $application
    ): string {
        return Config::get('system.paths.root') . '/cache/' . $application . '/';
    }

    /**
     * Gets the available languages for the given applet.
     *
     * @param  string  $applet  The applet identifier.
     *
     * @return array   The list of the available applet languages.
     * @throws Exception  If there was an error while retrieving the applet languages.
     */
    protected static function getAppletLanguages(
        string $applet
    ): array {
        $result = ApiService::getAppletLanguages($applet);

        try {
            self::checkForApiErrorResult($result);
        } catch (\Exception $e) {
            throw new \Exception('Getting languages for applet (' . $applet . ') was unsuccessful ' . $e->getMessage());
        }

        if (empty($result['data'])) {
            throw new \Exception('There is no available languages for the ' . $applet . ' applet.');
        }

        return $result['data'];
    }

    /**
     * Gets a language xml for an applet.
     *
     * @param  string  $applet    The identifier of the applet.
     * @param  string  $language  The language identifier.
     *
     * @return string|false   The content of the language file or false if weren't able to get it.
     */
    protected static function getAppletLanguageFile(
        string $applet,
        string $language
    ): string|false {
        $result = ApiService::getAppletLanguageFile($applet, $language);

        try {
            self::checkForApiErrorResult($result);
        } catch (\Exception $e) {
            throw new \Exception('Getting language xml for applet: (' . $applet . ') on language: (' . $language . ') was unsuccessful: '
                    . $e->getMessage());
        }

        return $result['data'];
    }

    /**
     * Checks the api call result.
     *
     * @param  mixed  $result  The api call result to check.
     *
     * @return void
     * @throws Exception   If the api call was not successful.
     */
    protected static function checkForApiErrorResult(
        mixed $result
    ): void {
        ApiService::checkForApiErrorResult($result);
    }
}
