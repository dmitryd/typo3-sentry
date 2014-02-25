<?php
namespace DmitryDulepov\Sentry\ErrorHandlers;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Dmitry Dulepov <dmitry.dulepov@gmail.com>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * This class contains a error handler, which is compatible with 6.x. It replaces
 * TYPO3 standard error handler to install the Sentry handler. Such complex
 * installation is necessary because TYPO3 requires interfaces to be implemented
 * for error handlers.
 *
 * @author Dmitry Dulepov <dmitry.dulepov@gmail.com>
 */
class SentryErrorHandler implements \TYPO3\CMS\Core\Error\ErrorHandlerInterface {

	/**
	 * Error mask (E_xxx constants) to catch.
	 *
	 * @var int
	 */
	static protected $errorMask;

	/** @var string */
	static protected $oldErrorHandlerClassName;

	/** @var \Raven_ErrorHandler */
	static protected $ravenErrorHandler;

	/** @var \TYPO3\CMS\Core\Error\ErrorHandlerInterface */
	protected $typo3ErrorHandler = null;

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
			$this->typo3ErrorHandler = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(self::$oldErrorHandlerClassName, $errorHandlerErrors);

			self::$ravenErrorHandler->registerErrorHandler(true, self::$errorMask);
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

	/**
	 * Prepares the class to replace TYPO3 standard handler with Raven-PHP
	 * implementation.
	 *
	 * @param \Raven_ErrorHandler $ravenErrorHandler
	 * @return void
	 */
	public static function initialize(\Raven_ErrorHandler $ravenErrorHandler, $errorMask) {
		self::$ravenErrorHandler = $ravenErrorHandler;
		self::$errorMask = $errorMask;

		// Save old error handler
		self::$oldErrorHandlerClassName = $GLOBALS['TYPO3_CONF_VARS']['SYS']['errorHandler'];

		$GLOBALS['TYPO3_CONF_VARS']['SYS']['errorHandler'] = __CLASS__;
	}
}
