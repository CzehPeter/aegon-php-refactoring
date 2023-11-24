<?php

namespace Language\Services;

use Language\ApiCall;
use Language\Config;
use Language\LanguageBatchBo;

/**
 * Class LanguageFile
 *
 * @package Language
 */
class LanguageFile
{
    /**
     * Gets the language file for the given language and stores it.
     *
     * @param  string  $application  The name of the application.
     * @param  string  $language     The identifier of the language.
     *
     * @return bool   The success of the operation.
     * @throws CurlException   If there was an error during the download of the language file.
     */
    public function getLanguageFile(
        string $application,
        string $language
    ): bool {
        $result           = false;
        $languageResponse = ApiCall::call(
            'system_api',
            'language_api',
            [
                        'system' => 'LanguageFiles',
                        'action' => 'getLanguageFile'
                ],
            ['language' => $language]
        );

        try {
            $this->checkForApiErrorResult($languageResponse);
        } catch (\Exception $e) {
            throw new \Exception('Error during getting language file: (' . $application . '/' . $language . ')');
        }

        // If we got correct data we store it.
        $destination = $this->getLanguageCachePath($application) . $language . '.php';
        // If there is no folder yet, we'll create it.
        var_dump($destination);
        if (!is_dir(dirname($destination))) {
            mkdir(dirname($destination), 0755, true);
        }

        $result = file_put_contents($destination, $languageResponse['data']);

        return (bool) $result;
    }

    /**
     * Gets a language xml for an applet.
     *
     * @param  string  $applet    The identifier of the applet.
     * @param  string  $language  The language identifier.
     *
     * @return string|false   The content of the language file or false if weren't able to get it.
     */
    public function getAppletLanguages(
        string $applet,
        string $language
    ): string|false {
        $result = ApiCall::call(
            'system_api',
            'language_api',
            [
                        'system' => 'LanguageFiles',
                        'action' => 'getAppletLanguageFile'
                ],
            [
                        'applet'   => $applet,
                        'language' => $language
                ]
        );

        try {
            $this->checkForApiErrorResult($result);
        } catch (\Exception $e) {
            throw new \Exception('Getting language xml for applet: (' . $applet . ') on language: (' . $language . ') was unsuccessful: '
                    . $e->getMessage());
        }

        return $result['data'];
    }

    /**
     * Gets the directory of the cached language files.
     *
     * @param  string  $application  The application.
     *
     * @return string   The directory of the cached language files.
     *
     * @TODO it was protected static
     */
    private function getLanguageCachePath(string $application): string
    {
        return Config::get('system.paths.root') . '/cache/' . $application . '/';
    }

    /**
     * Checks the api call result.
     *
     * @param  mixed  $result  The api call result to check.
     *
     * @return void
     * @throws Exception   If the api call was not successful.
     *
     * @TODO it was protected static
     */
    private function checkForApiErrorResult($result)
    {
        // Error during the api call.
        if ($result === false || !isset($result['status'])) {
            throw new \Exception('Error during the api call');
        }
        // Wrong response.
        if ($result['status'] != 'OK') {
            throw new \Exception('Wrong response: '
                    . (!empty($result['error_type']) ? 'Type(' . $result['error_type'] . ') ' : '')
                    . (!empty($result['error_code']) ? 'Code(' . $result['error_code'] . ') ' : '')
                    . ((string) $result['data']));
        }
        // Wrong content.
        if ($result['data'] === false) {
            throw new \Exception('Wrong content!');
        }
    }
}
