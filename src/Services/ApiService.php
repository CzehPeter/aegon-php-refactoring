<?php

namespace Language\Services;

use Language\ApiCall;

/**
 * Class LanguageFileGenerator
 *
 * Generates language files
 */
class ApiService
{

    /**
     * @param  string  $language
     *
     * @return array|false
     */
    public static function getLanguageFile(
        string $language
    ): ?array {
        return ApiCall::call(
            'system_api',
            'language_api',
            [
                        'system' => 'LanguageFiles',
                        'action' => 'getLanguageFile'
                ],
            [
                        'language' => $language
                ]
        );
    }

    /**
     * @param  string  $applet
     *
     * @return array|false
     */
    public static function getAppletLanguages(
        string $applet
    ): ?array {
        return ApiCall::call(
            'system_api',
            'language_api',
            [
                        'system' => 'LanguageFiles',
                        'action' => 'getAppletLanguages'
                ],
            [
                        'applet' => $applet
                ]
        );
    }


    /**
     * @param  string  $applet
     * @param  string  $language
     *
     * @return array|false
     */
    public static function getAppletLanguageFile(
        string $applet,
        string $language
    ): ?array {
        return ApiCall::call(
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
    }

    /**
     * @param  mixed  $result
     *
     * @return void
     * @throws \Exception
     */
    public static function checkForApiErrorResult(
        mixed $result
    ): void {
        // Error during the api call.
        if (!isset($result['status'])) {
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
