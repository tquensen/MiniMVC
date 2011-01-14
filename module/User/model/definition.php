<?php
$modelDefinition['User'] = array(
    'identifier' => 'id',
    'autoIncrement' => true,
    'columns' => array(
        'id' => 'integer',
        'slug' => 'string',
        'name' => 'string',
        'email' => 'string',
        'password' => 'string',
        'salt' => 'string',
        'auth_token' => 'string',
        'role' => 'string',
        'created_at' => 'integer',
        'updated_at' => 'integer'
    ),
    'relations' => array(
        //'RelationName' => array('AnotherModel', 'id', 'user_id') //array(ForeignClassName, local_column, foreign_column, [true=foreign is single (for m:1 or 1:1), string = name of ref table (for m:n), leave blank=foreign is multiple (for 1:m)])
    )
);