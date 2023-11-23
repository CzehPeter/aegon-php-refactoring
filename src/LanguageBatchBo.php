<?php

namespace Language;

use Language\Services\AppletLanguageXmlFileGenerator;
use Language\Services\LanguageFileGenerator;

/**
 * Business logic related to generating language files.
 */
class LanguageBatchBo {
	/**
	 * Contains the applications which ones require translations.
	 *
	 * @var array
	 */
	protected static $applications = [];

	/**
	 * Language files generation
	 *
	 * @return void
	 * @throws \Exception
	 */
	public static function generateLanguageFiles() {
		self::$applications = Config::get('system.translated_applications');

		/**
		 * Generate language files
		 */
		(new LanguageFileGenerator())
				->generateFiles(self::$applications);
	}

	/**
	 * Gets the language files for the applet and puts them into the cache.
	 *
	 * @return void
	 * @throws Exception   If there was an error.
	 *
	 */
	public static function generateAppletLanguageXmlFiles() {
		$applets   = [
			'memberapplet' => 'JSM2_MemberApplet',
		];

		(new AppletLanguageXmlFileGenerator())
			->generateFiles($applets);
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
	 *
	 * @TODU: public from protected
	 */
	public static function getLanguageFile($application, $language) {
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
			self::checkForApiErrorResult($languageResponse);
		} catch (\Exception $e) {
			throw new \Exception('Error during getting language file: (' . $application . '/' . $language . ')');
		}

		// If we got correct data we store it.
		$destination = self::getLanguageCachePath($application) . $language . '.php';
		// If there is no folder yet, we'll create it.
		var_dump($destination);
		if (!is_dir(dirname($destination))) {
			mkdir(dirname($destination), 0755, true);
		}

		$result = file_put_contents($destination, $languageResponse['data']);

		return (bool) $result;
	}

	/**
	 * Gets the directory of the cached language files.
	 *
	 * @param  string  $application  The application.
	 *
	 * @return string   The directory of the cached language files.
	 */
	protected static function getLanguageCachePath($application) {
		return Config::get('system.paths.root') . '/cache/' . $application . '/';
	}

	/**
	 * Gets the available languages for the given applet.
	 *
	 * @param  string  $applet  The applet identifier.
	 *
	 * @return array   The list of the available applet languages.
	 *
	 * @TODO public from protected
	 */
	public static function getAppletLanguages($applet) {
		$result = ApiCall::call(
				'system_api',
				'language_api',
				[
						'system' => 'LanguageFiles',
						'action' => 'getAppletLanguages'
				],
				['applet' => $applet]
		);

		try {
			self::checkForApiErrorResult($result);
		} catch (\Exception $e) {
			throw new \Exception('Getting languages for applet (' . $applet . ') was unsuccessful ' . $e->getMessage());
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
	 *
	 * @TODO public from protected
	 */
	public static function getAppletLanguageFile($applet, $language) {
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
			self::checkForApiErrorResult($result);
		} catch (\Exception $e) {
			throw new \Exception('Getting language xml for applet: (' . $applet . ') on language: (' . $language . ') was unsuccessful: '
					. $e->getMessage()
			);
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
	 *
	 */
	protected static function checkForApiErrorResult($result) {
		// Error during the api call.
		if ($result === false || !isset($result['status'])) {
			throw new \Exception('Error during the api call');
		}
		// Wrong response.
		if ($result['status'] != 'OK') {
			throw new \Exception('Wrong response: '
					. (!empty($result['error_type']) ? 'Type(' . $result['error_type'] . ') ' : '')
					. (!empty($result['error_code']) ? 'Code(' . $result['error_code'] . ') ' : '')
					. ((string) $result['data'])
			);
		}
		// Wrong content.
		if ($result['data'] === false) {
			throw new \Exception('Wrong content!');
		}
	}
}
