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
    protected $theme = null;

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
        if (is_array($file)) {
            $currentFormat = $this->format ? $this->format : 'default';
            $file = isset($file[$currentFormat]) ? $file[$currentFormat] : (isset($file['all']) ? $file['all'] : null);
        }
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
     * @param mixed $format the format to use (eg. "json", "xml") or null to use the default format (usually default)
     */
    public function setFormat($format = null)
    {
        if ($format == 'default' || !is_string($format)) {
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
     * @param mixed $theme the theme to useor null to use the default theme
     */
    public function setTheme($theme = null)
    {
        if ($theme == 'default' || !is_string($theme)) {
            $theme = null;
        }
        $this->theme = $theme;
    }

    /**
     *
     * @return mixed returns the name of the current theme or null if the default theme is used
     */
    public function getTheme()
    {
        return $this->theme === null ? $this->registry->settings->get('config/defaultTheme', false) : $this->theme;
    }

    /**
     *
     * @param MiniMVC_View $content the returned view class of the main controller
     * @return MiniMVC_View the prepared view class of the current layout file and everything included
     */
    public function prepare($content)
    {
        if ($this->layout === null) {
            $this->layout = $this->registry->settings->get('config/defaultLayout', 'default');
        }

        if ($this->layout === false) {
            return $content; //$this->getSlot('main');
        }

        $viewName = $this->registry->settings->get('config/classes/view', 'MiniMVC_View');
        $view = new $viewName('_default');

        $view->layout = $this;

        $this->slots['main'] = array($content);
        //$this->addToSlot('main', $content);

        return $view->prepare($this->layout);
    }

    /**
     *
     * @param string $slot the name of the slot
     * @param MiniMVC_Views $content the content which will be added to the slot
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
     * @param array $data he date to pass to the slot widgets
     * @param string $glue if returned as string, $glue will be added between the contents
     * @return mixed the contents of the slot as string or array (as set in the $array parameter)
     */
    public function getSlot($slot, $data = array(), $glue = '')
    {
        if (!isset($this->slots[$slot])) {
            $this->slots[$slot] = array();
            $this->prepareSlot($slot, $data);
        }
        if ($glue === true) {
            return $this->slots[$slot];
        }
        $return = array();
        foreach ($this->slots[$slot] as $currentSlot) {
            $return[] = $currentSlot->parse();
        }
        return implode($glue, $return);
    }

    protected function prepareSlot($slot, $data)
    {
        if (is_string($slot)) {
            $slot = array($slot);
        }

        $cache = $this->registry->cache->get('cachedSlots');
    
        foreach ($slot as $currentSlot) {
            if (isset($cache[$currentSlot])) {
                $slotWidgets = $cache[$currentSlot];
            } else {
                $slotWidgets = $this->getSlotWidgets($currentSlot);
            }

            $route = $this->registry->settings->get('currentRoute');
            $format = $this->getFormat();
            $layout = $this->getLayout();
            $theme = $this->getTheme();
            
            foreach ($slotWidgets as $currentWidget => $widgetData) {

                if (!empty($widgetData['show'])) {
                    if (is_string($widgetData['show']) && $widgetData['show'] != $route) {
                        continue;
                    } elseif(is_array($widgetData['show']) && !in_array($route, $widgetData['show'])) {
                        continue;
                    }
                }
                if (!empty($widgetData['hide'])) {
                    if (is_string($widgetData['hide']) && $widgetData['hide'] == $route) {
                        continue;
                    } elseif(is_array($widgetData['hide']) && in_array($route, $widgetData['hide'])) {
                        continue;
                    }
                }
                if (empty($widgetData['format']) || $widgetData['format'] == 'default') {
                    $widgetData['format'] = null;
                }
                if (!is_array($widgetData['format']) && $widgetData['format'] != 'all' && ($widgetData['format'] != $format)) {
                    continue;
                } elseif(is_array($widgetData['format']) && !in_array($format ? $format : 'default', $widgetData['format'])) {
                    continue;
                }

                if ($widgetData['theme']) {
                    if (!is_array($widgetData['theme']) && $widgetData['theme'] != 'all' && ($widgetData['theme'] != $theme)) {
                        continue;
                    } elseif(is_array($widgetData['theme']) && !in_array($theme ? $theme : 'default', $widgetData['theme'])) {
                        continue;
                    }
                }

                if ($widgetData['layout']) {
                    if (is_string($widgetData['layout']) && $widgetData['layout'] != 'all' && $widgetData['layout'] != $layout) {
                        continue;
                    } elseif(is_array($widgetData['layout']) && !in_array($layout, $widgetData['layout'])) {
                        continue;
                    }
                }

                try {
                    $parameter = array('slot' => $data);
                    $this->addToSlot($currentSlot, $this->registry->dispatcher->callWidget($currentWidget, $parameter));
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

            if (!empty($widgetData['position']) && is_array($widgetData['position'])) {
                $widgetData['position'] = isset($widgetData['position'][$slot]) ? (int) $widgetData['position'][$slot] : 0;
            }
            $slotWidgets[$widgetName] = array(
                'show' => isset($widgetData['show']) ? $widgetData['show'] : null,
                'hide' => isset($widgetData['hide']) ? $widgetData['hide'] : null,
                'format' => isset($widgetData['format']) ? $widgetData['format'] : null,
                'layout' => isset($widgetData['layout']) ? $widgetData['layout'] : null,
                'theme' => isset($widgetData['theme']) ? $widgetData['theme'] : null,
                'position' => isset($widgetData['position']) ? (int) $widgetData['position'] : 0,
            );
        }

        uasort($slotWidgets, array($this, 'sortSlotWidgets'));

        $this->registry->cache->set('cachedSlots/'.$slot, $slotWidgets);
        return $slotWidgets;
    }

    public function sortSlotWidgets($a, $b)
    {
        if ($a['position'] == $b['position']) {
            return 0;
        }
        return ($a['position'] < $b['position']) ? -1 : 1;
    }
}

