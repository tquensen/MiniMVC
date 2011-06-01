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

        if (!isset($definition['mapReduce'])) {
            $definition['mapReduce'] = false;
        }
        
        if ($definition['mapReduce']) {
            if (!isset($definition['columns']['_id'])) {
                $definition['columns']['_id'] = 'mixed';
            }
            if (!isset($definition['columns']['value'])) {
                $definition['columns']['value'] = 'mixed';
            }
        } elseif (!isset($definition['columns']['_id'])) {
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
            '{relations_list}',
            '{embedded_methods}',
            '{embedded_list}',
            '{auto_increment}',
            '{parent_model}'
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
            $this->getRelationsListCode($definition, $model),
            $this->getEmbeddedMethodsCode($definition, $model),
            $this->getEmbeddedListCode($definition, $model),
            (bool) $definition['autoIncrement'] ? 'true' : 'false',
            $definition['mapReduce'] ? 'Mongo_MapReduceModel' : 'Mongo_Model'
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
            file_put_contents($path . '/'.$model.'Repository.php', str_replace($search, $replace, file_get_contents($dummy . ($definition['mapReduce'] ? '/MapReduceRepository.php' : '/Repository.php'))));
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

    protected function getEmbeddedListCode($definition, $model)
    {
        $return = array();
        foreach ($definition['embedded'] as $relation => $data) {
            if (empty($data[0]) || empty($data[1]) || empty($data[2])) {
                continue;
            }
            $return[] = '\''.$relation.'\' => array(\''.$data[0].'\', \''.$data[1].'\', \''.$data[2].'\''.(!empty($data[3]) ? ', true' : '').')';
        }
        return implode(', ', $return);
    }

    protected function getRelationsMethodsCode($definition, $model)
    {

    $code1 = '
    /**
     *
     * @return {foreignModel}|null
     */
    public function get{relation}()
    {
        return $this->getRelated(\'{relation}\');
    }

    /**
     * @param {foreignModel}|mixed $related either a {foreignModel} object or a {foreignModel}->_id-value
     * @param mixed $save set to null to prevent a save() call, otherwise call save($save)
     * @return bool
     */
    public function set{relation}($related, $save = true)
    {
        return $this->setRelated(\'{relation}\', $related, $save = true);
    }

    /**
     * @param boolean $delete true to delete the related entry from the database, false to only remove the relation (default false) 
     * @param mixed $save set to null to prevent a save() call, otherwise call save($save)
     * @return bool
     */
    public function remove{relation}($delete = false, $save = true)
    {
        return $this->removeRelated(\'{relation}\', true, $delete, $save);
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
        return $this->getRelated(\'{relation}\', $query, $sort, $limit, $skip);
    }

    /**
     * @param {foreignModel}|mixed $related either a {foreignModel} object, a {foreignModel}->_id-value or an array with multiple {foreignModel}s
     * @param mixed $save set to null to prevent a save() call, otherwise call save($save)
     * @param bool $multiple true to store multiple related as array (m:n), false to only store a single value (1:1, n:1, default)
     * @return bool
     */
    public function set{relation}($related, $save = true, $multiple = false)
    {
        return $this->setRelated(\'{relation}\', $related, $save, $multiple);
    }

    /**
     * @param {foreignModel}|mixed $related true to remove all objects or either a {foreignModel} object, a {foreignModel}->_id-value  or an array with multiple {foreignModel}s
     * @param boolean $delete true to delete the related entry from the database, false to only remove the relation (default false) 
     * @param mixed $save set to null to prevent a save() call, otherwise call save($save)
     * @return bool
     */
    public function remove{relation}($related = true, $delete = false, $save = true)
    {
        return $this->removeRelated(\'{relation}\', $related, $delete, $save);
    }
    ';

        $return = array();
        $search = array('{relation}', '{foreignModel}', '{localProperty}', '{foreignProperty}');
        foreach ($definition['relations'] as $relation => $data) {
            $replace = array($relation, $data[0], $data[1], $data[2]);
            $return[] = str_replace($search, $replace, !empty($data[3]) ? $code1 : $code2);
        }
        return implode("\n", $return);
    }

    protected function getEmbeddedMethodsCode($definition, $model)
    {

    $code1 = '
    /**
     *
     * @return {foreignModel}|null
     */
    public function get{relation}()
    {
        return $this->getEmbedded(\'{relation}\');
    }

    /**
     *
     * @param {foreignModel}|array $data a {foreignModel} object or an array representing a {foreignModel}
     * @param mixed $save set to null to prevent a save() call, otherwise call save($save)
     * @return bool
     */
    public function set{relation}($data, $save = true)
    {
        return $this->setEmbedded(\'{relation}\', $data);
    }

    /**
     * removes the relation to {foreignModel}
     *
     * @param mixed $save set to null to prevent a save() call, otherwise call save($save)
     * @return bool
     */
    public function remove{relation}($save = true)
    {
        return $this->removeEmbedded(\'{relation}\', true, $save);
    }
    ';

    $code2 = '
    /**
     *
     * @param int|bool $key the identifier of a embedded or true to return all
     * @param string $sortBy (optional) if $key == true, order the entries by this property, null to keep the db order
     * @param bool $sortDesc false (default) to sort ascending, true to sort descending
     * @return {foreignModel}|array
     */
    public function get{relation}($key = true, $sortBy = null, $sortDesc = false)
    {
        return $this->getEmbedded(\'{relation}\', $key, $sortBy, $sortDesc);
    }

    /**
     *
     * @param {foreignModel}|array $data a {foreignModel} object or an array representing a {foreignModel} or an array with multiple {foreignModel}
     * @param mixed $save set to null to prevent a save() call, otherwise call save($save)
     * @return bool
     */
    public function set{relation}($data, $save = true)
    {
        return $this->setEmbedded(\'{relation}\', $data, $save);
    }

    /**
     * removes the chosen {foreignModel}s (or all for $key = true) from the embedded list
     *
     * @param mixed $key one or more keys for {foreignModel} objects or true to remove all
     * @param mixed $save set to null to prevent a save() call, otherwise call save($save)
     * @return bool
     */
    public function remove{relation}($key = true, $save = true)
    {
        return $this->removeEmbedded(\'{relation}\', $key, $save);
    }
    ';

        $return = array();
        $search = array('{relation}', '{foreignModel}', '{localProperty}', '{foreignProperty}');
        foreach ($definition['embedded'] as $relation => $data) {
            $replace = array($relation, $data[0], $data[1], !empty($data[2]) ? $data[2] : null);
            $return[] = str_replace($search, $replace, !empty($data[3]) ? $code1 : $code2);
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
