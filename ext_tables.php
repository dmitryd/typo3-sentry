<?php
//************************************************************************
// Plugins
//************************************************************************

$extConf = @unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['sentry']);
if (is_array($extConf) && isset($extConf['enableTestPlugin']) && $extConf['enableTestPlugin']) {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin('sentry', 'ErrorHandlerTest', 'Sentry: Error handler test plugin');
}
unset($extConf);
