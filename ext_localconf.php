<?php
if (defined('TYPO3_mode')) {
	die();
}

if (!function_exists('sentry_register')) {
	/**
	 * Registers exception handler to the Sentry.
	 *
	 * @return void
	 */
	function sentry_register() {
		$extConf = @unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['sentry']);
		if (is_array($extConf) && isset($extConf['sentryDSN'])) {
			// Register Raven autoloader
			if (version_compare(TYPO3_branch, '6.0', '>=')) {
				$ravenPhpAutoloaderPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('sentry', 'lib/raven-php/lib/Raven/Autoloader.php');
			}
			else {
				$ravenPhpAutoloaderPath = t3lib_extMgm::extPath('sentry', 'lib/raven-php/lib/Raven/Autoloader.php');
			}
			require_once($ravenPhpAutoloaderPath);
			Raven_Autoloader::register();

			// Set error handler
			$GLOBALS['SENTRY_CLIENT'] = new Raven_Client($extConf['sentryDSN']);
			$ravenErrorHandler = new Raven_ErrorHandler($GLOBALS['SENTRY_CLIENT']);
			// Early error handler
			$ravenErrorHandler->registerErrorHandler(false);
			$ravenErrorHandler->registerExceptionHandler(false);
			// Make sure that TYPO3 does not override our handler
			\TYPO3\CMS\Extension\Sentry\SentryErrorHandler::initialize($ravenErrorHandler);
			\TYPO3\CMS\Extension\Sentry\SentryExceptionHandler::initialize($ravenErrorHandler);
		}
	}

	sentry_register();
}

?>