<?php

$json = array(
    'success' => true,
    'currentPage' => $pager->getPage(),
    'numPages' => $pager->getPages(),
    'numEntries' => $pager->getNumEntries(),
    'currentEntries' => $pager->getPageEntries(),
    'entries' => array()
);
if (count($entries)) {
    foreach ($entries as $model) {
        //$json['entries'][] = $model->toArray(); //return the complete entry

        //... or just specific and/or additional properties
        $json['entries'][] = array(
            'id' => $model->id,
            'title' => $model->title,
            'url' => $h->url->get('user.userShow', array('slug' => $model->slug))
        );
    }
}

echo json_encode($json);