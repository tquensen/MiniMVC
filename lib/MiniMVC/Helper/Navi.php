<?php

class Helper_Navi extends MiniMVC_Helper
{

    public function getHtml($navi)
    {
        $navi = $this->get($navi);
        return $this->registry->helper->partial->get('navi', array('navi' => $navi), $this->module);
    }

    public function get($navi)
    {
        $navi = $this->registry->settings->get('view/navi/' . $navi, array());
        $naviData = $this->buildNavi($navi);
        return $naviData[0];
    }

    protected function buildNavi($navi)
    {
        $return = array();
        $active = false;
        foreach ($navi as $entry) {
            $current = array();
            $current['active'] = false;
            if (!isset($entry['title'])) {
                continue;
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
                $t = $this->registry->helper->i18n->get(isset($entry[1]) ? $entry[1] : '_default');
                $current['title'] = $t->{$entry['title'][0]};
            } else {
                $current['title'] = '';
            }
            if (isset($entry['url'])) {
                $current['url'] = $entry['url'];
            } elseif (isset($entry['route'])) {
                $current['url'] = $this->registry->helper->url->get($entry['route'], isset($entry['parameter']) ? $entry['parameter'] : array(), isset($entry['app']) ? $entry['app'] : null);
                if ($entry['route'] == $this->registry->settings->get('runtime/currentRoute')) {
                    $current['active'] = (!isset($entry['parameter']) || $entry['parameter'] == $this->registry->settings->get('runtime/currentRouteParameter')) ? true : false;
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
            $return[] = $current;
        }
        return array($return, $active);
    }

}