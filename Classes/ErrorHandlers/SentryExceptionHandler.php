<?php
namespace TYPO3\CMS\Extension\Sentry;
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
 * This class contains an exception handler, which is compatible with 6.x. It
 * replaces TYPO3 standard error handler to install the Sentry handler. Such
 * complex installation is necessary because TYPO3 requires interfaces to be
 * implemented for error handlers.
 *
 * @author Dmitry Dulepov <dmitry.dulepov@gmail.com>
 */
class SentryExceptionHandler extends \TYPO3\CMS\Core\Error\AbstractExceptionHandler {

	/** @var string */
	static protected $oldExceptionHandlerClassName;

	/** @var \Raven_ErrorHandler */
	static protected $ravenErrorHandler;

	/**
	 * Constructs this exception handler - registers itself as the default exception handler.
	 */
	public function __construct() {
		$extConf = @unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['sentry']);
		if ($extConf['passErrorsToTypo3']) {
			if (self::$oldExceptionHandlerClassName) {
				// The code below will set up a TYPO3 exception handler
				\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(self::$oldExceptionHandlerClassName);
			}

			// We always register exception handler for Sentry, regardless of TYPO3 settings!
			self::$ravenErrorHandler->registerExceptionHandler(true);
		}
	}

	/**
	 * Formats and echoes the exception as XHTML.
	 *
	 * @param \Exception $exception The exception object
	 * @return void
	 */
	public function echoExceptionWeb(\Exception $exception) {
		// Empty, not used directly
	}

	/**
	 * Formats and echoes the exception for the command line
	 *
	 * @param \Exception $exception The exception object
	 * @return void
	 */
	public function echoExceptionCLI(\Exception $exception) {
		// Empty, not used directly
	}

	/**
	 * Prepares the class to replace TYPO3 standard handler with Raven-PHP
	 * implementation.
	 *
	 * @param \Raven_ErrorHandler $ravenErrorHandler
	 * @return void
	 */
	public static function initialize(\Raven_ErrorHandler $ravenErrorHandler) {
		self::$ravenErrorHandler = $ravenErrorHandler;

		// Save old error handler
		self::$oldExceptionHandlerClassName = $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['errors']['exceptionHandler'];

		$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['errors']['exceptionHandler'] = 'TYPO3\\CMS\\Extension\\Sentry\\SentryExceptionHandler';
	}
}
