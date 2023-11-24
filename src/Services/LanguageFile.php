<?php

namespace Language\Services;

use Language\Services\ApiService;
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
     * @param  string  $destination
     * @param  string  $languageResponse
     *
     * @return bool
     */
    public function putLanguageFile(
        string $fileName,
        string $fileContent
    ): bool {
        var_dump($fileName);
        if (!is_dir(dirname($fileName))) {
            mkdir(dirname($fileName), 0755, true);
        }

        return (bool) file_put_contents(
            $fileName,
            $fileContent
        );
    }

    /**
     * @param  string  $xmlFile
     * @param  string  $xmlContent
     *
     * @return bool
     */
    public function putAppletXmlFile(
        string $xmlFile,
        string $xmlContent
    ): bool {
        return (
            strlen($xmlContent) == file_put_contents(
                $xmlFile,
                $xmlContent
            )
        );
    }
}
