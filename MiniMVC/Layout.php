<?php

class MiniMVC_Layout
{
    protected $registry = null;
    protected $slots = array();
    protected $layout = null;
    protected $format = null;

    public function __construct()
    {
        $this->registry = MiniMVC_Registry::getInstance();
    }

    protected function prepareSlots()
    {
        $key = $this->layout . ($this->format != null) ? '.' . $this->format : '';
        $slots = $this->registry->settings->slots;

        if (!isset($slots[$key]))
        {
            return;
        }
        
        foreach ($slots as $currentSlot => $slotData) {
            if (!isset($this->slots[$currentSlot])) {
                $this->slots[$currentSlot] = array();
            }
            $this->prepareSlot($currentSlot, $slotData);
        }
    }

    protected function prepareSlot($slot, $slotData)
    {
        $dispatcher = $this->registry->dispatcher;
        foreach ($slotData as $currentSlotData) {
            if (!isset($currentSlotData['name']) || ! isset($currentSlotData['type'])) {
                continue;
            }

            try {
                if ($currentSlotData['type'] == 'route') {
                    $content = $dispatcher->callRoute($currentSlotData['name'], (isset($currentSlotData['parameter'])) ? $currentSlotData['parameter'] : array(), false);
                } elseif ($currentSlotData['type'] == 'widget') {
                    $content = $dispatcher->callWidget($currentSlotData['name'], (isset($currentSlotData['parameter'])) ? $currentSlotData['parameter'] : array());
                } else {
                    continue;
                }
                $this->addToSlot($slot, $content);
            } catch (Exception $e) {
                
            }
        }
    }

    public function setLayout($file)
    {
        $this->layout = ($file === true) ? null : $file;
    }

    public function getLayout()
    {
        return $this->layout;
    }

    public function setFormat($format = null)
    {
        $this->format = $format;
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function parse($app = null)
    {
        $app = ($app) ? $app : $this->registry->settings->currentApp;
        if ($this->layout === false) {
            return $this->getSlot('main');
        }
        if ($this->layout === null) {
            $this->layout = (isset($this->registry->settings->config['defaultLayout']) && $this->registry->settings->config['defaultLayout']) ? $this->registry->settings->config['defaultLayout'] : 'default';
        }

        if (isset($this->registry->settings->config['classes']['view']) && $this->registry->settings->config['classes']['view']) {
            $viewName = $this->registry->settings->config['classes']['view'];
            $view = new $viewName('_default');
        } else {
            $view = new MiniMVC_View('_default');
        }

        $this->prepareSlots();

        return $view->parse($this->layout);
    }

    public function addToSlot($slot, $content)
    {
        if (!isset($this->slots[$slot])) {
            $this->slots[$slot] = array();
        }
        $this->slots[$slot][] = $content;
    }

    public function getSlot($slot, $array = false, $glue = '')
    {
        if (!isset($this->slots[$slot])) {
            $this->slots[$slot] = array();
        }
        return ($array) ? $this->slots[$slot] : implode($glue, $this->slots[$slot]);
    }

}

