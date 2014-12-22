<?php

/**
 * @author     Branko Wilhelm <branko.wilhelm@gmail.com>
 * @link       http://www.z-index.net
 * @copyright  (c) 2013 - 2015 Branko Wilhelm
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die;

class plgSystemWowInstallerScript
{
    private $modules = array(
        'mod_wow_armory_guild_news' => 'Wow Armory Guild News',
        'mod_wow_guild_members' => 'WoW Guild Members',
        'mod_wow_guild_rank' => 'WoW Guild Rank',
        'mod_wow_guild_tabard' => 'WoW Guild Tabard',
        'mod_wow_latest_guild_achievements' => 'WoW latest Guild Achievements',
        'mod_wow_top_weekly_contributors' => 'WoW Top Weekly Contributors',
        'mod_wow_raid_progress_classic' => 'WoW Raid Progress - Classic',
        'mod_wow_raid_progress_bc' => 'WoW Raid Progress - BC',
        'mod_wow_raid_progress_wotlk' => 'WoW Raid Progress - WotLk',
        'mod_wow_raid_progress_cata' => 'WoW Raid Progress - Cata',
        'mod_wow_raid_progress_mop' => 'WoW Raid Progress - MoP',
        'mod_wow_raid_progress_wod' => 'WoW Raid Progress - WoD'
    );

    private $jversion = '2.5.28';

    private $phpversion = '5.3.7';

    public function preflight()
    {
        if (!version_compare(JVERSION, $this->jversion, '>=')) {
            $link = JHtml::_('link', 'index.php?option=com_joomlaupdate', 'Joomla! ' . $this->jversion);
            JFactory::getApplication()->enqueueMessage(sprintf('You need %s or newer to install this extension', $link), 'error');

            return false;
        }

        if (!version_compare(PHP_VERSION, $this->phpversion, '>=')) {
            JFactory::getApplication()->enqueueMessage(sprintf('You need PHP %s or newer to install this extension', $this->phpversion), 'error');

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

    public function postflight()
    {
        $plugin = JPluginHelper::getPlugin('system', 'wow');
        
        return true; // TODO

        // check if plugin not configured
        if (
            !$plugin ||
            !($plugin->params = new JRegistry($plugin->params)) ||
            (
                !$plugin->params->get('guild') ||
                !$plugin->params->get('realm') ||
                !$plugin->params->get('region') ||
                !$plugin->params->get('locale')
            )
            && ($module = $this->loadModuleParams()) != null
        ) {
            $plugin->params->set('guild', $module->get('guild'));
            $plugin->params->set('realm', $module->get('realm'));
            $plugin->params->set('region', $module->get('region'));
            $plugin->params->set('locale', $module->get('lang'));
        }

        $this->updatePlugin($plugin->params);

        return true;
    }

    private function loadModuleParams()
    {
        $db = JFactory::getDbo();

        $query = $db->getQuery(true)
            ->select('m.params')
            ->from('#__modules AS m')
            ->where('m.module IN(' . implode(',', array_map(array($db, 'quote'), array_keys($this->modules))) . ')');

        $db->setQuery($query);

        $modules = $db->loadColumn();

        if (empty($modules)) {
            return null;
        }

        foreach ($modules as $module) {
            $module = new JRegistry($module);

            // check if module configured
            if ($module->get('guild') && $module->get('realm') && $module->get('region') && $module->get('lang')) {
                return $module;
            }
        }

        return null;
    }

    private function updatePlugin(JRegistry $params)
    {
        $db = JFactory::getDbo();

        $query = $db->getQuery(true)
            ->update('#__extensions AS e')
            ->set('e.enabled =' . $db->quote(1))
            ->set('e.access =' . $db->quote(1))
            ->set('e.params =' . $db->quote($params->toString()))
            ->where('e.type = ' . $db->quote('plugin'))
            ->where('e.folder = ' . $db->quote('system'))
            ->where('e.element = ' . $db->quote('wow'));

        $db->setQuery($query)->execute();
    }
}