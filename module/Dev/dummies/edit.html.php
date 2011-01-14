<h2><?php echo $t->CONTROLLERLCFIRSTEditHeadline(array('title' => htmlspecialchars($model->title))); ?></h2>
<?php echo $h->partial->get('form', array('form' => $form)); ?>