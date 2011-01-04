<?php

class Dev_Generate_Controller extends MiniMVC_Controller
{

    public function i18nAction($params)
    {
        if (!$params['module']) {
            return 'Kein Modul angegeben!' . "\n";
        }

        if (!file_exists(MODULEPATH . $params['module'])) {
            return 'Modul existiert nicht!' . "\n";
        }

        $return = '';

        $languages = array();
        $activeLanguages = $this->registry->settings->get('config/enabledLanguages', array());
        foreach ($activeLanguages as $language) {
            $MiniMVC_i18n = array();
            $languages[$language] = array('found' => array(), 'new' => array());

            if (file_exists(MODULEPATH . $params['module'].'/i18n/'.$language.'.php')) {
                include MODULEPATH . $params['module'].'/i18n/'.$language.'.php';
                if (isset($MiniMVC_i18n[$params['module']])) {
                    $languages[$language]['found'] = $MiniMVC_i18n[$params['module']];
                }
            }
        }

        $i18nFound = $this->searchI18n(MODULEPATH . $params['module']);
        asort($i18nFound);

        foreach ($languages as $currentLanguage => $currentLanguageData) {
            foreach ($i18nFound as $newI18n) {
                if (!isset($currentLanguageData['found'][$newI18n])) {
                    $languages[$currentLanguage]['new'][$newI18n] = '$MiniMVC_i18n[\''.$params['module'].'\'][\''.$newI18n.'\'] = \''.$newI18n.'\';';
                }
            }
        }

        foreach ($languages as $currentLanguage => $currentLanguageData) {
            if (file_exists(MODULEPATH . $params['module'].'/i18n/'.$currentLanguage.'.php')) {
                file_put_contents(MODULEPATH . $params['module'].'/i18n/'.$currentLanguage.'.php', "\n".implode("\n", $languages[$currentLanguage]['new']), FILE_APPEND);
                $return .= 'Datei '.MODULEPATH . $params['module'].'/i18n/'.$currentLanguage.'.php'.' aktualisiert!'."\n";
            } else {
                file_put_contents(MODULEPATH . $params['module'].'/i18n/'.$currentLanguage.'.php', '<?php'."\n".implode("\n", $languages[$currentLanguage]['new']));
                $return .= 'Datei '.MODULEPATH . $params['module'].'/i18n/'.$currentLanguage.'.php'.' angelegt!'."\n";
            }
        }
        
        return $return .= 'I18n-Dateien erfolgreich generiert!';

    }

    protected function searchI18n($folder, $found = array())
    {
        if (!is_dir($folder)) {
            return $found;
        }

        $regex = '#(\$|->)(t|i18n)->([\w]+)#i';

        foreach (scandir($folder) as $file) {
            if (is_dir($folder.'/'.$file) && $file !== '.' && $file !== '..') {
                $found = $this->searchI18n($folder.'/'.$file, $found);
            } elseif (is_file($folder.'/'.$file) && substr($file, -4) == '.php') {
                $content = file_get_contents($folder.'/'.$file);
                $matches = array();
                if (preg_match_all($regex, $content, $matches)) {
                    foreach ($matches[3] as $match) {
                        if ($match == 'get') {
                            continue;
                        }
                        $found[$match] = $match;
                    }
                }
            }
        }
        return $found;
    }

