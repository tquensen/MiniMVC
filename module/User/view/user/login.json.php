<?php

$json = array_merge(array('success' => true), $form->toArray());

echo json_encode($json);