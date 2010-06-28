<?php
/**
 * MiniMVC_Layout is the default layout/template class
 */
class MiniMVC_Layout
{
    /**
     *
     * @var MiniMVC_Registry
     */
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

    /**
     *
     * @param mixed $file the filename (without extension) of the layout file to use, true or null to use the default layout, false to use no layout file
     */
    public function setLayout($file)
    {
        $this->layout = ($file === true) ? null : $file;
    }

    /**
     *
     * @return mixed returns the name of the current layout file or null if the default layout is used
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     *
     * @param mixed $format the format to use (eg. "json", "xml") or null to use the default format (usually html)
     */
    public function setFormat($format = null)
    {
        $this->format = $format;
    }

    /**
     *
     * @return mixed returns the name of the current format or null if the default format is used
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     *
     * @param mixed $app the name of the app to use or null for the current app
     * @return string returns the parsed output of the current layout file and everything included
     */
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
        $view->layout = $this;

        $this->prepareSlots();

        return $view->parse($this->layout);
    }

    /**
     *
     * @param string $slot the name of the slot
     * @param string $content the content which will be added to the slot
     */
    public function addToSlot($slot, $content)
    {
        if (!isset($this->slots[$slot])) {
            $this->slots[$slot] = array();
        }
        $this->slots[$slot][] = $content;
    }

    /**
     *
     * @param string $slot the name of the slot
     * @param bool $array if the content should be returned as array or as string
     * @param string $glue if returned as string, $glue will be added between the contents
     * @return mixed the contents of the slot as string or array (as set in the $array parameter)
     */
    public function getSlot($slot, $array = false, $glue = '')
    {
        if (!isset($this->slots[$slot])) {
            $this->slots[$slot] = array();
        }
        return ($array) ? $this->slots[$slot] : implode($glue, $this->slots[$slot]);
    }

}

