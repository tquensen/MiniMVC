<?php

$json = array(
    'success' => $success,
    'message' => $message,
    'url' => $h->url->get('MODLC.CONTROLLERLCFIRSTIndex')
);

echo json_encode($json);