    public function appAction($params)
    {
        if (!$params['app']) {
            return 'Keinen Appnamen angegeben!' . "\n";
        }

        if (file_exists(APPPATH . $params['app'])) {
            return 'App existiert bereits!' . "\n";
        }

        if (!is_writable(APPPATH)) {
            return 'Keine Schreibrechte im App-Ordner!' . "\n";
        }

        $path = APPPATH . $params['app'];
        $dummy = MODULEPATH . 'Dev/dummies';

        mkdir($path);
        mkdir($path . '/i18n');
        mkdir($path . '/settings');
        mkdir($path . '/view');
        mkdir($path . '/web');
        mkdir($path . '/web/js');
        mkdir($path . '/web/css');
        mkdir($path . '/web/images');
        mkdir($path . '/lib');

        file_put_contents($path . '/view/default.php', str_replace('APP', $params['app'], file_get_contents($dummy . '/app_view.php')));
        file_put_contents($path . '/view/default.json.php', str_replace('APP', $params['app'], file_get_contents($dummy . '/app_view.json.php')));
        file_put_contents($path . '/i18n/de_DE.php', str_replace('APP', $params['app'], file_get_contents($dummy . '/de_DE_app.php')));
        file_put_contents($path . '/i18n/en_US.php', str_replace('APP', $params['app'], file_get_contents($dummy . '/en_US_app.php')));
        //file_put_contents($path . '/settings/slots.php', str_replace('APP', $params['app'], file_get_contents($dummy . '/slots_app.php')));
        file_put_contents($path . '/settings/view.php', str_replace('APP', $params['app'], file_get_contents($dummy . '/view.php')));
        file_put_contents($path . '/settings/config.php', str_replace('APP', $params['app'], file_get_contents($dummy . '/config_app.php')));
        file_put_contents($path . '/settings/routes.php', str_replace('APP', $params['app'], file_get_contents($dummy . '/routes_app.php')));
        file_put_contents($path . '/settings/widgets.php', str_replace('APP', $params['app'], file_get_contents($dummy . '/widgets_app.php')));

        if ($params['w']) {
            file_put_contents(WEBPATH . $params['app'] . '.php', str_replace('APP', $params['app'], file_get_contents($dummy . '/app_web.php')));
            file_put_contents(WEBPATH . $params['app'] . '_dev.php', str_replace('APP', $params['app'], file_get_contents($dummy . '/app_web_dev.php')));
        }

        return 'App was generated successfully!'."\n"
              .'Make sure to define the $MiniMVC_apps[\''.$params['app'].'\'][\'baseurl\'] in your settings/apps.php and settings/apps_dev.php files!'."\n"
              .'Optionally, you can modify the web/.htaccess file to enable pretty URLs.';
    }

