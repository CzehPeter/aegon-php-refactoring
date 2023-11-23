<?php

namespace Language\Services;

use Language\Config;
use Language\LanguageBatchBo;

/**
 * Class AppletLanguageXmlFileGenerator
 *
 * @package Language
 */
class AppletLanguageXmlFileGenerator {
	/**
	 * @param  array  $applets
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function generateFiles(array $applets): void {
		// it's a straight copy of the original generateAppletLanguageXmlFiles method, but without the $applets first line
		echo "\nGetting applet language XMLs..\n";

		foreach ($applets as $appletDirectory => $appletLanguageId) {
			echo " Getting > $appletLanguageId ($appletDirectory) language xmls..\n";
			$languages = LanguageBatchBo::getAppletLanguages($appletLanguageId);
			if (empty($languages)) {
				throw new \Exception('There is no available languages for the ' . $appletLanguageId . ' applet.');
			} else {
				echo ' - Available languages: ' . implode(', ', $languages) . "\n";
			}
			$path = Config::get('system.paths.root') . '/cache/flash';
			foreach ($languages as $language) {
				$xmlContent = LanguageBatchBo::getAppletLanguageFile($appletLanguageId, $language);
				$xmlFile    = $path . '/lang_' . $language . '.xml';
				if (strlen($xmlContent) == file_put_contents($xmlFile, $xmlContent)) {
					echo " OK saving $xmlFile was successful.\n";
				} else {
					throw new \Exception('Unable to save applet: (' . $appletLanguageId . ') language: (' . $language
							. ') xml (' . $xmlFile . ')!'
					);
				}
			}
			echo " < $appletLanguageId ($appletDirectory) language xml cached.\n";
		}

		echo "\nApplet language XMLs generated.\n";
	}
}