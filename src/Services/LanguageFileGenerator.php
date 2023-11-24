<?php

namespace Language\Services;

use Language\Services\LanguageFile;

/**
 * Class LanguageFileGenerator
 *
 * Generates language files
 */
class LanguageFileGenerator
{
    private LanguageFile $languageFile;

    public function __construct()
    {
        $this->languageFile = new LanguageFile();
    }

    /**
     * @param  array  $applications
     *
     * @return void
     * @throws \Exception
     */
    public function generateFiles(
        array $applications
    ): void {
        //it's a straight copy of the original generateLanguageFiles method, without the first line
        echo "\nGenerating language files\n";
        foreach ($applications as $application => $languages) {
            echo "[APPLICATION: " . $application . "]\n";
            foreach ($languages as $language) {
                echo "\t[LANGUAGE: " . $language . "]";

                if ($this->languageFile->getLanguageFile($application, $language)) {
                    echo " OK\n";
                } else {
                    throw new \Exception('Unable to generate language file!');
                }
            }
        }
    }
}
