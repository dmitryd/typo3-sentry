<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Dmitry Dulepov (dmitry.dulepov@gmail.com)
 *  All rights reserved
 *
 *  This script is part of the Typo3 project. The Typo3 project is
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
namespace DmitryDulepov\Sentry\ErrorHandlers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\AbstractContentObject;

/**
 * This class contains a TYPO3 7.0 FE exception handler. It has to be an XCLASS
 * because somebody hard-coded extension class name in the core.
 *
 * @package DmitryDulepov\Sentry\Controller
 *
 * @author Dmitry Dulepov <dmitry.dulepov@gmail.com>
 */
class SentryExceptionHandlerFrontend extends \TYPO3\CMS\Frontend\ContentObject\Exception\ProductionExceptionHandler implements \TYPO3\CMS\Core\SingletonInterface {

	/** @var \Raven_ErrorHandler */
	protected $ravenErrorHandler;

	public function __construct(\Raven_ErrorHandler $ravenErrorHandler) {
		$this->ravenErrorHandler = $ravenErrorHandler;
	}

	/**
	 * Handles exceptions thrown during rendering of content objects
	 * The handler can decide whether to re-throw the exception or
	 * return a nice error message for production context.
	 *
	 * @param \Exception $exception
	 * @param AbstractContentObject $contentObject
	 * @param array $contentObjectConfiguration
	 * @return string
	 */
	public function handle(\Exception $exception, AbstractContentObject $contentObject = NULL, $contentObjectConfiguration = array()) {
		// TRUE below prevents Raven from calling a previous handler (not FE handler)
		$this->ravenErrorHandler->handleException($exception, TRUE);

		$extConf = @unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['sentry']);
		if ($extConf['passErrorsToTypo3']) {
			parent::handle($exception, $contentObject, $contentObjectConfiguration);
		}
	}

	static public function initialize($ravenErrorHandler) {
		GeneralUtility::makeInstance(__CLASS__, $ravenErrorHandler);
	}

}