    public function moduleAction($params)
    {
        if (!$params['module']) {
            return 'Kein Modul angegeben!' . "\n";
        }

        if (file_exists(MODULEPATH . $params['module'])) {
            return 'Modul existiert bereits!' . "\n";
        }

        if (!is_writable(MODULEPATH)) {
            return 'Keine Schreibrechte im Module-Ordner!' . "\n";
        }

        $controller = !empty($params['controller']) ? $params['controller'] : $params['module'];

        $path = MODULEPATH . $params['module'];
        $dummy = MODULEPATH . 'Dev/dummies';

        mkdir($path);
        mkdir($path . '/controller');
        mkdir($path . '/model');
        //mkdir($path . '/Model/Schema');
        mkdir($path . '/settings');
        mkdir($path . '/i18n');
        mkdir($path . '/view');
        mkdir($path . '/view/'.strtolower($controller));
        mkdir($path . '/web');
        mkdir($path . '/web/js');
        mkdir($path . '/web/css');
        mkdir($path . '/web/images');
        mkdir($path . '/lib');
        //mkdir($path . '/Lib/Migrations');

        $search = array(
            'MODULELCFIRST',
            'CONTROLLERLCFIRST',
            'MODLC',
            'MODULE',
            'CONTROLLERLC',
            'CONTROLLER',
        );
        $replace = array(
            strtolower(substr($params['module'], 0, 1)) . substr($params['module'], 1),
            strtolower(substr($controller, 0, 1)) . substr($controller, 1),
            strtolower($params['module']),
            $params['module'],
            strtolower($controller),
            $controller
        );
        file_put_contents($path . '/controller/'.$controller.'.php', str_replace($search, $replace, file_get_contents($dummy . '/Default.php')));
        file_put_contents($path . '/Installer.php', str_replace($search, $replace, file_get_contents($dummy . '/Installer.php')));
        file_put_contents($path . '/i18n/de_DE.php', str_replace($search, $replace, file_get_contents($dummy . '/de_DE.php')));
        file_put_contents($path . '/i18n/en_US.php', str_replace($search, $replace, file_get_contents($dummy . '/en_US.php')));
        file_put_contents($path . '/view/'.strtolower($controller).'/index.php', str_replace($search, $replace, file_get_contents($dummy . '/index.php')));
        file_put_contents($path . '/view/'.strtolower($controller).'/index.json.php', str_replace($search, $replace, file_get_contents($dummy . '/index.json.php')));
        file_put_contents($path . '/view/'.strtolower($controller).'/widget.php', str_replace($search, $replace, file_get_contents($dummy . '/widget.php')));
        file_put_contents($path . '/view/'.strtolower($controller).'/new.php', str_replace($search, $replace, file_get_contents($dummy . '/new.php')));
        file_put_contents($path . '/view/'.strtolower($controller).'/show.php', str_replace($search, $replace, file_get_contents($dummy . '/show.php')));
        file_put_contents($path . '/view/'.strtolower($controller).'/show.json.php', str_replace($search, $replace, file_get_contents($dummy . '/show.json.php')));
        file_put_contents($path . '/view/'.strtolower($controller).'/edit.php', str_replace($search, $replace, file_get_contents($dummy . '/edit.php')));
        file_put_contents($path . '/settings/config.php', str_replace($search, $replace, file_get_contents($dummy . '/config.php')));
        file_put_contents($path . '/settings/routes.php', str_replace($search, $replace, file_get_contents($dummy . '/routes.php')));
        file_put_contents($path . '/settings/widgets.php', str_replace($search, $replace, file_get_contents($dummy . '/widgets.php')));
        file_put_contents($path . '/model/definition.php', str_replace($search, $replace, file_get_contents($dummy . '/definition.php')));
        //file_put_contents($path . '/settings/slots.php', str_replace(array('MODLC', 'MODULE'), array(strtolower($params['module']), $params['module']), file_get_contents($dummy . '/slots.php')));
        //file_put_contents($path . '/Model/Schema/schema.yml', str_replace(array('MODLC', 'MODULE'), array(strtolower($params['module']), $params['module']), file_get_contents($dummy . '/schema.yml')));

        return 'Modul wurde erfolgreich generiert!';
    }

