<?php
$modelDefinition['MongoExampleArticle'] = array(
    'autoIncrement' => true, //recommended, if not set to false, it will automatically generate a MongoId as _id on insert (if _id was empty)
    'columns' => array(
        '_id' => 'MongoId',
        'slug' => 'string',
        'title' => 'string',
        'content' => 'string',
        'comments' => 'array',
        'author_id' => 'MongoId',
        'created_at' => 'MongoDate',
        'updated_at' => 'MongoDate'
    ),
    'relations' => array(
        'Comments' => array('MongoExampleComment', '_id', 'article_id'), //array(ForeignClassName, local_column, foreign_column, [true=foreign is single (for m:1 or 1:1), false/null=foreign is multiple (for 1:m)])
        'Author' => array('MongoExampleAuthor', 'author_id', '_id', true)
    ),
    'embedded' => array(
        'EComments' => array('MongoEmbeddedComment', 'comments') //array(ForeignClassName (use Mongo_Embedded as generic class), [true=foreign is single (for m:1 or 1:1), false/null=foreign is multiple (for 1:m)])
    )
);

$modelDefinition['MongoExampleComment'] = array(
    'autoIncrement' => true,
    'columns' => array(
        '_id' => 'MongoId',
        'article_id' => 'MongoId',
        'author_id' => 'string',
        'content' => 'string',
        'created_at' => 'MongoDate',
        'updated_at' => 'MongoDate'
    ),
    'relations' => array(
        'Article' => array('MongoExampleArticle', 'article_id', '_id', true),
        'Author' => array('MongoExampleAuthor', 'author_id', '_id', true)
    )
);

$modelDefinition['MongoExampleAuthor'] = array(
    'autoIncrement' => true,
    'columns' => array(
        '_id' => 'MongoId',
        'name' => 'string',
        'created_at' => 'MongoDate',
        'updated_at' => 'MongoDate'
    ),
    'relations' => array(
        'Articles' => array('MongoExampleArticle', '_id', 'author_id'),
        'Comments' => array('MongoExampleComment', '_id', 'author_id')
    )
);