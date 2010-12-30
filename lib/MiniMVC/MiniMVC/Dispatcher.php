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

        if ($_SERVER['QUERY_STRING'] && substr($url, strlen($_SERVER['QUERY_STRING']) * -1) == $_SERVER['QUERY_STRING']) {
            $url = substr($url, 0, -1 + strlen($_SERVER['QUERY_STRING']) * -1);
        }

        $this->registry->settings->set('currentUrl', $url);

        if (isset($_POST['REQUEST_METHOD'])) {
            $_SERVER['REQUEST_METHOD'] = strtoupper($_POST['REQUEST_METHOD']);
        }
        $method = (!empty($_SERVER['REQUEST_METHOD'])) ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET';

        $currentLanguage = null;
        $currentApp = $this->registry->settings->get('currentApp');
        $route = null;

        if (!$currentApp || !$this->registry->settings->get('apps/'.$currentApp)) {
            $defaultApp = $this->registry->settings->get('config/defaultApp');
            if (!$redirectUrl = $this->registry->settings->get('apps/'.$defaultApp.'/baseurl')) {
                throw new Exception('No matching App found and no default app configured!', 404);
            }
            header('Location: ' . $redirectUrl);
            exit;
        }

        $appurls = $this->registry->settings->get('apps/'.$currentApp);
        if (isset($appurls['baseurlI18n'])) {
            if (substr($appurls['baseurlI18n'], 0, 1) == '/') {
                $appurls['baseurlI18n'] = $host . $appurls['baseurlI18n'];
            }
            $languageFormat = $this->registry->settings->get('config/languageFormat', '[a-z]{2}_[A-Z]{2}');
            if (preg_match('#^' . str_replace(':lang:', '(?P<lang>'.$languageFormat.')', $appurls['baseurlI18n']) . '(?P<route>[^\?\#]*)$#', $url, $matches)) {
                $currentLanguage = $matches['lang'];
                $route = $matches['route'];
            }
        }
        if ($route === null && isset($appurls['baseurl'])) {
            if (substr($appurls['baseurl'], 0, 1) == '/') {
                $appurls['baseurl'] = $host . $appurls['baseurl'];
            }
            if (preg_match('#^' . $appurls['baseurl'] . '(?P<route>[^\?\#]*)$#', $url, $matches)) {
                $route = $matches['route'];
            } else {
                $route = false;
            }
        }

        if ($currentLanguage) {
            if (!in_array($currentLanguage, $this->registry->settings->get('config/enabledLanguages', array())) || $currentLanguage == $this->registry->settings->get('config/defaultLanguage')) {
                if (!$redirectUrl = $this->registry->settings->get('apps/'.$currentApp.'/baseurl')) {
                    throw new Exception('No baseurl for App '.$currentApp.' found!');
                }
                header('Location: ' . $redirectUrl . $route . (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : ''));
                exit;
            }
        } else {
            if (!$currentLanguage = $this->registry->settings->get('config/defaultLanguage')) {
                throw new Exception('No default language for App '.$currentApp.' found!');
            }
        }
        $this->registry->settings->set('currentLanguage', $currentLanguage);
        
        $this->registry->settings->set('requestedRoute', $route);

        try {

            $routes = $this->getRoutes(); 
            $routeData = null;

            if (!$route && $route !== false) {
                $defaultRoute = $this->registry->settings->get('config/defaultRoute');
                if ($defaultRoute && isset($routes[$defaultRoute])) {
                    $routeName = $defaultRoute;
                    $routeData = $routes[$defaultRoute];
                } else {
                    throw new Exception('no route given and no valid default Route defined!', 404);
                }
            } else {
                $found = false;

                if ($route) {
                    foreach ($routes as $currentRoute => $currentRouteData) {
                        if (isset($currentRouteData['active']) && !$currentRouteData['active']) {
                            continue;
                        }
                        if (!isset($currentRouteData['route'])) {
                            continue;
                        }
                        if (isset($currentRouteData['method']) && ((is_string($currentRouteData['method']) && strtoupper($currentRouteData['method']) != $method) || (is_array($currentRouteData['method']) && !in_array($method, array_map('strtoupper', $currentRouteData['method']))))) {
                            continue;
                        }

                        if (preg_match($currentRouteData['routePatternGenerated'], $route, $matches)) {
                            $params = (isset($currentRouteData['parameter'])) ? $currentRouteData['parameter'] : array();
                            $anonymousParams = array();
                            foreach ($matches as $paramKey => $paramValue) {
                                if (!is_numeric($paramKey)) {
                                    if ($paramKey == 'anonymousParams') {
                                        foreach (explode('/', $paramValue) as $anonymousParam) {
                                            $anonymousParam = explode('-', $anonymousParam, 2);
                                            if (trim($anonymousParam[0]) && !isset($params[urldecode($anonymousParam[0])])) {
                                                $params[urldecode($anonymousParam[0])] = (isset($anonymousParam[1])) ? urldecode($anonymousParam[1]) : true;
                                                $anonymousParams[urldecode($anonymousParam[0])] = (isset($anonymousParam[1])) ? urldecode($anonymousParam[1]) : true;
                                            }
                                        }
                                    } elseif (trim($paramValue)) {
                                        $params[urldecode($paramKey)] = urldecode($paramValue);
                                    }
                                }
                            }

                            $params = array_merge($params, $anonymousParams);

                            $routeName = $currentRoute;
                            //$routeData = $this->getRoute($currentRoute, $params);
                            $found = true;
                            break;
                        }
                    }
                }

                if (!$found) {
                    throw new Exception('no valid route found!', 404);
                }

                $identifier = md5($routeName . serialize(isset($params) ? $params : array()));

                $csrfData = array(
                    'expected' => isset($_SESSION[$identifier.'_csrf_token']) ? $_SESSION[$identifier.'_csrf_token'] : null,
                    'submitted' => isset($_POST['_csrf_token']) ? $_POST['_csrf_token'] : null
                );
                unset($_SESSION[$identifier.'_csrf_token']);
                $this->registry->settings->set('csrfData', $csrfData);
            }


            $this->registry->settings->set('currentRoute', $routeName);
            $this->registry->settings->set('currentRouteParameter', isset($params) ? $params : array());

            $this->registry->events->notify(new sfEvent($this, 'minimvc.init'));            
            $content = $this->callRoute($routeName, (isset($params) ? $params : array()));
            return $this->registry->template->prepare($content, $this->registry->settings->get('currentApp'))->parse();
        } catch (Exception $e) {

            try {
                //try to handle 401, 403 and 404 exceptions
                switch ($e->getCode()) {
                    case 401:
                        $error401Route = $this->registry->settings->get('config/error401Route');
                        if ($error401Route && isset($routes[$error401Route])) {
                            $routeData = $routes[$error401Route];
                        } else {
                            throw new Exception('Not logged in and no 401 Route defined!');
                        }
                        break;
                    case 403:
                        $error403Route = $this->registry->settings->get('config/error403Route');
                        if ($error403Route && isset($routes[$error403Route])) {
                            $routeData = $routes[$error403Route];
                        } else {
                            throw new Exception('Insufficient rights and no 403 Route defined!');
                        }
                        break;
                    case 404:
                        $error404Route = $this->registry->settings->get('config/error404Route');
                        if ($error404Route && isset($routes[$error404Route])) {
                            $routeData = $routes[$error404Route];
                        } else {
                            throw new Exception('no valid route found and no valid 404 Route defined!');
                        }
                        break;
                    default:
                        //server error / rethrow exception
                        throw $e;
                }

                $routeData['parameter']['exception'] = $e;
                $content = $this->call($routeData['controller'], $routeData['action'], $routeData['parameter']);
                return $this->registry->template->prepare($content, $this->registry->settings->get('currentApp'))->parse();

            } catch (Exception $e) {
                //handle 50x errors
                $error500Route = $this->registry->settings->get('config/error500Route');
                if ($error500Route && isset($routes[$error500Route])) {
                    $routeData = $routes[$error500Route];
                    $routeData['parameter']['exception'] = $e;
                    $content = $this->call($routeData['controller'], $routeData['action'], (isset($routeData['parameter']) ? $routeData['parameter'] : array()));
                    return $this->registry->template->prepare($content, $this->registry->settings->get('currentApp'))->parse();
                } else {
                    throw new Exception('Exception was thrown and no 500 Route defined!');
                }
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
        $app = ($app) ? $app : $this->registry->settings->get('currentApp');

        if (!$routeData = $this->registry->settings->get('routes/'.$route, array(), $app)) {
            throw new Exception('Route "' . $route . '" does not exist!');
        }
        
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
     * @param bool $isMainRoute use or ignore the format/layout/... data of the route to set the layout
     * @return MiniMVC_View the prepared view class of the called action
     */
    public function callRoute($route, $params = array(), $isMainRoute = true)
    {
        $routeData = $this->getRoute($route, $params);

        if ($isMainRoute) {
            if (isset($routeData['format'])) {
                $this->registry->template->setFormat($routeData['format']);
            } elseif(isset($routeData['parameter']['_format'])) {
                $this->registry->template->setFormat($routeData['parameter']['_format']);
            }

            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && isset($routeData['ajaxLayout']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                $this->registry->template->setLayout($routeData['ajaxLayout']);
            } else {
                if (isset($routeData['layout'])) {
                    $this->registry->template->setLayout($routeData['layout']);
                }
            }

        }

        if (isset($routeData['parameter']['_action'])) {
            $routeData['action'] = $routeData['parameter']['_action'];
        } elseif(!isset($routeData['action'])) {
            $routeData['action'] = 'index';
        }

        if (isset($routeData['parameter']['_controller'])) {
            if (isset($routeData['parameter']['_module'])) {
                $routeData['controller'] = ucfirst($routeData['parameter']['_module']) . '_' . ucfirst($routeData['parameter']['_controller']);
            } else {
                $routeData['controller'] = 'My_' . ucfirst($routeData['parameter']['_controller']);
            }
        } elseif(!isset($routeData['controller'])) {
            $routeData['controller'] = 'My_Default';
        }

        if (!isset($routeData['controller']) || !isset($routeData['action'])) {
            throw new Exception('Route "' . $route . '" is invalid (controller or action not set!', 404);
        }


        if (isset($routeData['rights']) && $routeData['rights'] && !$this->registry->guard->userHasRight($routeData['rights'])) {
            if (!$this->registry->guard->userHasRight('guest')) {
                throw new Exception('Insufficient rights', 403);
            } else {
                throw new Exception('Not logged in', 401);
            }
        }

        if (isset($routeData['model']) && is_array($routeData['model'])) {
            if (isset($routeData['model'][0]) && !is_array($routeData['model'][0])) {
                $routeData['model'] = array($routeData['model']);
            }
            $models = array();
            foreach ($routeData['model'] as $modelKey => $modelData) {
                $modelName = $modelData[0];
                $tableName = $modelName.'Table';
                if (!class_exists($modelName) || !class_exists($tableName)) {
                    $models[$modelKey] = null;
                } else {
                    $table = new $tableName;
                    $property = !empty($modelData[1]) ? $modelData[1] : $table->getIdentifier();
                    $refProperty = !empty($modelData[2]) ? $modelData[2] : $property;
                    $models[$modelKey] = empty($routeData['parameter'][$refProperty]) ? null : $table->loadOneBy($property.' = ?', $routeData['parameter'][$refProperty]);
                }
            }
            $routeData['parameter']['model'] = (count($models) === 1 && isset($models[0])) ? reset($models) : $models;
        }

        return $this->call($routeData['controller'], $routeData['action'], isset($routeData['parameter']) ? $routeData['parameter'] : array());
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
        $app = ($app) ? $app : $this->registry->settings->get('currentApp');

        if (!$widgetData = $this->registry->settings->get('widgets/'.$widget, array(), $app)) {
            throw new Exception('Widget "' . $widget . '" does not exist!', 404);
        }
        
        $widgetData['parameter'] = (isset($widgetData['parameter'])) ? array_merge($widgetData['parameter'], (array)$params) : (array)$params;
        return $widgetData;
    }

    /**
     *
     * @param string $widget the name of an internal widget
     * @param array $params the parameters for the widget
     * @return MiniMVC_View the prepared view class of the called widget
     */
    public function callWidget($widget, $params = array())
    {
        $widgetData = $this->getWidget($widget, $params);

        if (isset($widgetData['parameter']['_action'])) {
            $widgetData['action'] = $widgetData['parameter']['_action'];
        } elseif(!isset($widgetData['action'])) {
            $widgetData['action'] = 'index';
        }

        if (isset($widgetData['parameter']['_controller'])) {
            if (isset($widgetData['parameter']['_module'])) {
                $widgetData['controller'] = ucfirst($widgetData['parameter']['_module']) . '_' . ucfirst($widgetData['parameter']['_controller']);
            } else {
                $routeData['controller'] = 'My_' . ucfirst($widgetData['parameter']['_controller']);
            }
        } elseif(!isset($widgetData['controller'])) {
            $widgetData['controller'] = 'My_Default';
        }

        if (!isset($widgetData['controller']) || !isset($widgetData['action'])) {
            throw new Exception('Widget "' . $widget . '" is invalid (controller or action not set!', 404);
        }

        if (isset($widgetData['rights']) && $widgetData['rights'] && !$this->registry->guard->userHasRight($widgetData['rights'])) {
            if (!$this->registry->guard->userHasRight('guest')) {
                throw new Exception('Insufficient rights to call widget "' . $widget . '"!', 403);
            } else {
                throw new Exception('Insufficient rights to call widget "' . $widget . '"!', 401);
            }

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
        $app = ($app) ? $app : $this->registry->settings->get('currentApp');
        if (!$taskData = $this->registry->settings->get('tasks/'.$task, array(), $app)) {
            throw new Exception('Task "' . $task . '" does not exist!', 404);
        }

        if (isset($taskData['assign'])) {
            if (is_array($taskData['assign'])) {
                foreach ($taskData['assign'] as $k => $v) {
                    if (isset($params[$k])) {
                        $params[$v] = $params[$k];
                    }
                }
            } elseif (is_string($taskData['assign']) && isset($params[0])) {
                $params[$taskData['assign']] = $params[0];
            }
        }
        
        $taskData['parameter'] = (isset($taskData['parameter'])) ? array_merge($taskData['parameter'], $params) : $params;
        return $taskData;
    }

    /**
     *
     * @param string $task the name of an internal task
     * @param array $params the parameters for the task
     * @return MiniMVC_View the prepared view class of the called task
     */
    public function callTask($task, $params = array())
    {
        $taskData = $this->getTask($task, $params);

        if (!isset($taskData['controller']) || !isset($taskData['action'])) {
            throw new Exception('Task "' . $task . '" is invalid (controller or action not set!', 404);
        }

        return $this->call($taskData['controller'], $taskData['action'], $taskData['parameter']);
    }

    /**
     *
     * @param string $controller the name of a controller
     * @param string $action the name of an action
     * @param array $params an array with parameters
     * @return MiniMVC_View the prepared view class of the called action
     */
    public function call($controller, $action, $params)
    {
        if (strpos($controller, '_') === false) {
            throw new Exception('Invalid controller "' . $controller . '"!', 404);
        }
        $controllerParts = explode('_', $controller);
        $controllerName = $controllerParts[0] . '_' . $controllerParts[1] . '_Controller';
        $actionName = $action . 'Action';
        if (!class_exists($controllerName)) {
            throw new Exception('Controller "' . $controller . '" does not exist!', 404);
        }
        if (!method_exists($controllerName, $actionName)) {
            throw new Exception('Action "' . $action . '" for Controller "' . $controller . '" does not exist!', 404);
        }

        $viewName = $this->registry->settings->get('config/classes/view', 'MiniMVC_View');
        $view = new $viewName($controllerParts[0], strtolower($controllerParts[1].'/'.$action));

        $controllerClass = new $controllerName($view);
        $this->registry->events->notify(new sfEvent($controllerClass, 'minimvc.call', array('controller' => $controller, 'action' => $action, 'params' => $params)));
        $this->registry->events->notify(new sfEvent($controllerClass, strtolower($controllerParts[0]).'.'.strtolower($controllerParts[1]).'.'.strtolower($action).'.call', array('controller' => $controller, 'action' => $action, 'params' => $params)));
        $return = $controllerClass->$actionName($params);

        if(is_object($return) && $return instanceof $viewName) {
            return $return;
        } elseif ($return === false) {
            return $controllerClass->getView()->prepareEmpty();
        } elseif (is_string($return)) {
            return $controllerClass->getView()->prepareText($return);
        }

        return $controllerClass->getView();
    }

    /**
     *
     * @param string $route a name of an internal route
     * @param array $routeData information about the route
     * @return string returns a regular expression pattern to parse the called route
     */
    protected function getRoutes()
    {
        $cache = $this->registry->cache->get('routes');

        if ($cache) {
            return $cache;
        }

        $routes = $this->registry->settings->get('routes', array());
        foreach ($routes as $route => $routeData) {
            $routePattern = isset($routeData['routePattern']) ? $routeData['routePattern'] : str_replace(array('?','(',')','[',']','.'), array('\\?','(',')?','\\[','\\]','\\.'), $routeData['route']);
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
            if (!empty($routeData['allowAnonymous'])) {
                if (substr($route, -1) == '/') {
                    $routePattern .= '(?P<anonymousParams>([^-/]+-[^/]+/)*)';
                } else {
                    $routePattern .= '(?P<anonymousParams>(/[^-/]+-[^/]+)*)';
                }
            }
            $routePattern = '#^' . $routePattern . '$#';

            $routes[$route]['routePatternGenerated'] = $routePattern;
        }
        $this->registry->cache->set('routes', $routes, true);
        return $routes;
    }

}