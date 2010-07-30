<?php
/**
 * MiniMVC_Dispatcher delegates requests and route/widget calls to the right controllers and actions
 */
class MiniMVC_Dispatcher
{
    /**
     *
     * @var MiniMVC_Registry
     */
    protected $registry = null;

    public function __construct()
    {
        $this->registry = MiniMVC_Registry::getInstance();
    }

    /**
     *
     * @return string returns the parsed output of the current request
     */
    public function dispatch()
    {
        $protocol = (!isset($_SERVER['HTTPS']) || !$_SERVER['HTTPS'] || $_SERVER['HTTPS'] == 'off') ? 'http' : 'https';
		$host = $protocol.'://'.$_SERVER['HTTP_HOST'];
        $url = $host . $_SERVER['REQUEST_URI'];
        
        $currentLanguage = null;
        $currentApp = null;
        $route = null;
        foreach ($this->registry->settings->apps as $app => $appurls) {
            if (isset($appurls['baseurlI18n'])) {
                if (preg_match('#^' . str_replace(':lang:', '(?P<lang>[a-z]{2})', $appurls['baseurlI18n']) . '(?P<route>[^\?]*).*$#', $url, $matches)) {
                    $currentLanguage = $matches['lang'];
                    $currentApp = $app;
                    $route = $matches['route'];
                    break;
                }
            }
            if (isset($appurls['baseurl'])) {
                if (preg_match('#^' . $appurls['baseurl'] . '(?P<route>[^\?]*).*$#', $url, $matches)) {
                    $currentApp = $app;
                    $route = $matches['route'];
                    break;
                }
            }
        }

        if (!$currentApp) {
            if (!isset($this->registry->settings->defaultApp) || !isset($this->registry->settings->apps[$this->registry->settings->defaultApp]['baseurl'])) {
                throw new Exception('No matching App found and no default app configured!');
            }
            header('Location: ' . $this->registry->settings->apps[$this->registry->settings->defaultApp]['baseurl']);
            exit;
        }
        $this->registry->settings->currentApp = $currentApp;

        if ($currentLanguage) {
            if (!in_array($currentLanguage, $this->registry->settings->config['enabledLanguages']) || $currentLanguage == $this->registry->settings->config['defaultLanguage']) {
                if (!isset($this->registry->settings->apps[$currentApp]['baseurl'])) {
                    throw new Exception('No baseurl for App '.$currentApp.' found!');
                }
                header('Location: ' . $this->registry->settings->apps[$currentApp]['baseurl'] . $route);
                exit;
            }
            $this->registry->settings->currentLanguage = $currentLanguage;
        } else {
            if (!isset($this->registry->settings->config['defaultLanguage'])) {
                throw new Exception('No default language for App '.$currentApp.' found!');
            }
            $this->registry->settings->currentLanguage = $this->registry->settings->config['defaultLanguage'];
        }

        $routes = $this->registry->settings->routes;
        $routeData = null;

        if (!$route) {
            $defaultRoute = $this->registry->settings->config['defaultRoute'];
            if ($defaultRoute && isset($routes[$defaultRoute])) {
                $routeName = $defaultRoute;
                $routeData = $routes[$defaultRoute];
            } else {
                throw new Exception('no route given and no default Route defined!');
            }
        } else {
            $found = false;

            foreach ($routes as $currentRoute => $currentRouteData) {
                if (!isset($currentRouteData['route']) || !isset($currentRouteData['controller']) || !isset($currentRouteData['action'])) {
                    continue;
                }
                $routePattern = (isset($currentRouteData['routePattern'])) ? $currentRouteData['routePattern'] : $this->getRegex($currentRoute, $currentRouteData);
                if (preg_match($routePattern, $route, $matches)) {
                    $params = (isset($currentRouteData['parameter'])) ? $currentRouteData['parameter'] : array();
                    foreach ($matches as $paramKey => $paramValue) {
                        if (!is_numeric($paramKey)) {
                            if ($paramKey == 'anonymousParams') {

                                foreach (explode('/', $paramValue) as $anonymousParam) {
                                    $anonymousParam = explode('-', $anonymousParam, 2);
                                    if (trim($anonymousParam[0]) && !isset($params[urldecode($anonymousParam[0])])) {
                                        $params[urldecode($anonymousParam[0])] = (isset($anonymousParam[1])) ? urldecode($anonymousParam[1]) : '';
                                    }
                                }
                            } else {
                                $params[urldecode($paramKey)] = urldecode($paramValue);
                            }
                        }
                    }

                    $routeName = $currentRoute;
                    //$routeData = $this->getRoute($currentRoute, $params);
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $error404Route = (isset($this->registry->settings->config['error404Route'])) ? $this->registry->settings->config['error404Route'] : false;
                if ($error404Route && isset($routes[$error404Route])) {
                    $routeName = $error404Route;
                    $routeData = $routes[$error404Route];
                } else {
                    throw new Exception('no valid route found and no valid 404 Route defined!');
                }
            }
        }        

        
        
        try {
            $this->registry->db->init();
            
            $content = $this->callRoute($routeName, (isset($params) ? $params : array()));
            $this->registry->template->addToSlot('main', $content);
            return $this->registry->template->parse($this->registry->settings->currentApp);
        } catch (Exception $e) {
            $error500Route = (isset($this->registry->settings->config['error500Route'])) ? $this->registry->settings->config['error500Route'] : false;
            if ($error500Route && isset($routes[$error500Route])) {
                $routeData = $routes[$error500Route];
                $routeData['parameter']['exception'] = $e;
                $content = $this->call($routeData['controller'], $routeData['action'], (isset($routeData['parameter']) ? $routeData['parameter'] : array()));
                $this->registry->template->addToSlot('main', $content);
                return $this->registry->template->parse($this->registry->settings->currentApp, false);
            } else {
                throw new Exception('Exception was thrown and no 500 Route defined!');
            }
        }
    }

    /**
     *
     * @param string $route the name of an internal route
     * @param array $params the parameters for the route
     * @param mixed $app the name of an app or null to use the current app
     * @return array returns an array with route information
     */
    public function getRoute($route, $params = array(), $app = null)
    {
        $app = ($app) ? $app : $this->registry->settings->currentApp;
        $routes = $this->registry->settings->get('routes', $app);
        if (!isset($routes[$route])) {
            throw new Exception('Route "' . $route . '" does not exist!');
        }
        
        $routeData = $routes[$route];
        $routeData['parameter'] = (isset($routeData['parameter'])) ? array_merge($routeData['parameter'], (array)$params) : (array)$params;

        $event = new sfEvent($this, 'minimvc.dispatcher.filterRoute');
        $this->registry->events->filter($event, $routeData);
        $routeData = $event->getReturnValue();

        return $routeData;
    }

    /**
     *
     * @param string $route the name of an internal route
     * @param array $params the parameters for the route
     * @param bool $showErrorPages if true, the 401/403 error pages will be called if the user has insuficcient rights
     * @return string the parsed output of the called action
     */
    public function callRoute($route, $params = array(), $showErrorPages = true)
    {
        $routeData = $this->getRoute($route, $params);

        if (isset($routeData['format'])) {
            $this->registry->template->setFormat($routeData['format']);
        } elseif(isset($routeData['parameter']['_format'])) {
            $this->registry->template->setFormat($routeData['parameter']['_format']);
        }

        if (isset($routeData['layout'])) {
            $this->registry->template->setLayout($routeData['layout']);
        }

        if (isset($routeData['parameter']['_action'])) {
            $routeData['action'] = $routeData['parameter']['_action'];
        } elseif(!isset($routeData['action'])) {
            $routeData['action'] = 'index';
        }

        if (isset($routeData['parameter']['_controller'])) {
            if (isset($routeData['parameter']['_module'])) {
                $routeData['controller'] = $routeData['parameter']['_module'] . '_' . $routeData['parameter']['_controller'];
            } else {
                $routeData['controller'] = 'My_' . $routeData['parameter']['_controller'];
            }
        } elseif(!isset($routeData['controller'])) {
            $routeData['controller'] = 'My_Default';
        }


        if (isset($routeData['rights']) && $routeData['rights'] && !((int)$routeData['rights'] & $this->registry->guard->getRights())) {
            if (!$showErrorPages) {
                return '';
            }
            if ($this->registry->guard->getRole() && $this->registry->guard->getRole() != $this->registry->rights->getRoleByKeyword('guest')) {
                $error403Route = (isset($this->registry->settings->config['error403Route'])) ? $this->registry->settings->config['error403Route'] : false;
                if ($error403Route && isset($routes[$error403Route])) {
                    $routeData = $routes[$error403Route];
                } else {
                    throw new Exception('Insufficient rights and no 403 Route defined!');
                }
            } else {
                $error401Route = (isset($this->registry->settings->config['error401Route'])) ? $this->registry->settings->config['error401Route'] : false;
                if ($error401Route && isset($routes[$error401Route])) {
                    $routeData = $routes[$error401Route];
                } else {
                    throw new Exception('Not logged in and no 401 Route defined!');
                }
            }
        }

        return $this->call($routeData['controller'], $routeData['action'], $routeData['parameter']);
    }

    /**
     *
     * @param string $widget the name of an internal widget
     * @param array $params the parameters for the widget
     * @param $app the name of an app or null to use the current app
     * @return array returns an array with widget information
     */
    public function getWidget($widget, $params = array(), $app = null)
    {
        $app = ($app) ? $app : $this->registry->settings->currentApp;
        $widgets = $this->registry->settings->get('widgets', $app);
        if (!isset($widgets[$widget])) {
            throw new Exception('Widget "' . $widget . '" does not exist!');
        }
        if (!isset($widgets[$widget]['controller']) || !isset($widgets[$widget]['action'])) {
            throw new Exception('Widget "' . $widget . '" is invalid (controller or action not set!');
        }
        $widgetData = $widgets[$widget];
        $widgetData['parameter'] = (isset($widgetData['parameter'])) ? array_merge($widgetData['parameter'], (array)$params) : (array)$params;
        return $widgetData;
    }

    /**
     *
     * @param string $widget the name of an internal widget
     * @param array $params the parameters for the widget
     * @return string the parsed output of the called widget
     */
    public function callWidget($widget, $params = array())
    {
        $widgetData = $this->getWidget($widget, $params);

        if (isset($widgetData['rights']) && $widgetData['rights'] && !((int)$widgetData['rights'] & $this->registry->guard->getRights())) {
            return '';
        }

        return $this->call($widgetData['controller'], $widgetData['action'], $widgetData['parameter']);
    }

    /**
     *
     * @param string $task the name of an internal task
     * @param array $params the parameters for the task
     * @param $app the name of an app or null to use the current app
     * @return array returns an array with task information
     */
    public function getTask($task, $params = array(), $app = null)
    {
        $app = ($app) ? $app : $this->registry->settings->currentApp;
        $tasks = $this->registry->settings->get('tasks', $app);
        if (!isset($tasks[$task])) {
            throw new Exception('Task "' . $task . '" does not exist!');
        }
        if (!isset($tasks[$task]['controller']) || !isset($tasks[$task]['action'])) {
            throw new Exception('Task "' . $task . '" is invalid (controller or action not set!');
        }
        $taskData = $tasks[$task];
        $taskData['parameter'] = (isset($taskData['parameter'])) ? array_merge($taskData['parameter'], (array)$params) : (array)$params;
        return $taskData;
    }

    /**
     *
     * @param string $task the name of an internal task
     * @param array $params the parameters for the task
     * @return string the parsed output of the called task
     */
    public function callTask($task, $params = array())
    {
        $taskData = $this->getTask($task, $params);

        return $this->call($taskData['controller'], $taskData['action'], $taskData['parameter']);
    }

    /**
     *
     * @param string $controller the name of a controller
     * @param string $action the name of an action
     * @param array $params an array with parameters
     * @return string the parsed output of the called action
     */
    public function call($controller, $action, $params)
    {
        if (strpos($controller, '_') === false) {
            throw new Exception('Invalid controller "' . $controller . '"!');
        }
        $controllerParts = explode('_', $controller);
        $controllerName = $controllerParts[0] . '_' . $controllerParts[1] . '_Controller';
        $actionName = $action . 'Action';
        if (!class_exists($controllerName)) {
            throw new Exception('Controller "' . $controller . '" does not exist!');
        }
        if (!method_exists($controllerName, $actionName)) {
            throw new Exception('Action "' . $action . '" for Controller "' . $controller . '" does not exist!');
        }

        if (isset($this->registry->settings->config['classes']['view']) && $this->registry->settings->config['classes']['view']) {
            $viewName = $this->registry->settings->config['classes']['view'];
            $view = new $viewName($controllerParts[0], $controllerParts[1], $action);
        } else {
            $view = new MiniMVC_View($controllerParts[0], $controllerParts[1], $action);
        }

        $controllerClass = new $controllerName($view);

        return $controllerClass->$actionName($params);
    }

    /**
     *
     * @param string $route a name of an internal route
     * @param array $routeData information about the route
     * @return string returns a regular expression pattern to parse the called route
     */
    protected function getRegex($route, $routeData)
    {
        $routePattern = $routeData['route'];
        if (isset($routeData['parameterPatterns'])) {
            $search = array();
            $replace = array();
            foreach ($routeData['parameterPatterns'] as $param => $regex) {
                $search[] = ':' . $param . ':';
                $replace[] = '(?P<' . $param . '>' . $regex . ')';
            }

            $routePattern = str_replace($search, $replace, $routePattern);
        }
        $routePattern = preg_replace('#:([^:]+):#i', '(?P<$1>[^\./]+)', $routePattern);
        if (isset($routeData['allowAnonymous']) && $routeData['allowAnonymous']) {
            if (substr($route, -1) == '/') {
                $routePattern .= '(?P<anonymousParams>([^-/]+-[^/]+/)*)';
            } else {
                $routePattern .= '(?P<anonymousParams>(/[^-/]+-[^/]+)*)';
            }
        }
        $routePattern = '#^' . $routePattern . '$#';

        $routes = $this->registry->settings->routes;
        if (isset($routes[$route])) {
            $routes[$route]['routePattern'] = $routePattern;
            $this->registry->settings->saveToCache('routes', $routes);
        }

        return $routePattern;
    }

}