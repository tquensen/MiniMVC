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
            $this->cachedEvents = MiniMVC_Registry::getInstance()->settings->events;
        }
        if (!isset($this->cachedEvents[$event]) || !is_array($this->cachedEvents[$event])) {
            return array();
        }
        $listeners = array();
        foreach ($this->cachedEvents[$event] as $listener) {
            $callable = false;
            if (isset($listener['function'])) {
                $callable = $listener['function'];
            } elseif (isset($listener['class']) && isset($listener['method']) && method_exists($listener['class'], $listener['method'])) {
                $className = $listener['class'];
                if (isset($listener['instance']) && $listener['instance'] == 'always') {
                    $callable = array(new $className(), $listener['method']);
                } elseif (isset($listener['instance']) && $listener['instance'] == 'static') {
                    $callable = array($className, $listener['method']);
                } else {
                    if (!isset($cachedListeners[$className])) {
                        $cachedListeners[$className] = new $className();
                    }
                    $callable = array($cachedListeners[$className], $listener['method']);
                }
            }
            if ($callable) {
                $listeners[] = $callable;
            }
        }

        return $listeners;
    }

}
?>
