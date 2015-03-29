<?php

/**
 * @author     Branko Wilhelm <branko.wilhelm@gmail.com>
 * @link       http://www.z-index.net
 * @copyright  (c) 2013 - 2015 Branko Wilhelm
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die;

class WoWAdapterGuildOx extends WoWAdapterAbstract
{
    /**
     * @return mixed
     */
    public function getData()
    {
        $this->url = 'http://www.guildox.com/go/guildxml.aspx?j=1&n=' . $this->params->get('guild') . '&r=' . JApplicationHelper::stringURLSafe($this->params->get('realm')) . '-' . $this->params->get('region');

        $result = $this->getRemote($this->url);

        $result->body = json_decode($result->body);

        if ($result->code != 200)
        {
            $msg = JText::sprintf('Server Error: %s', JHtml::_('link', $this->url, $result->code, array('target' => '_blank')));
            throw new RuntimeException($msg);
        }

        return $result;
    }
}