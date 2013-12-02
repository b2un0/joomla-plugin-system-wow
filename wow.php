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
    private $modules = array(
        'mod_wow_armory_guild_news',
        'mod_wow_guild_members',
        'mod_wow_guild_rank',
        'mod_wow_guild_tabard',
        'mod_wow_latest_guild_achievements',
        'mod_wow_top_weekly_contributors',
        'mod_wow_raid_progress_classic',
        'mod_wow_raid_progress_bc',
        'mod_wow_raid_progress_wotlk',
        'mod_wow_raid_progress_cata',
        'mod_wow_raid_progress_mop',
        'mod_wow_raid_progress_wod'
    );
    private $plugins = array(
        'wow_avatar'
    );

    public function onContentPrepareForm(JForm $form, $data)
    {
        if (($form->getName() == 'com_modules.module' && in_array($data->module, $this->modules)) || ($form->getName() == 'com_plugins.plugin' && in_array($data->element, $this->plugins))) {
            $form->setFieldAttribute('guild', 'readonly', 'true', 'params');
            $form->setFieldAttribute('realm', 'readonly', 'true', 'params');
            $form->setFieldAttribute('region', 'readonly', 'true', 'params');
            $form->setFieldAttribute('lang', 'readonly', 'true', 'params');
        }
    }

    public function onContentPrepareData($context, $data)
    {
        if (($context == 'com_modules.module' && in_array($data->module, $this->modules)) || ($context == 'com_plugins.plugin' && in_array($data->element, $this->plugins))) {
            $data->params['guild'] = $this->params->get('guild');
            $data->params['realm'] = $this->params->get('realm');
            $data->params['region'] = $this->params->get('region');
            $data->params['lang'] = $this->params->get('lang');
        }
    }
}