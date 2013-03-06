<?php
$extpath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('sentry');
return array(
	'TYPO3\\CMS\\Extension\\Sentry\\SentryErrorHandler' => $extpath . 'Classes/ErrorHandlers/SentryErrorHandler.php',
	'TYPO3\\CMS\\Extension\\Sentry\\SentryExceptionHandler' => $extpath . 'Classes/ErrorHandlers/SentryExceptionHandler.php',
);