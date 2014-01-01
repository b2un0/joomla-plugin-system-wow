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

    public function onBeforeCompileHead()
    {
        $app = JFactory::getApplication();
        if ($app->isAdmin()) {
            return;
        }

        $doc = JFactory::getDocument();
        $doc->addScript('media/wow/wow.js');

        $js = 'window.wow.base="' . Juri::base(true) . '";';
        $js .= 'window.wow.Itemid=' . $app->input->getInt('Itemid') . ';';

        $doc->addScriptDeclaration($js);
    }
}