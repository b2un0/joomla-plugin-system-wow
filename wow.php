<?php

/**
 * @author     Branko Wilhelm <branko.wilhelm@gmail.com>
 * @link       http://www.z-index.net
 * @copyright  (c) 2013 - 2015 Branko Wilhelm
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die;

class plgSystemWow extends JPlugin
{
    protected $autoloadLanguage = true;

    public function onAfterInitialise()
    {
        JLoader::register('WoW', JPATH_LIBRARIES . '/WoW/WoW.php');

        $this->loadLanguage();

        if (class_exists('WoW'))
        {
            WoW::getInstance($this->params); // define instance with params
        } else
        {
            JFactory::getApplication()->enqueueMessage(JText::_('PLG_SYSTEM_WOW_LIBRARY_MISSING'), 'error');
        }
    }

    public function onBeforeRender()
    {
        $app = JFactory::getApplication();
        $doc = JFactory::getDocument();
        $user = JFactory::getUser();

        if ($app->isAdmin())
        {
            if (
                !$this->params->get('apikey') ||
                !$this->params->get('guild') ||
                !$this->params->get('realm') ||
                !$this->params->get('region')
            )
            {
                $app->enqueueMessage(JText::_('PLG_SYSTEM_WOW_CONFIGURATION_MISSING'), 'error');

                return true;
            }

            if ($user->id && $this->params->get('j25_eol', true) && version_compare(JVERSION, 3, '<'))
            {
                $app->enqueueMessage(JText::_('PLG_SYSTEM_WOW_J25_EOL_SUPPORT_DROPPED'), 'error');
            }

            if ($doc instanceof JDocumentHTML && $this->params->get('status', true))
            {
                $buffer = $doc->getBuffer('modules', 'status');

                $link = '<a href="' . JRoute::_('index.php?option=com_plugins&view=plugins&filter_folder=system&filter_search=wow') . '">';
                $link .= $this->params->get('guild') . ' @ ' . strtoupper($this->params->get('region')) . '-' . $this->params->get('realm');
                $link .= '</a>';

                if (version_compare(JVERSION, 3, '>='))
                {
                    $buffer .= '<div class="btn-group">';
                    $buffer .= '<span class="badge">WoW</span> ';
                    $buffer .= $link;
                    $buffer .= '</div>';
                } else
                {
                    $buffer .= '<span style="background: url(../media/wow/wow.png) 3px 3px no-repeat;">';
                    $buffer .= $link;
                    $buffer .= '</span>';
                }

                $doc->setBuffer($buffer, 'modules', 'status');
            }
        }
    }

    public function onBeforeCompileHead()
    {
        $app = JFactory::getApplication();
        if ($app->isAdmin())
        {
            return;
        }

        if (version_compare(JVERSION, 3, '>='))
        {
            JHtml::_('jquery.framework');
        }

        $doc = JFactory::getDocument();
        $doc->addScript('media/wow/wow.js');
        $doc->addStyleSheet('media/wow/wow.css');

        $js = 'window.wow.base="' . JUri::base(true) . '";';
        $js .= 'window.wow.Itemid=' . $app->input->getInt('Itemid') . ';';

        $doc->addScriptDeclaration($js);
    }
}