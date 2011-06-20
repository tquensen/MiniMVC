<?php

class Helper_Navi extends MiniMVC_Helper
{

    protected $navis = array();
    
    public function addBreadcrumb($title, $url = null, $active = null)
    {
        if (empty($url)) { 
            $active = ($active !== false) ? true : false;
        }
        if (empty($this->navis['breadcrumb'])) {
            $this->get('breadcrumb');
        }
        $this->navis['breadcrumb'][] = array('title' => $title, 'url' => $url, 'active' => (bool) $active);
    }

    public function getHtml($navi, $module = null, $partial = 'navi')
    {
        $cache = $this->registry->helper->cache->get(array('name' => 'navi_'.$navi, 'role' => $this->registry->guard->getRole(), 'module' => $module, 'partial' => $partial), array('navi'), true);
        if ($return = $cache->load()) {
            return $return;
        }
        $navi = $this->get($navi);
        $return = $this->registry->helper->partial->get($partial, array('navi' => $navi), $module ? $module : $this->module);
        $cache->save($return);
        return $return;
    }

    public function get($navi)
    {
        if (!isset($this->navis[$navi])) {
            $naviData = $this->buildNavi($this->registry->settings->get('view/navi/' . $navi, array()));
            $this->navis[$navi] = $naviData[0];
        }
        return $this->navis[$navi];
    }

    protected function buildNavi($navi)
    {
        $return = array();
        $active = false;
        foreach ($navi as $entry) {
            $current = array();
            $current['active'] = false;

            if (!isset($entry['title'])) {
                $entry['title'] = false;
            }

            if (isset($entry['rights']) && $entry['rights'] && !($this->registry->guard->userHasRight($entry['rights']))) {
                continue;
            }
            if (isset($entry['data'])) {
                $current['data'] = $entry['data'];
            }
            if (is_string($entry['title'])) {
                $current['title'] = $entry['title'];
            } elseif (is_array($entry['title']) && isset($entry['title'][0])) {
                $t = $this->registry->helper->i18n->get(isset($entry['title'][1]) ? $entry['title'][1] : '_default');
                $current['title'] = $t->{$entry['title'][0]};
            } else {
                $current['title'] = '';
            }
            if (isset($entry['url'])) {
                $current['url'] = $entry['url'];
            } elseif (isset($entry['route'])) {
                $current['url'] = $this->registry->helper->url->get($entry['route'], isset($entry['parameter']) ? $entry['parameter'] : array(), isset($entry['app']) ? $entry['app'] : null);
                if (((!$entry['route'] || $entry['route'] == $this->registry->settings->get('config/defaultRoute')) && $this->registry->settings->get('currentRoute') == $this->registry->settings->get('config/defaultRoute')) || $entry['route'] == $this->registry->settings->get('currentRoute')) {
                    $current['active'] = true;
                    if (!empty($entry['parameter'])) {
                        $currentRouteParameter = $this->registry->settings->get('currentRouteParameter', array());
                        foreach ((array) $entry['parameter'] as $paramKey => $paramValue) {
                            if (!isset($currentRouteParameter[$paramKey]) || $currentRouteParameter[$paramKey] != $paramValue) {
                                $current['active'] = false;
                                break;
                            }
                        }
                    }
                    //$current['active'] = (!isset($entry['parameter']) || $entry['parameter'] == $this->registry->settings->get('currentRouteParameter')) ? true : false;
                }
            } else {
                $current['url'] = false;
            }
            if (isset($entry['submenu']) && is_array($entry['submenu'])) {
                list($submenu, $submenuActive) = $this->buildNavi($entry['submenu']);
                $current['submenu'] = !empty($submenu) ? $submenu : false;
                if ($submenuActive) {
                    $current['active'] = $submenuActive;
                }
            }

            if ($current['active']) {
                $active = true;
            }

            if (!$entry['title']) {
                continue;
            }
            $return[] = $current;
        }
        return array($return, $active);
    }

}