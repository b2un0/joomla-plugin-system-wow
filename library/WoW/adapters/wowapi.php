<?php

/**
 * @author     Branko Wilhelm <branko.wilhelm@gmail.com>
 * @link       http://www.z-index.net
 * @copyright  (c) 2013 - 2016 Branko Wilhelm
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die;

class WoWAdapterWoWAPI extends WoWAdapterAbstract
{
    public function getData($target, $persistent = false)
    {
        $uri = new JUri;

        switch ($target) {
            case 'guild':
                $uri->setVar('namespace', 'profile-' . $this->params->get('region'));
                $uri->setPath('/data/wow/guild/' . ($this->params->get('realm')) . '/' . ($this->params->get('guild')));
                break;

            case 'members':
                $uri->setVar('namespace', 'profile-' . $this->params->get('region'));
                $uri->setPath('/data/wow/guild/' . ($this->params->get('realm')) . '/' . ($this->params->get('guild')) . '/roster');
                break;

            case 'achievements':
                $uri->setVar('namespace', 'dynamic-' . $this->params->get('region'));
                $uri->setPath('/data/wow/guild/' . ($this->params->get('realm')) . '/' . ($this->params->get('guild')) . '/achievements');
                break;

            case 'races':
                $uri->setPath('/data/wow/playable-race/index');
                break;

            case 'classes':
                $uri->setPath('/data/wow/playable-class/index');
                break;

            case 'realms':
                $uri->setPath('/data/wow/realm/index');
                break;

            default:
                return null;
        }

        $result = $this->getRemote($uri, $persistent);

        domix($result, 1);
    }

    protected function getAchievement($achievement, $persistent = true)
    {
        $uri = new JUri;
        $uri->setVar('namespace', 'static-' . $this->params->get('region'));
        $uri->setPath('/data/wow/achievement/' . $achievement);

        return $this->getRemote($uri, $persistent);
    }

    protected function getMember($member, $realm)
    {
        $uri = new JUri;
        $uri->setVar('namespace', 'profile-' . $this->params->get('region'));
        $uri->setPath('/data/wow/character/' . $realm . '/' . $member . '/achievements');

        return $this->getRemote($uri);
    }

    private function getToken()
    {
        $uri = new Joomla\Uri\Uri();

        $uri->setScheme($this->params->get('scheme', 'https'));
        $uri->setHost($this->params->get('region') . '.battle.net');
        $uri->setPath('/oauth/token');
        $uri->setUser($this->params->get('client_id'));
        $uri->setPass($this->params->get('client_secret'));

        $cache = JFactory::getCache('wow', 'output');
        $cache->setCaching(1);
        $cache->setLifeTime(82800); // access token is 24hour valid

        $key = md5($uri->toString());

        if (!$token = $cache->get($key)) {
            $http = JHttpFactory::getHttp();
            $http->setOption('userAgent', 'Joomla/' . JVERSION . '; WoW Library/z-index.net; php/' . PHP_VERSION);
            $result = $http->post($uri, 'grant_type=client_credentials', [], $this->params->get('socket_timeout', 10));
            $result->body = json_decode($result->body);

            $cache->store($result->body->access_token, $key);

            $token = $result->body->access_token;
        }

        return $token;
    }

    /**
     * @param JUri $uri
     * @param bool $persistent
     * @param array $headers
     *
     * @return stdClass json decoded payload
     */
    protected function getRemote($uri, $persistent = false, $headers = [])
    {
        $uri->setScheme($this->params->get('scheme', 'https'));

        if ($this->params->get('region') === 'cn') {
            $uri->setHost('gateway.battlenet.com.cn');
        } else {
            $uri->setHost($this->params->get('region') . '.api.blizzard.com');
        }

        $uri->setVar('locale', $this->params->get('locale', 'en_US'));

        $uri->setVar('access_token', $this->getToken());

        $this->url = $uri->toString();

        $result = parent::getRemote($this->url, $persistent);

        $result->body = json_decode($result->body);

        if ($result->code != 200) {
            // hide api key from normal users
            if (!JFactory::getUser()->get('isRoot')) {
                $uri->delVar('access_token');
                $this->url = $uri->toString();
            }

            $msg = JText::sprintf('Server Error: %s url: %s', $result->body->reason, JHtml::_('link', $this->url, $result->code, ['target' => '_blank']));
            throw new RuntimeException($msg);
        }

        return $result;
    }
}
