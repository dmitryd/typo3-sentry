<?php
namespace TYPO3\CMS\Extension\Sentry;

class SentryErrorHandler implements \TYPO3\CMS\Core\Error\ErrorHandlerInterface {

	static protected $oldErrorHandler;

	/** @var \Raven_ErrorHandler */
	static protected $ravenErrorHandler;

	/** @var \TYPO3\CMS\Core\Error\ErrorHandlerInterface */
	protected $typo3ErrorHandler = NULL;

	/**
	 * Registers this class as default error handler
	 *
	 * @param integer $errorHandlerErrors The integer representing the E_* error level which should be
	 * @return void
	 */
	public function __construct($errorHandlerErrors) {
		$extConf = @unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['sentry']);
		if ($extConf['passErrorsToTypo3']) {
			// The code below will set up a TYPO3 error handler
			$this->typo3ErrorHandler = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(self::$oldErrorHandler);

			self::$ravenErrorHandler->registerErrorHandler(true, E_ALL & ~(E_STRICT | E_NOTICE | E_DEPRECATED));
		}
	}

	/**
	 * Defines which error levels should result in an exception thrown.
	 *
	 * @param integer $exceptionalErrors The integer representing the E_* error level to handle as exceptions
	 * @return void
	 */
	public function setExceptionalErrors($exceptionalErrors) {
		if ($this->typo3ErrorHandler) {
			$this->typo3ErrorHandler->setExceptionalErrors($exceptionalErrors);
		}
	}

	/**
	 * Handles an error.
	 * If the error is registered as exceptionalError it will by converted into an exception, to be handled
	 * by the configured exceptionhandler. Additionall the error message is written to the configured logs.
	 * If TYPO3_MODE is 'BE' the error message is also added to the flashMessageQueue, in FE the error message
	 * is displayed in the admin panel (as TsLog message)
	 *
	 * @param integer $errorLevel The error level - one of the E_* constants
	 * @param string $errorMessage The error message
	 * @param string $errorFile Name of the file the error occurred in
	 * @param integer $errorLine Line number where the error occurred
	 * @return void
	 * @throws \TYPO3\CMS\Core\Error\Exception with the data passed to this method if the error is registered as exceptionalError
	 */
	public function handleError($errorLevel, $errorMessage, $errorFile, $errorLine) {
		// Empty
	}

	public static function initialize($ravenErrorHandler) {
		self::$ravenErrorHandler = $ravenErrorHandler;

		// Save old error handler
		self::$oldErrorHandler = $GLOBALS['TYPO3_CONF_VARS']['SYS']['errorHandler'];

		$GLOBALS['TYPO3_CONF_VARS']['SYS']['errorHandler'] = 'TYPO3\\CMS\\Extension\\Sentry\\SentryErrorHandler';
	}
}
