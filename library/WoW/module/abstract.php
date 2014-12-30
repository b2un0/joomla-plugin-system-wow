<?php

/**
 * @author     Branko Wilhelm <branko.wilhelm@gmail.com>
 * @link       http://www.z-index.net
 * @copyright  (c) 2013 - 2015 Branko Wilhelm
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die;

class WoWModuleAbstract
{
    /**
     * @var stdClass
     */
    protected $params = null;

    public function __construct(JRegistry $params)
    {
        if (version_compare(JVERSION, 3.2, '<')) {
            $params->set('ajax', 0);
        }

        $this->params = new stdClass;
        $this->params->module = $params;
        $this->params->global = WoW::getInstance()->params;
    }

    public static function getAjax()
    {
        $module = JModuleHelper::getModule('mod_' . JFactory::getApplication()->input->get('module'));

        if (empty($module)) {
            return false;
        }

        JFactory::getLanguage()->load($module->module);

        $params = new JRegistry($module->params);
        $params->set('ajax', 0);

        ob_start();

        require(JPATH_ROOT . '/modules/' . $module->module . '/' . $module->module . '.php');

        return ob_get_clean();
    }

    public static function getData(JRegistry $params)
    {
        if ($params->get('ajax')) {
            return;
        }

        $instance = new static($params);

        return $instance->getInternalData();
    }

    protected function getInternalData()
    {
        return null;
    }

}