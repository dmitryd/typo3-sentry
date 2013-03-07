<?php
if (version_compare(TYPO3_branch, '6.0', '>=')) {
	$extpath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('sentry');
}
else {
	$extpath = t3lib_extMgm::extPath('sentry');
}
return array(
	'TYPO3\\CMS\\Extension\\Sentry\\SentryErrorHandler' => $extpath . 'Classes/ErrorHandlers/SentryErrorHandler.php',
	'TYPO3\\CMS\\Extension\\Sentry\\SentryExceptionHandler' => $extpath . 'Classes/ErrorHandlers/SentryExceptionHandler.php',
	'tx_sentry_errorhandler' => $extpath . 'Classes/ErrorHandlers/class.tx_sentry_errorhandler.php',
	'tx_sentry_exceptionhandler' => $extpath . 'Classes/ErrorHandlers/class.tx_sentry_exceptionhandler.php',
);