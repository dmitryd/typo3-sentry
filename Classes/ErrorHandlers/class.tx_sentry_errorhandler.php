<?php

class tx_sentry_errorhandler implements t3lib_error_ErrorHandlerInterface {

	static protected $errorMask;

	static protected $oldErrorHandler;

	/** @var Raven_ErrorHandler */
	static protected $ravenErrorHandler;

	/** @var t3lib_error_ErrorHandlerInterface */
	protected $typo3ErrorHandler = NULL;

	/**
	 * Registers this class as default error handler
	 *
	 * @param integer    The integer representing the E_* error level which should be
	 *                    handled by the registered error handler.
	 * @return void
	 */
	public function __construct($errorHandlerErrors) {
		$extConf = @unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['sentry']);
		if ($extConf['passErrorsToTypo3']) {
			// The code below will set up a TYPO3 error handler
			$this->typo3ErrorHandler = t3lib_div::makeInstance(self::$oldErrorHandler, $errorHandlerErrors);

			self::$ravenErrorHandler->registerErrorHandler(true, self::$errorMask);
		}
	}

	/**
	 * Defines which error levels should result in an exception thrown.
	 *
	 * @param integer    The integer representing the E_* error level to handle as exceptions
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
	 * @param integer    The error level - one of the E_* constants
	 * @param string    The error message
	 * @param string    Name of the file the error occurred in
	 * @param integer    Line number where the error occurred
	 * @return void
	 * @throws t3lib_error_Exception with the data passed to this method if the error is registered as exceptionalError
	 */
	public function handleError($errorLevel, $errorMessage, $errorFile, $errorLine) {
		// Empty
	}


	public static function initialize($ravenErrorHandler, $errorMask) {
		self::$ravenErrorHandler = $ravenErrorHandler;
		self::$errorMask = $errorMask;

		// Save old error handler
		self::$oldErrorHandler = $GLOBALS['TYPO3_CONF_VARS']['SYS']['errorHandler'];

		$GLOBALS['TYPO3_CONF_VARS']['SYS']['errorHandler'] = 'tx_sentry_errorhandler';
	}
}
