<?php
namespace DmitryDulepov\Sentry\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

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
		throw new \Exception('Test exception from EXT:sfperrorhandler', 0x07031973);
	}
}
