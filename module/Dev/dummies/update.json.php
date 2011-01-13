<?php

$json = array_merge(array('success' => $success), $form->toArray());

if ($success) {
    $json['model'] = array(
        'url' => $h->url->get('MODLC.CONTROLLERLCFIRSTShow', array('id' => $model->id)),
        'id' => $model->id,
        'title' => $model->title
    );
}


echo json_encode($json);