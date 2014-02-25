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
			$running6x = (version_compare(TYPO3_branch, '6.0', '>='));

			// Register Raven autoloader
			if ($running6x) {
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

			$errorMask = E_ALL & ~(E_DEPRECATED | E_NOTICE);
			if (!$extConf['catchStrict']) {
				$errorMask &= ~E_STRICT;
			}

			// Early error handler
			$ravenErrorHandler->registerErrorHandler(false, $errorMask);
			$ravenErrorHandler->registerExceptionHandler(false);

			// Make sure that TYPO3 does not override our handler
			if ($running6x) {
				\DmitryDulepov\Sentry\ErrorHandlers\SentryErrorHandler::initialize($ravenErrorHandler, $errorMask);
				\DmitryDulepov\Sentry\ErrorHandlers\SentryExceptionHandler::initialize($ravenErrorHandler);
			}
			else {
				tx_sentry_errorhandler::initialize($ravenErrorHandler, $errorMask);
				tx_sentry_exceptionhandler::initialize($ravenErrorHandler);
			}
		}
	}

	sentry_register();
}

?>