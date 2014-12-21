<?php

/**
 * @author     Branko Wilhelm <branko.wilhelm@gmail.com>
 * @link       http://www.z-index.net
 * @copyright  (c) 2013 - 2015 Branko Wilhelm
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die;

JFormHelper::loadFieldClass('radio');

class JFormFieldAjax extends JFormFieldRadio
{
    public $type = 'Ajax';

    private $jversion = 3;

    protected function getInput()
    {
        return version_compare(JVERSION, $this->jversion, '>=') ? parent::getInput() : '';
    }

    protected function getLabel()
    {
        return version_compare(JVERSION, $this->jversion, '>=') ? parent::getLabel() : '';
    }

    protected function getOptions()
    {
        return array(
            JHtml::_('select.option', 1, JText::_('JYES')),
            JHtml::_('select.option', 0, JText::_('JNO'))
        );
    }
}