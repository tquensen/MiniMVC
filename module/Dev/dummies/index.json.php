<?php

$json = array(
    'success' => true,
    'currentPage' => $pager->getPage(),
    'currentEntries' => $pager->getPageEntries(),
    'numPages' => $pager->getPages(),
    'numEntries' => $pager->getNumEntries(),
    'entries' => array()
);
if (count($entries)) {
    foreach ($entries as $model) {
        //$json['entries'][] = $model->toArray(); //return the complete entry

        //... or just specific and/or additional properties
        $json['entries'][] = array(
            'id' => $model->id,
            'title' => $model->title,
            'url' => $h->url->get('MODLC.CONTROLLERLCFIRSTShow', array('slug' => $model->slug))
        );
    }
}

echo json_encode($json);