<?php

if ($success) {
    $json = array(
        'success' => true,
        'message' => $message,
        'url' => $h->url->get('user.userShow', array('slug' => $model->slug)),
        'id' => $model->id,
        'name' => $model->name
    );
} else {
    $json = array_merge(array('success' => false), $form->toArray());
}


echo json_encode($json);