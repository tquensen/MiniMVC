<?php
$modelDefinition['CONTROLLER'] = array(
    'identifier' => 'id',
    'autoIncrement' => true,
    'columns' => array(
        'id' => 'integer',
        'slug' => 'string',
        'title' => 'string'
    ),
    'relations' => array(
        'RelationName' => array('AnotherModel', 'id', 'CONTROLLERTABLE_id') //array(ForeignClassName, local_column, foreign_column, [true=foreign is single (for m:1 or 1:1), string = name of ref table (for m:n), leave blank=foreign is multiple (for 1:m)])
    )
);