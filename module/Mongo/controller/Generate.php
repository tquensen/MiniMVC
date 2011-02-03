<?php
class Mongo_Generate_Controller extends MiniMVC_Controller
{
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

        if (!isset($definition['columns']['_id'])) {
            $definition['columns']['_id'] = 'MongoId';
        }
        if (!isset($definition['relations'])) {
            $definition['relations'] = array();
        }
        if (!isset($definition['embedded'])) {
            $definition['embedded'] = array();
        }

        if (!isset($definition['autoIncrement'])) {
            $definition['autoIncrement'] = true;
        }

        $model = ucfirst($params['model']);

        $search = array(
            '{name}',
            '{module}',
            '{modlc}',
            '{namelcfirst}',
            '{table}',
            '{columns_list}',
            '{columns_phpdoc}',
            '{columns_form}',
            '{relations_methods}',
            '{embedded_methods}',
            '{auto_increment}'
        );
        $replace = array(
            $model,
            $params['module'],
            strtolower($params['module']),
            strtolower(substr($model, 0, 1)) . substr($model, 1),
            strtolower(preg_replace('/(?!^)[[:upper:]]+/', '_$0', $model)),
            '\''.implode('\', \'', array_keys($definition['columns'])).'\'',
            $this->getPhpDocCode($definition, $model),
            $this->getFormCode($definition, $model),
            $this->getRelationsMethodsCode($definition, $model),
            $this->getEmbeddedMethodsCode($definition, $model),
            (bool) $definition['autoIncrement'] ? 'true' : 'false'
        );

        $path = MODULEPATH . $params['module'].'/model';
        $dummy = MODULEPATH . 'Mongo/dummies';

        $message = 'Erstelle Models...'."\n";
        if (!file_exists($path . '/'.$model.'.php')) {
            file_put_contents($path . '/'.$model.'.php', str_replace($search, $replace, file_get_contents($dummy . '/Model.php')));
            $message .= '-> Datei '.$model.'.php erstellt'."\n";
        } else {
            $message .= '-> Datei '.$model.'.php existiert bereits'."\n";
        }
        if (!file_exists($path . '/'.$model.'Repository.php')) {
            file_put_contents($path . '/'.$model.'Repository.php', str_replace($search, $replace, file_get_contents($dummy . '/Repository.php')));
            $message .= '-> Datei '.$model.'Repository.php erstellt'."\n";
        } else {
            $message .= '-> Datei '.$model.'Repository.php existiert bereits'."\n";
        }

