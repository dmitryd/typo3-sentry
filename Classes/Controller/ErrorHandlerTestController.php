<?php
namespace DmitryDulepov\Sentry\Controller;

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

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * This class contains a controller to simulate PHP error, warning and exception.
 *
 * @package DmitryDulepov\Sentry\Controller
 *
 * @author Dmitry Dulepov <dmitry.dulepov@gmail.com>
 */
class ErrorHandlerTestController extends ActionController {

	/**
	 * Shows a menu with choices for further actions.
	 */
	public function indexAction() {
	}

	/**
	 * Simulates a PHP warning.
	 */
	public function phpWarningAction() {
		preg_match('There will be a warning about missing delimiter here!', 'test');
	}

	/**
	 * Simulates a PHP error.
	 */
	public function phpErrorAction() {
		$instance = NULL;
		/** @noinspection PhpUndefinedMethodInspection */
		$instance->dummy();
	}

	/**
	 * Simulates a PHP exception.
	 */
	public function phpExceptionAction() {
		throw new \Exception('Test exception from EXT:sentry', 0x07031973);
	}
}
