<?php

namespace Language\Services;

use Language\LanguageBatchBo;

/**
 * Class LanguageFileGenerator
 *
 * Generates language files
 */
class LanguageFileGenerator
{
	/**
	 * @param  array  $applications
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function generateFiles(array $applications): void
	{
		//it's a straight copy of the original generateLanguageFiles method, without the first line
		echo "\nGenerating language files\n";
		foreach ($applications as $application => $languages) {
			echo "[APPLICATION: " . $application . "]\n";
			foreach ($languages as $language) {
				echo "\t[LANGUAGE: " . $language . "]";
				if (LanguageBatchBo::getLanguageFile($application, $language)) {
					echo " OK\n";
				} else {
					throw new \Exception('Unable to generate language file!');
				}
			}
		}
	}
}
