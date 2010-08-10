<?php

class Helper_Partial extends MiniMVC_Helper
{

    public function get($_partial, $_data = array(), $_module = null, $_app = null)
    {
        if ($_module === null) {
            $_module = $this->module;
        }
        $_app = ($_app) ? $_app : $this->registry->settings->get('runtime/currentApp');

        $_file = $_partial . '.php';
        extract($_data);

        ob_start();
        if ($_module !== null && file_exists(APPPATH . $_app . '/Partial/' . $_module . '/' . $_file)) {
            include(APPPATH . $_app . '/Partial/' . $_module . '/' . $_file);
        } elseif ($_module !== null && file_exists(MODULEPATH . $_module . '/Partial/' . $_file)) {
            include(MODULEPATH . $_module . '/Partial/' . $_file);
        } elseif (file_exists(APPPATH . $_app . '/Partial/' . $_file)) {
            include(APPPATH . $_app . '/Partial/' . $_file);
        } elseif (file_exists(BASEPATH . 'Partial/' . $_file)) {
            include(BASEPATH . 'Partial/' . $_file);
        }
        return ob_get_clean();
    }

}