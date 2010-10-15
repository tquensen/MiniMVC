<?php

class MiniMVC_Events extends sfEventDispatcher
{
    protected $cachedEvents = null;
    protected $cachedListeners = array();

    /**
     * Returns true if the given event name has some listeners.
     *
     * @param  string   $name    The event name
     *
     * @return Boolean true if some listeners are connected, false otherwise
     */
    public function hasListeners($name)
    {
        if (!isset($this->listeners[$name])) {
            $this->listeners[$name] = $this->loadConfigListeners($name);
        }

        return (boolean)count($this->listeners[$name]);
    }

    /**
     * Returns all listeners associated with a given event name.
     *
     * @param  string   $name    The event name
     *
     * @return array  An array of listeners
     */
    public function getListeners($name)
    {
        if (!isset($this->listeners[$name])) {
            $this->listeners[$name] = $this->loadConfigListeners($name);
        }

        return $this->listeners[$name];
    }

    /**
     * Loads and connects listeners from MiniMVC config files
     */
    protected function loadConfigListeners($event)
    {
        if ($this->cachedEvents === null) {
            $this->cachedEvents = MiniMVC_Registry::getInstance()->settings->get('events');
        }
        if (!isset($this->cachedEvents[$event]) || !is_array($this->cachedEvents[$event])) {
            return array();
        }
        $listeners = array();
        foreach ($this->cachedEvents[$event] as $listener) {
            $callable = false;
            if (!is_array($listener)) {
                $listeners[] = $listener;
            } elseif (count($listener) > 1) {
                $className = array_shift($listener);
                $methodName = array_shift($listener);
                $instance = array_shift($listener);
                if ($instance == 'always') {
                    $listeners[] = array(new $className(), $methodName);
                } elseif ($instance == 'static') {
                    $listeners[] = array($className, $methodName);
                } else {
                    if (!isset($this->cachedListeners[$className])) {
                        $this->cachedListeners[$className] = new $className();
                    }
                    $listeners[] = array($this->cachedListeners[$className], $methodName);
                }
            }
        }

        return $listeners;
    }

}
?>
