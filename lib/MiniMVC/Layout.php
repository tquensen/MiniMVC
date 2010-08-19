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
        if ($format == 'html') {
            $format = null;
        }
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
    public function parse($content, $app = null)
    {
        $app = ($app) ? $app : $this->registry->settings->get('runtime/currentApp');
        if ($this->layout === false) {
            return $this->getSlot('main');
        }
        if ($this->layout === null) {
            $this->layout = ($layout = $this->registry->settings->get('config/defaultLayout', 'default')) ? $layout : 'default';
        }

        if ($viewName = $this->registry->settings->get('config/classes/view')) {
            $view = new $viewName('_default');
        } else {
            $view = new MiniMVC_View('_default');
        }
        $view->layout = $this;

        $this->addToSlot('main', $content);

        return $view->parse($this->layout);
    }

    /**
     *
     * @param string $slot the name of the slot
     * @param string $content the content which will be added to the slot
     */
    protected function addToSlot($slot, $content)
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
    public function getSlot($slot, $data = array(), $glue = '')
    {
        if (!isset($this->slots[$slot])) {
            $this->slots[$slot] = array();
            $this->prepareSlot($slot, $data);
        }
        return ($glue === true) ? $this->slots[$slot] : implode($glue, $this->slots[$slot]);
    }

    protected function prepareSlot($slot, $data)
    {
        if (is_string($slot)) {
            $slot = array($slot);
        }
    
        foreach ($slot as $currentSlot) {
            $slotWidgets = $this->registry->settings->get('widgets/cachedSlots/'.$currentSlot);
            if ($slotWidgets === null) {
                $slotWidgets = $this->getSlotWidgets($currentSlot);
            }
            foreach ($slotWidgets as $currentWidget) {
                try {
                    $parameter = array('slot' => $data);
                    $content = $this->registry->dispatcher->callWidget($currentWidget, $parameter);
                    if ($content) {
                        $this->addToSlot($currentSlot, $content);
                    }
                } catch (Exception $e) {

                }
            }
        }
    }

    protected function getSlotWidgets($slot)
    {
        $widgets = $this->registry->settings->get('widgets', array());
        $slotWidgets = array();
        foreach ($widgets as $widgetName => $widgetData) {
            if (!isset($widgetData['slot']) || !$widgetData['slot']) {
                continue;
            }
            if (is_string($widgetData['slot']) && $widgetData['slot'] != $slot) {
                continue;
            }
            if (is_array($widgetData['slot']) && !in_array($slot, $widgetData['slot'])) {
                continue;
            }
            $slotWidgets[] = $widgetName;
        }
        var_dump($slot, $slotWidgets);
        $this->registry->settings->set('widgets/cachedSlots/'.$slot, $slotWidgets);
        return $slotWidgets;
    }
}