    public function modelAction($params)
    {
        if (!$params['module']) {
            return 'Kein Modul angegeben!' . "\n";
        }
        if (!$params['model']) {
            return 'Kein Modelnamen angegeben!' . "\n";
        }

        $definition = $this->getModelDefinition($params['module'], $params['model']);

        if (!$definition) {
            return 'No model definition found for '.$params['model'].' in module '.$params['module'].'...' . "\n";
        }

        if (empty($definition['identifier'])) {
            $definition['identifier'] = 'id';
        }
        if (!isset($definition['autoIncrement'])) {
            $definition['autoIncrement'] = false;
        }
        if (!isset($definition['columns'][$definition['identifier']])) {
            $definition['columns'][$definition['identifier']] = 'int';
        }
        if (!isset($definition['relations'])) {
            $definition['relations'] = array();
        }

        $columns = ($params['columns']) ? array_map('trim', explode(',', $params['columns'])) : array('id', 'slug', 'title');

        if (false !== ($idColumn = array_search('id', $columns))) {
            unset($columns[$idColumn]);
        }


        $model = ucfirst($params['model']);

        $search = array(
            '{name}',
            '{table}',
            '{module}',
            '{modlc}',
            '{namelcfirst}',
            '{columns_list}',
            '{columns_sql}',
            '{columns_phpdoc}',
            '{columns_form}',
            '{auto_increment}',
            '{identifier}',
            '{relations_list}',
            '{relations_methods}',
            '{modelClass}',
            '{tableClass}',
            '{collectionClass}'
        );
        $replace = array(
            $model,
            strtolower(preg_replace('/(?!^)[[:upper:]]+/', '_$0', $model)),
            $params['module'],
            strtolower($params['module']),
            strtolower(substr($model, 0, 1)) . substr($model, 1),
            '\''.implode('\', \'', array_keys($definition['columns'])).'\'',
            $this->getSqlCode($definition, $model),
            $this->getPhpDocCode($definition, $model),
            $this->getFormCode($definition, $model),
            $definition['autoIncrement'] ? 'true' : 'false',
            $definition['identifier'],
            $this->getRelationsListCode($definition, $model),
            $this->getRelationsMethodsCode($definition, $model),
            $this->registry->settings->get('config/classes/model', 'MiniMVC_Model'),
            $this->registry->settings->get('config/classes/table', 'MiniMVC_Table'),
            $this->registry->settings->get('config/classes/collection', 'MiniMVC_Collection')
        );

        $path = MODULEPATH . $params['module'].'/model';
        $dummy = MODULEPATH . 'Dev/dummies';

        $message = 'Erstelle Models...'."\n";
        if (!file_exists($path . '/'.$model.'.php')) {
            file_put_contents($path . '/'.$model.'.php', str_replace($search, $replace, file_get_contents($dummy . '/Model.php')));
            $message .= '-> Datei '.$model.'.php erstellt'."\n";
        } else {
            $message .= '-> Datei '.$model.'.php existiert bereits'."\n";
        }
        if (!file_exists($path . '/'.$model.'Table.php')) {
            file_put_contents($path . '/'.$model.'Table.php', str_replace($search, $replace, file_get_contents($dummy . '/Table.php')));
            $message .= '-> Datei '.$model.'Table.php erstellt'."\n";
        } else {
            $message .= '-> Datei '.$model.'Table.php existiert bereits'."\n";
        }
        if (!file_exists($path . '/'.$model.'Collection.php')) {
            file_put_contents($path . '/'.$model.'Collection.php', str_replace($search, $replace, file_get_contents($dummy . '/Collection.php')));
            $message .= '-> Datei '.$model.'Collection.php erstellt'."\n";
        } else {
            $message .= '-> Datei '.$model.'Collection.php existiert bereits'."\n";
        }

        file_put_contents($path . '/'.$model.'Base.php', str_replace($search, $replace, file_get_contents($dummy . '/ModelBase.php')));
        $message .= '-> Datei '.$model.'Base.php erstellt'."\n";
        file_put_contents($path . '/'.$model.'TableBase.php', str_replace($search, $replace, file_get_contents($dummy . '/TableBase.php')));
        $message .= '-> Datei '.$model.'TableBase.php erstellt'."\n";


        return $message;
    }

