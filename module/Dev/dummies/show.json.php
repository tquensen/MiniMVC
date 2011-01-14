<?php

//return the complete object (be carefull with passwords or other secret data!)
/*
$json = array_merge(
    array(
        'success' => true,
        'url' => $h->url->get('MODLC.CONTROLLERLCFIRSTShow', array('slug' => $model->slug))
    ),
    $model->toArray() //be careful if the model has also a 'success' or 'url' property
);
*/

//.. or only specific properties of the current object
$json = array(
    'success' => true,
    'url' => $h->url->get('MODLC.CONTROLLERLCFIRSTShow', array('slug' => $model->slug)),
    'id' => $model->id,
    'title' => $model->title
);

echo json_encode($json);