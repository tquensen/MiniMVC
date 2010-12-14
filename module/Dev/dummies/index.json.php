<?php
/* LIST VIEW
$json = array(
    'status' => true,
    'currentPage' => $pager->getPage(),
    'numPages' => $pager->getPages(),
    'numEntries' => $pager->getNumEntries(),
    'currentEntries' => $pager->getPageEntries(),
    'entries' => array()
);
if (count($entries)) {
    foreach ($entries as $entry) {
        //$json['entries'][] = $entry->toArray(); //return the complete entry

        //... or just specific and/or additional properties
        $json['entries'][] = array(
            'id' => $entry->id,
            'title' => $entry->title,
            'url' => $h->url->get('MODLC.CONTROLLERLCFIRSTShow', array('id' => $entry->id))
        );
    }
}

echo json_encode($json);
 */