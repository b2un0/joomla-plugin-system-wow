<?php

/**
 * @author     Branko Wilhelm <branko.wilhelm@gmail.com>
 * @link       http://www.z-index.net
 * @copyright  (c) 2013 - 2016 Branko Wilhelm
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die;

class plgSystemWowInstallerScript
{
    const JVERSION = 3.2;

    public function preflight()
    {
        if (!version_compare(JVERSION, self::JVERSION, '>=')) {
            $link = JHtml::_('link', 'index.php?option=com_joomlaupdate', 'Joomla! ' . self::JVERSION);
            JFactory::getApplication()->enqueueMessage(sprintf('You need %s or newer to install this extension', $link), 'error');

            return false;
        }

        return true;
    }

    public function install(JAdapterInstance $adapter)
    {
        $lib = $adapter->getParent()->getPath('source') . '/library/';

        $installer = new JInstaller;
        $installer->install($lib);
    }

    public function update(JAdapterInstance $adapter)
    {
        $this->install($adapter);
    }
}
