<?php

$json = array(
    'success' => $success,
    'url' => $h->url->get('MODLC.CONTROLLERLCFIRSTIndex')
);

echo json_encode($json);