        return $message;
    }

    public function embeddedAction($params)
    {
        if (!$params['module']) {
            return 'Kein Modul angegeben!' . "\n";
        }
        if (!$params['model']) {
            return 'Kein Modelnamen angegeben!' . "\n";
        }
        $model = ucfirst($params['model']);
        $search = array(
            '{name}'
        );
        $replace = array(
            $model
        );

        $path = MODULEPATH . $params['module'].'/model';
        $dummy = MODULEPATH . 'Mongo/dummies';

        $message = 'Erstelle Model...'."\n";
        if (!file_exists($path . '/'.$model.'.php')) {
            file_put_contents($path . '/'.$model.'.php', str_replace($search, $replace, file_get_contents($dummy . '/Embedded.php')));
            $message .= '-> Datei '.$model.'.php erstellt'."\n";
        } else {
            $message .= '-> Datei '.$model.'.php existiert bereits'."\n";
        }

        return $message;
    }

    

    protected function getPhpDocCode($definition, $model)
    {
        $return = '';
        foreach ($definition['columns'] as $column => $type) {
            $return .= '@property ' . $type . ' $' . $column . "\n * ";
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
            if ($column == '_id') {
                continue;
            }
            $replace = array(strtolower(substr($model, 0, 1)) . substr($model, 1), $column, ucfirst($column), ucfirst(preg_replace('/_(.)/e', 'ucfirst("$1")', $column)), isset($types[strtolower($type)]) ? $types[strtolower($type)] : 'Text');
            $output .= str_replace($search, $replace, $code);
        }
        return $output;
    }

    protected function getRelationsMethodsCode($definition, $model)
    {
    $code1 = '
    /**
     * @return {foreignModel}
     */
    public function get{relation}()
    {
        return {foreignModel}Repository::get()->findOne(array(\'{foreignProperty}\' => $this->{localProperty}));
    }

    /**
     * @param {foreignModel}|mixed $related either a {foreignModel} object or a {foreignModel}->{foreignProperty}-value
     * @param bool $save set to false to prevent a save() call
     * @return bool
     */
    public function set{relation}($related, $save = true)
    {
        if (is_object($related) && $related instanceof {foreignModel}) {
            $this->{localProperty} = $related->{foreignProperty};
        } else {
            $this->{localProperty} = $related;
        }
        return $save ? $this->save() : true;
    }

    /**
     * @param bool $save set to false to prevent a save() call
     * @return bool
     */
    public function remove{relation}($save = true)
    {
        $this->{localProperty} = null;
        return $save ? $this->save() : true;
    }
    ';

    $code2 = '
    /**
     *
     * @param array $query Additional fields to filter.
     * @param array $sort The fields by which to sort.
     * @param int $limit The number of results to return.
     * @param int $skip The number of results to skip.
     * @return array
     */
    public function get{relation}($query = array(), $sort = array(), $limit = null, $skip = null)
    {
        $query = (array) $query;
        $query[\'{foreignProperty}\'] = $this->{localProperty};
        return {foreignModel}Repository::get()->find($query = array(), $sort = array(), $limit = null, $skip = null);
    }

    /**
     * @param {foreignModel}|mixed $related either a {foreignModel} object, a {foreignModel}->_id-value or an array with multiple {foreignModel}s
     * @param bool $save set to false to prevent a save() call
     * @return bool
     */
    public function set{relation}($related, $save = true)
    {
        if (is_array($related)) {
            foreach ($related as $rel) {
                $this->set{relation}($rel, $save);
            }
            return true;
        }
        if (!is_object($related) || !($related instanceof {foreignModel})) {
            $related = {foreignModel}Repository::get()->findOne($related);
        }
        if (!$related) {
            throw new InvalidArgumentException(\'Could not find valid {foreignModel}\');
        }
        $related->{foreignProperty} = $this->{localProperty};
        return $save ? $related->save() : true;
    }

    /**
     * @param {foreignModel}|mixed $related either a {foreignModel} object, a {foreignModel}->_id-value  or an array with multiple {foreignModel}s
     * @param bool $save set to false to prevent a save() call
     * @return bool
     */
    public function remove{relation}($related, $save = true)
    {
        if (is_array($related)) {
            foreach ($related as $rel) {
                $this->remove{relation}($rel, $save);
            }
            return true;
        }
        if (!is_object($related) || !($related instanceof {foreignModel})) {
            $related = {foreignModel}Repository::get()->findOne($related);
        }
        if (!$related) {
            throw new InvalidArgumentException(\'Could not find valid {foreignModel}\');
        }
        if ($related->{foreignProperty} != $this->{localProperty}) {
            return false;
        }
        $related->{foreignProperty} = null;
        return $save ? $related->save() : true;
    }
    ';

        $return = array();
        $search = array('{relation}', '{foreignModel}', '{localProperty}', '{foreignProperty}');
        foreach ($definition['relations'] as $relation => $data) {
            $replace = array($relation, $data[0], $data[1], $data[2]);
            $return[] = str_replace($search, $replace, (isset($data[3]) && $data[3] === true) ? $code1 : $code2);
        }
        return implode("\n", $return);
    }

    protected function getEmbeddedMethodsCode($definition, $model)
    {
    $code1 = '
    /**
     * @return {foreignModel}
     */
    public function get{relation}()
    {
        return new {foreignModel}($this->_properties[\'{localProperty}\'];
    }

    /**
     * @param {foreignModel}|array $related a {foreignModel} object or an array representing a {foreignModel}
     * @param bool $save set to false to prevent a save() call
     * @return bool
     */
    public function set{relation}($related, $save = true)
    {
        if (is_object($related) && $related instanceof Mongo_Embedded) {
            $this->_properties[\'{localProperty}\'] = $related->getData();
        } else {
            $this->_properties[\'{localProperty}\'] = $related;
        }
        return $save ? $this->save() : true;
    }

    /**
     * @param bool $save set to false to prevent a save() call
     * @return bool
     */
    public function remove{relation}($save = true)
    {
        $this->{localProperty} = null;
        return $save ? $this->save() : true;
    }
    ';

    $code2 = '
    /**
     *
     * @param bool|int $key the key of the model to get or true (default) to get all
     * @return array
     */
    public function get{relation}($key = true)
    {
        if ($key === true) {
            $return = array();
            foreach ($this->_properties[\'{localProperty}\'] as $currentKey => $entry) {
                $return[$currentKey] = new {foreignModel}($entry);
            }
            return $return;
        }
        return isset($this->_properties[\'{localProperty}\'][$key]) ? new {foreignModel}($this->_properties[\'{localProperty}\'][$key]) : null;
    }

    /**
     * overwrites {relation} with the data provided
     *
     * @param {foreignModel}|array $related if $key = true, an array of {foreignModel} objects or an array of arrays representing {foreignModel}s, if $key = int: a {foreignModel} or an array representing a {foreignModel}
     * @param bool|int $key the key of the model to overwrites or true (default) to overwrite all
     * @param bool $save set to false to prevent a save() call
     * @return bool
     */
    public function set{relation}($related, $key = true, $save = true)
    {
        if ($key === true) {
            $entries = array();
            foreach ($related as $rel) {
                if (is_object($related) && $related instanceof Mongo_Embedded) {
                    $related = $related->getData();
                }
                $entries[] = $related;
            }

            $this->_properties[\'{localProperty}\'] = $entries;
        } else {
            if (!isset($this->_properties[\'{localProperty}\'][$key])) {
                return false;
            }
            if (is_object($related) && $related instanceof Mongo_Embedded) {
                $related = $related->getData();
            }
            $this->_properties[\'{localProperty}\'][$key] = $related;
        }
        
        return $save ? $this->save() : true;
    }

    /**
     * adds the provided {foreignModel}s to the {relation}
     *
     * @param array $related an array of {foreignModel} objects or an array of arrays representing {foreignModel}s or an array with multiple {foreignModel}s (use multiple)
     * @param bool $multiple set to true if you are passing multiple {foreignModel}s
     * @param bool $save set to false to prevent a save() call
     * @return bool
     */
    public function add{relation}($related, $multiple = false, $save = true)
    {
        if (!$multiple) {
            $related = array($related);
        }
        $entries = array();
        foreach ($related as $rel) {
            if (is_object($related) && $related instanceof Mongo_Embedded) {
                $related = $related->getData();
            }
            $entries[] = $related;
        }
        $currentEntries = (array) $this->_properties[\'{localProperty}\'];
        $this->_properties[\'{localProperty}\'] = array_merge($currentEntries, $entries);

        return $save ? $this->save() : true;
    }

    /**
     * removes the chosen {foreignModel} (or all for $key = true) from the {relation} and reindexes the {localProperty} array
     *
     * @param bool|int $key the key of the model to remove or true (default) to remove all
     * @param bool $save set to false to prevent a save() call
     * @return bool
     */
    public function remove{relation}($key = true, $save = true)
    {
        if ($key === true) {
             $this->{localProperty} = array();
        } else {
            unset($this->_properties[\'{localProperty}\'][$key]);
            $this->_properties[\'{localProperty}\'] = array_values($this->_properties[\'{localProperty}\']);
        }
        
        return $save ? $this->save() : true;
    }
    ';

        $return = array();
        $search = array('{relation}', '{foreignModel}', '{localProperty}');
        foreach ($definition['embedded'] as $relation => $data) {
            $replace = array($relation, $data[0], $data[1]);
            $return[] = str_replace($search, $replace, (isset($data[2]) && $data[2] === true) ? $code1 : $code2);
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
