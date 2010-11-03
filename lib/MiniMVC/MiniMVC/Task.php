<?php

/**
 * MiniMVC_Task is used to dispatch and call CLI tasks
 */
class MiniMVC_Task
{
    protected $registry = null;

    public function __construct()
    {
        $this->registry = MiniMVC_Registry::getInstance();
    }

    /**
     *
     * @param array $rawParams the raw CLI parameters (the argv array)
     * @return MiniMVC_View the prepared view class of the called task
     */
    public function dispatch($rawParams)
    {
        $this->registry->template->setLayout(false);
        $this->registry->template->setFormat('plain');

        array_shift($rawParams); // remove the filename (cli.php)
        $taskName = array_shift($rawParams);
        $params = $this->parseArgs($rawParams);

        if (isset($params['app']) && $this->registry->settings->get('apps/' . $params['app'])) {
            $this->registry->settings->set('runtime/currentApp', $params['app']);
        }

        if (isset($params['env'])) {
            $this->registry->settings->set('runtime/currentEnvironment', $params['env']);
        }

        $this->registry->settings->set('runtime/currentLanguage', '');

        if (!$taskName) {
            return 'error: no task specified!';
        }

        if (!$task = $this->registry->settings->get('tasks/' . $taskName)) {
            return 'error: task ' . $taskName . ' not found';
        }

        try {
            $this->registry->db->init();
            return $this->registry->dispatcher->callTask($taskName, $params)->parse();
        } catch (Exception $e) {
            if (!empty($params['debug'])) {
                return 'error: ' . $e;
            }
            return 'error: ' . $e->getMessage();
        }
    }

    /**
     *
     * @param array $argv the raw CLI parameters to parse
     * @return array returns an array of parameters
     */
    public function parseArgs($argv)
    {
        $out = array();
        foreach ($argv as $arg) {
            // --foo --bar=baz
            if (substr($arg, 0, 2) == '--') {
                $eqPos = strpos($arg, '=');
                // --foo
                if ($eqPos === false) {
                    $key = substr($arg, 2);
                    $value = isset($out[$key]) ? $out[$key] : true;
                    $out[$key] = $value;
                }
                // --bar=baz
                else {
                    $key = substr($arg, 2, $eqPos - 2);
                    $value = substr($arg, $eqPos + 1);
                    $out[$key] = $value;
                }
            }
            // -k=value -abc
            else if (substr($arg, 0, 1) == '-') {
                // -k=value
                if (substr($arg, 2, 1) == '=') {
                    $key = substr($arg, 1, 1);
                    $value = substr($arg, 3);
                    $out[$key] = $value;
                }
                // -abc
                else {
                    $chars = str_split(substr($arg, 1));
                    foreach ($chars as $char) {
                        $key = $char;
                        $value = isset($out[$key]) ? $out[$key] : true;
                        $out[$key] = $value;
                    }
                }
            }
            // plain-arg
            else {
                $value = $arg;
                $out[] = $value;
            }
        }
        return $out;
    }

}
