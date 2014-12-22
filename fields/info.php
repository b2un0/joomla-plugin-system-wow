<?php

/**
 * @author     Branko Wilhelm <branko.wilhelm@gmail.com>
 * @link       http://www.z-index.net
 * @copyright  (c) 2013 - 2015 Branko Wilhelm
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die;

JFormHelper::loadFieldClass('text');

class JFormFieldInfo extends JFormFieldText
{
    public $type = 'Info';

    protected function getInput()
    {
        if(version_compare(JVERSION, 3, '>=')) {
            $button = JHtml::_(
                'link',
                $this->getAttribute('info_url'),
                JText::_($this->getAttribute('info_label')),
                array('
                  target' => '_blank',
                    'class' => 'btn btn-info',
                    'title' => JText::_($this->getAttribute('info_title'))
                )
            );
        }else{
            $info_url = $this->element->getAttribute('info_url');
            $info_label = $this->element->getAttribute('info_label');
            $info_title = $this->element->getAttribute('info_title');

            $button = '<button type="button" onclick="window.open(\'' . JText::_($info_url) . '.\');" title="' . JText::_($info_title) . '">' . JText::_($info_label) . '</button>';

        }

        return parent::getInput() . '&nbsp;' . $button;
    }
}