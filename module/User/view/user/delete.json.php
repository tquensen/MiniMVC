<?php

$json = array(
    'success' => $success,
    'message' => $message,
    'url' => $h->url->get('user.userIndex')
);

echo json_encode($json);