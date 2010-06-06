<?php
class MiniMVC_Task {
    public function dispatch()
    {
        $params = $this->getCliParams();
        var_dump($params);
    }

    public function getCliParams()
    {
        $params = array();
        foreach ($argv as $arg) {
            if (substr($arg, 0, 2) != '--') {
                continue;
            }
            $arg = explode('=',  substr($arg, 2));
            if (!isset($arg[1])) {
                $arg[1] = 'true';
            }
            if (!trim($arg[0])) {
                continue;
            }
            $params[trim($arg[0])] = trim($arg[1]);
        }
    }
}
