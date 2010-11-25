<?php

class Helper_Partial extends MiniMVC_Helper
{

    public function get($_partial, $_data = array(), $_module = null, $_app = null)
    {
        if ($_module === null) {
            $_module = $this->module;
        }
        $_app = ($_app) ? $_app : $this->registry->settings->get('currentApp');

        $_format = $this->registry->template->getFormat();

        try {
            ob_start();
            $cache = $this->registry->cache->get('partialCached');
            if (isset($cache[$_app.'_'.$_module.'_'.$_format.'_'.str_replace('/', '__', $_partial)])) {
                $_file = $cache[$_app.'_'.$_module.'_'.$_format.'_'.str_replace('/', '__', $_partial)];
            } else {
                if ($_format && $_module !== null && file_exists(APPPATH . $_app . '/partial/' . $_module . '/' . $_partial . '.' . $_format . '.php')) {
                    $_file = APPPATH . $_app . '/partial/' . $_module . '/' . $_partial . '.' . $_format . '.php';
                } elseif ($_format && $_module !== null && file_exists(MODULEPATH . $_module . '/partial/' . $_partial . '.' . $_format . '.php')) {
                    $_file = MODULEPATH . $_module . '/partial/' . $_partial . '.' . $_format . '.php';
                } elseif ($_format && file_exists(APPPATH . $_app . '/partial/' . $_partial . '.' . $_format . '.php')) {
                    $_file = APPPATH . $_app . '/partial/' . $_partial . '.' . $_format . '.php';
                } elseif ($_format && file_exists(DATAPATH . 'partial/' . $_partial . '.' . $_format . '.php')) {
                    $_file = DATAPATH . 'partial/' . $_partial . '.' . $_format . '.php';
                } elseif ($_format && file_exists(MINIMVCPATH . 'data/partial/' . $_partial . '.' . $_format . '.php')) {
                    $_file = MINIMVCPATH . 'data/partial/' . $_partial . '.' . $_format . '.php';
                } elseif ($_module !== null && file_exists(APPPATH . $_app . '/partial/' . $_module . '/' . $_partial . '.php')) {
                    $_file = APPPATH . $_app . '/partial/' . $_module . '/' . $_partial . '.php';
                } elseif ($_module !== null && file_exists(MODULEPATH . $_module . '/partial/' . $_partial . '.php')) {
                    $_file = MODULEPATH . $_module . '/partial/' . $_partial . '.php';
                } elseif (file_exists(APPPATH . $_app . '/partial/' . $_partial . '.php')) {
                    $_file = APPPATH . $_app . '/partial/' . $_partial . '.php';
                } elseif (file_exists(DATAPATH . 'partial/' . $_partial . '.php')) {
                    $_file = DATAPATH . 'partial/' . $_partial . '.php';
                } elseif (file_exists(MINIMVCPATH . 'data/partial/' . $_partial . '.php')) {
                    $_file = MINIMVCPATH . 'data/partial/' . $_partial . '.php';
                } else {
                    return ob_get_clean();
                }
                $this->registry->cache->set('partialCached', array($_app.'_'.$_module.'_'.$_format.'_'.str_replace('/', '__', $_partial) => $_file), true);
            }
            extract($_data);
            $h = $this->registry->helper;
            $o = $h->text;
            include $_file;
            return ob_get_clean();
        } catch (Exception $e) {
            ob_end_clean();
            throw $e;
        }
    }

}