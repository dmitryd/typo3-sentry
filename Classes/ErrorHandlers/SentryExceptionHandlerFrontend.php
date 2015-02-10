<?php
namespace DmitryDulepov\Sentry\ErrorHandlers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\AbstractContentObject;

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
