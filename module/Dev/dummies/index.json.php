<?php
/* LIST VIEW
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
            'url' => $h->url->get('MODLC.CONTROLLERLCFIRSTShow', array('id' => $model->id))
        );
    }
}

echo json_encode($json);
 */