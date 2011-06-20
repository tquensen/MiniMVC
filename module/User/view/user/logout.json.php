<?php

$json = array(
    'success' => true,
    'message' => $t->userLogoutMessage
);
echo json_encode($json);