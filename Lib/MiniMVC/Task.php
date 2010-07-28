<?php
/**
 * MiniMVC_Task is used to dispatch and call CLI tasks
 */
class MiniMVC_Task {
    protected $registry = null;

    public function __construct()
    {
        $this->registry = MiniMVC_Registry::getInstance();
    }

    /**
     *
     * @param array $rawParams the raw CLI parameters (the argv array)
     * @return string the parsed output of the task
     */
    public function dispatch($rawParams)
    {
        $this->registry->template->setLayout(false);
        $this->registry->template->setFormat('text');

        $params = $this->getCliParams($rawParams);

        if (isset($params['app']) && isset($this->registry->settings->apps[$params['app']])) {
            $this->registry->settings->currentApp = $params['app'];
        }

        $this->registry->settings->currentLanguage = '';

        if (!$params['task']) {
            return 'error: no task specified!';
        }

        if (!isset($this->registry->settings->tasks[$params['task']])) {
            return 'error: task '.$params['task'].' not found';
        }
      
        try {
            $this->registry->db->init();
            return $this->registry->dispatcher->callTask($params['task'], $params);
        } catch (Exception $e) {
            return 'error: '.$e->getmessage();
        }
    }

    /**
     *
     * @param array $rawParams the raw CLI parameters to parse
     * @return array returns an associative array of parameters
     */
    public function getCliParams($rawParams)
    {
        $params = array();
        foreach ($rawParams as $param) {
            if (substr($param, 0, 2) != '--') {
                continue;
            }
            $param = explode('=',  substr($param, 2));
            if (!isset($param[1])) {
                $param[1] = true;
            }
            if (!trim($param[0])) {
                continue;
            }
            $params[trim($param[0])] = trim($param[1]);
        }
        $params['task'] = (isset($rawParams[1])) ? $rawParams[1] : null;
        return $params;
    }
}