    public function controllerAction($params)
    {
        if (!$params['module']) {
            return 'Kein Modul angegeben!' . "\n";
        }
        if (!$params['controller']) {
            return 'Kein Controllername angegeben!' . "\n";
        }

        $params['module'] = ucfirst($params['module']);
        $controller = ucfirst($params['controller']);

        $search = array(
            'MODULELCFIRST',
            'CONTROLLERLCFIRST',
            'MODLC',
            'MODULE',
            'MODULELCFIRST',
            'CONTROLLERLC',
            'CONTROLLER'
        );
        $replace = array(
            strtolower(substr($params['module'], 0, 1)) . substr($params['module'], 1),
            strtolower(substr($controller, 0, 1)) . substr($controller, 1),
            strtolower($params['module']),
            $params['module'],
            strtolower(substr($params['module'], 0, 1)) . substr($params['module'], 1),
            strtolower($controller),
            $controller
        );

        $path = MODULEPATH . $params['module'].'/controller';
        $dummy = MODULEPATH . 'Dev/dummies';

        $message = 'Erstelle Controller...'."\n";
        if (!file_exists($path . '/'.$controller.'.php')) {
            mkdir($path . '/../view/'.strtolower($controller));
            file_put_contents($path . '/'.$controller.'.php', str_replace($search, $replace, file_get_contents($dummy . '/Controller.php')));
            file_put_contents($path . '/../view/'.strtolower($controller).'/index.php', str_replace($search, $replace, file_get_contents($dummy . '/index.php')));
            file_put_contents($path . '/../view/'.strtolower($controller).'/index.json.php', str_replace($search, $replace, file_get_contents($dummy . '/index.json.php')));
            file_put_contents($path . '/../view/'.strtolower($controller).'/widget.php', str_replace($search, $replace, file_get_contents($dummy . '/widget.php')));
            file_put_contents($path . '/../view/'.strtolower($controller).'/new.php', str_replace($search, $replace, file_get_contents($dummy . '/new.php')));
            file_put_contents($path . '/../view/'.strtolower($controller).'/show.php', str_replace($search, $replace, file_get_contents($dummy . '/show.php')));
            file_put_contents($path . '/../view/'.strtolower($controller).'/show.json.php', str_replace($search, $replace, file_get_contents($dummy . '/show.json.php')));
            file_put_contents($path . '/../view/'.strtolower($controller).'/edit.php', str_replace($search, $replace, file_get_contents($dummy . '/edit.php')));

            $message .= '-> Datei '.$controller.'.php erstellt'."\n";
        } else {
            $message .= '-> Datei '.$controller.'.php existiert bereits'."\n";
        }
        return $message;
    }

    protected function getSqlCode($definition, $model)
    {
        $types = array(
            'int' => 'INT(11)',
            'integer' => 'INT(11)',
            'bool' => 'TINYINT(1) NOT NULL',
            'boolean' => 'TINYINT(1) NOT NULL',
            'float' => 'FLOAT',
            'double' => 'DOUBLE',
            'string' => 'VARCHAR(255)',
            'array' => 'TEXT'
        );

        $return = array();
        foreach ($definition['columns'] as $column => $type) {
            $sqlType = isset($types[strtolower($type)]) ? $types[strtolower($type)] : 'VARCHAR(255)';
            if ($column == $definition['identifier']) {
                array_unshift($return, $column . ' ' .$sqlType . ($definition['autoIncrement'] ? ' AUTO_INCREMENT' : ''));
            } else {
                $return[] = '                      ' . $column . ' ' .$sqlType;
            }
        }
        return implode(",\n", $return);

    }

    protected function getPhpDocCode($definition, $model)
    {
        $return = '';
        foreach ($definition['columns'] as $column => $type) {
            $return .= '@property ' . strtolower($type) . ' $' . $column . "\n * ";
        }
        return $return;
    }

    protected function getFormCode($definition, $model)
    {
        $types = array(
            'int' => 'Text',
            'integer' => 'Text',
            'bool' => 'Checkbox',
            'boolean' => 'Checkbox',
            'float' => 'Text',
            'double' => 'Text',
            'string' => 'Text',
            'array' => 'Text'
        );

        $code = '$form->setElement(new MiniMVC_Form_Element_{type}(\'{column}\',
                        array(\'label\' => $i18n->{namelcfirst}Form{columncc}Label),
                        array(
                            //new MiniMVC_Form_Validator_Required(array(\'errorMessage\' => $i18n->{namelcfirst}Form{columncc}Error))
                )));
        ';

        $output = '';
        $search = array('{namelcfirst}', '{column}', '{columnucfirst}', '{columncc}', '{type}');

