<?php

if ($success) {
    $json = array(
        'success' => true,
        'message' => $message,
        'url' => $h->url->get(''),
        'id' => $model->id,
        'name' => $model->name
    );
} else {
    $json = array_merge(array('success' => $form->isValid()), $form->toArray());
}


echo json_encode($json);