<?php

if ($success) {
    $json = array(
        'success' => true,
        'message' => $message,
        'url' => $h->url->get('MODLC.CONTROLLERLCFIRSTShow', array('slug' => $model->slug)),
        'id' => $model->id,
        'title' => $model->title
    );
} else {
    $json = array_merge(array('success' => false), $form->toArray());
}


echo json_encode($json);