        foreach ($definition['columns'] as $column => $type) {
            if ($column == $definition['identifier']) {
                continue;
            }
            $replace = array(strtolower(substr($model, 0, 1)) . substr($model, 1), $column, ucfirst($column), ucfirst(preg_replace('/_(.)/e', 'ucfirst("$1")', $column)), isset($types[strtolower($type)]) ? $types[strtolower($type)] : 'Text');
            $output .= str_replace($search, $replace, $code);
        }
        return $output;
    }

    protected function getRelationsListCode($definition, $model)
    {
        $return = array();
        foreach ($definition['relations'] as $relation => $data) {
            if (empty($data[0]) || empty($data[1]) || empty($data[2])) {
                continue;
            }
            $return[] = '\''.$relation.'\' => array(\''.$data[0].'\', \''.$data[1].'\', \''.$data[2].'\''.(!empty($data[3]) ? ($data[3] === true ? ', true' : ', \''.$data[3].'\'') : '').')';
        }
        return implode(', ', $return);
    }

    protected function getRelationsMethodsCode($definition, $model)
    {
        $code = '
    /**
     *
     * @param mixed $identifier the identifier of the related model or true to return all stored models of this relation
     * @return {related_model_or_array}
     */
    public function get{relation}($identifier = true)
    {
        return $this->getRelated(\'{relation}\', $identifier = true);
    }

    /**
     *
     * @param MiniMVC_Model $identifier the related model
     * @param bool $update whether to update the model if it is already stored or not
     */
    public function set{relation}($identifier = null, $update = true)
    {
        return $this->setRelated(\'{relation}\', $identifier = null, $update = true);
    }

    /**
     *
     * @param mixed $identifier either a model object, an identifier of a related model or true
     * @param bool $realDelete whether to delete the model from the database (true) or just from this object(false) defaults to true
     * @param bool $realDeleteLoad if the identifier is true only the related models currently assigned to this object will be deleted. with relaDeleteLoad=true, all related models will be deleted
     * @param bool $realDeleteCleanRef if relaDeleteLoad is true, set realDeleteCleanRef=true to clean up the ref table (for m:n relations)
     */
    public function delete{relation}($identifier = true, $realDelete = true, $realDeleteLoad = false, $realDeleteCleanRef = false)
    {
        return $this->deleteRelated(\'{relation}\', $identifier = true, $realDelete = true, $realDeleteLoad = false, $realDeleteCleanRef = false);
    }

    /**
     *
     * @param string $condition the where-condition
     * @param array $values values for the placeholders in the condition
     * @param string $order the order
     * @param int $limit the limit
     * @param int $offset the offset
     * @return {related_model_or_array}
     */
    public function load{relation}($condition = null, $values = array(), $order = null, $limit = null, $offset = null)
    {
        return $this->loadRelated(\'{relation}\', $condition = null, $values = array(), $order = null, $limit = null, $offset = null);
    }

    /**
     *
     * @param mixed $identifier a related model object, the identifier of a related model currently asigned to this model or true to save all related models
     * @param bool $saveThisOnDemand whether to allow this model to be saved in the database if its new (to generate an auto-increment identifier)
     */
    public function save{relation}($identifier = true, $saveThisOnDemand = true)
    {
        return $this->saveRelated(\'{relation}\', $identifier = true, $saveThisOnDemand = true);
    }

    /**
     *
     * @param mixed $identifier a related model object, the identifier of a related model
     * @param bool $loadRelated whether to load the related object (if identifier is not already loaded and assigned to this model)
     */
    public function link{relation}($identifier = null, $loadRelated = false)
    {
        return $this->linkRelated(\'{relation}\', $identifier = null, $loadRelated = false);
    }

    /**
     *
     * @param mixed $identifier a related model object, the identifier of a related model or unlink to save all related models
     */
    public function unlink{relation}($identifier = true)
    {
        return $this->unlinkRelated(\'{relation}\', $identifier = true);
    }

    ';


        $return = array();
        $search = array('{relation}', '{related_model_or_array}');
        foreach ($definition['relations'] as $relation => $data) {
            $replace = array($relation, isset($data[3]) && $data[3] === true ? $data[0] : $data[0].'Collection');
            $return[] = str_replace($search, $replace, $code);
        }
        return implode("\n", $return);
    }

    protected function getModelDefinition($module, $model)
    {
        $modelDefinition = array();
        $file = MODULEPATH . $module.'/model/definition.php';
        if (file_exists($file)) {
            include $file;
        }
        return isset($modelDefinition[$model]) ? $modelDefinition[$model] : false;
    }
}