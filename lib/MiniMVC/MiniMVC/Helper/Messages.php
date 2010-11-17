<?php

class Helper_Messages extends MiniMVC_Helper
{
    protected $messages = array();

    public function construct()
    {
        $this->messages = !empty($_SESSION['helper_messages']) ? $_SESSION['helper_messages'] : array();
    }
    /**
     *
     * @param mixed $message the message to store
     * @param string $type the message type, (notice, warning, error, ...)
     * @param bool $persistent store the message in the session (default true)
     */
    public function add($message, $type = 'notice', $persistent = true)
    {
        $this->messages[$type][] = $message;
        if ($presistent) {
            $_SESSION['helper_messages'][$type][] = $message;
        }
    }

    /**
     *
     * @param string|bool $type the messagetype to check or false to check any type
     * @return bool
     */
    public function messagesAvailable($type = 'notice')
    {
        if (!$type) {
            return !empty($this->messages);
        }
        return !empty($this->messages[$type]);
    }

    /**
     *
     * @param string|bool $type the messagetype to return or false to return any types (default false)
     * @param bool $remove whether to remove the messages after returning (default true)
     * @return array an array of all messages of the requested type or an array of all types
     */
    public function get($type = false, $remove = true)
    {
        $messages = array();
        if (!$type) {
            $messages = !empty($this->messages) ? $this->messages : array();
            if ($remove) {
                $this->messages = array();
                $_SESSION['helper_messages'] = array();
            }
        } else {
            $messages = !empty($this->messages[$type]) ? $this->messages[$type] : array();
            if ($remove) {
                unset($this->messages[$type]);
                unset($_SESSION['helper_messages'][$type]);
            }
        }
        return $messages;
    }

    /**
     *
     * @param string|bool $type the messagetype to return or false to return any types (default false)
     * @param bool $remove whether to remove the messages after returning (default true)
     * @return string the html output
     */
    public function getHtml($type = false, $remove = true, $module = null, $partial = 'messages')
    {
        $messages = $this->get($type, $remove);
        return $this->registry->helper->partial->get($partial, $type ? array($type => $messages) : $messages, $module ? $module : $this->module);
    }

}