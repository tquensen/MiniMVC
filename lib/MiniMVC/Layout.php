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

    protected function prepareSlot($slot)
    {
        if (is_string($slot)) {
            $slot = array($slot);
        }
        $dispatcher = $this->registry->dispatcher;
        $slots = $this->registry->settings->get('slots');
        $route = $this->registry->settings->get('runtime/currentRoute');

        foreach ($slot as $currentSlot) {
            if (!isset($slots[$currentSlot])) {
                continue;
            }
            foreach ($slots[$currentSlot] as $currentSlotData) {
                if (is_string($currentSlotData)) {
                    $currentSlotData = array('name' => $currentSlotData, 'type' => 'widget');
                }
                if (isset($currentSlotData['active']) && !$currentSlotData['active']) {
                    continue;
                }
                if (!isset($currentSlotData['type'])) {
                    $currentSlotData['type'] = 'widget';
                }
                if (!isset($currentSlotData['name'])) {
                    continue;
                }
                if (isset($currentSlotData['show']) && $currentSlotData['show']) {
                    if (is_string($currentSlotData['show']) && $currentSlotData['show'] != $route) {
                        continue;
                    } elseif(is_array($currentSlotData['show']) && !in_array($route, $currentSlotData['show'])) {
                        continue;
                    }
                }
                if (isset($currentSlotData['hide']) && $currentSlotData['hide']) {
                    if (is_string($currentSlotData['hide']) && $currentSlotData['hide'] == $route) {
                        continue;
                    } elseif(is_array($currentSlotData['hide']) && in_array($route, $currentSlotData['hide'])) {
                        continue;
                    }
                }
                if (!isset($currentSlotData['format']) || $currentSlotData['format'] == 'html') {
                    $currentSlotData['format'] = null;
                }
                if (!is_array($currentSlotData['format']) && $currentSlotData['format'] != 'all' && ($currentSlotData['format'] != $this->format)) {
                    continue;
                } elseif(is_array($currentSlotData['format']) && !in_array($this->format ? $this->format : 'html', $currentSlotData['format'])) {
                    continue;
                }
                if (isset($currentSlotData['layout']) && $currentSlotData['layout']) {
                    if (is_string($currentSlotData['layout']) && $currentSlotData['layout'] != 'all' && $currentSlotData['layout'] != $this->layout) {
                        continue;
                    } elseif(is_array($currentSlotData['layout']) && !in_array($this->layout, $currentSlotData['layout'])) {
                        continue;
                    }
                }

                try {
                    if ($currentSlotData['type'] == 'route') {
                        $content = $dispatcher->callRoute($currentSlotData['name'], (isset($currentSlotData['parameter'])) ? $currentSlotData['parameter'] : array(), false);
                    } elseif ($currentSlotData['type'] == 'widget') {
                        $content = $dispatcher->callWidget($currentSlotData['name'], (isset($currentSlotData['parameter'])) ? $currentSlotData['parameter'] : array());
                    } else {
                        continue;
                    }
                    $this->addToSlot($currentSlot, $content);
                } catch (Exception $e) {

                }
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
    public function getSlot($slot, $array = false, $glue = '')
    {
        if (!isset($this->slots[$slot])) {
            $this->slots[$slot] = array();
            $this->prepareSlot($slot);
        }
        return ($array) ? $this->slots[$slot] : implode($glue, $this->slots[$slot]);
    }

}

