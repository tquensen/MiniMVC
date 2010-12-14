<?php

//return the complete object
/*
$json = array_merge(
    array(
        'status' => true
    ),
    $model->toArray() //be careful if the model has also a 'status' property
);
*/

//.. or only specific properties of the current object
$json = array(
    'status' => true,
    'id' => $model->id,
    'title' => $model->title
);

echo json_encode($json);