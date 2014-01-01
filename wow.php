<?php

/**
 * @author     Branko Wilhelm <branko.wilhelm@gmail.com>
 * @link       http://www.z-index.net
 * @copyright  (c) 2013 Branko Wilhelm
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die;

class plgSystemWow extends JPlugin
{
    public function onBeforeCompileHead()
    {
        $app = JFactory::getApplication();
        if ($app->isAdmin()) {
            return;
        }

        if (version_compare(JVERSION, 3, '>=')) {
            JHtml::_('jquery.framework');
        }

        $doc = JFactory::getDocument();
        $doc->addScript('media/wow/wow.js');
        $doc->addStyleSheet('media/wow/wow.css');

        $js = 'window.wow.base="' . Juri::base(true) . '";';
        $js .= 'window.wow.Itemid=' . $app->input->getInt('Itemid') . ';';

        $doc->addScriptDeclaration($js);
    }

    public function onAfterRoute()
    {
        if (JFactory::getApplication()->isAdmin() || version_compare(JVERSION, 3.2, '>=')) {
            return;
        }

        $input = JFactory::getApplication()->input;

        if ($input->getWord('option') == 'com_ajax' && $module = $input->get('module')) {

            JLoader::import('joomla.application.module.helper');

            $moduleObject = JModuleHelper::getModule('mod_' . $module);

            if ($moduleObject->id != 0) {
                $helperFile = JPATH_BASE . '/modules/mod_' . $module . '/helper.php';

                if (strpos($module, '_')) {
                    $parts = explode('_', $module);
                } elseif (strpos($module, '-')) {
                    $parts = explode('-', $module);
                }

                if ($parts) {
                    $class = 'mod';
                    foreach ($parts as $part) {
                        $class .= ucfirst($part);
                    }
                    $class .= 'Helper';
                } else {
                    $class = 'mod' . ucfirst($module) . 'Helper';
                }

                $method = $input->get('method') ? $input->get('method') : 'get';

                if (is_file($helperFile)) {
                    require_once $helperFile;

                    if (method_exists($class, $method . 'Ajax')) {
                        try {
                            $results = call_user_func($class . '::' . $method . 'Ajax');
                        } catch (Exception $e) {
                            $results = $e;
                        }

                    } // Method does not exist
                    else {
                        $results = new LogicException(sprintf('Method %s does not exist', $method . 'Ajax'), 404);
                    }
                } // The helper file does not exist
                else {
                    $results = new RuntimeException(sprintf('The file at %s does not exist', 'mod_' . $module . '/helper.php'), 404);
                }
            } // Module is not published, you do not have access to it, or it is not assigned to the current menu item
            else {
                $results = new LogicException(sprintf('Module %s is not published, you do not have access to it, or it\'s not assigned to the current menu item', 'mod_' . $module), 404);
            }

            $this->out($results);
        }
    }

    private function out($results)
    {
        $app = JFactory::getApplication();

        if ($results instanceof Exception) {
            // Log an error
            JLog::add($results->getMessage(), JLog::ERROR);

            // Set status header code
            $app->setHeader('status', $results->getCode(), true);

            // Echo exception type and message
            $out = get_class($results) . ': ' . $results->getMessage();
        } // Output string/ null
        elseif (is_scalar($results)) {
            $out = (string)$results;
        } // Output array/ object
        else {
            $out = implode((array)$results);
        }

        echo $out;

        $app->close();
    }
}