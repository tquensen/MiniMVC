<h2><?php echo $t->CONTROLLERLCFIRSTCreateHeadline; ?></h2>
<?php if ($success): ?>
    <p>
        <a href="<?php echo $h->url->get('MODLC.CONTROLLERLCFIRSTShow', array('slug' => $model->slug)); ?>">
            <?php echo $o->esc($message); ?>
        </a>
    </p>
<?php else:
    echo $h->partial->get('form', array('form' => $form));
endif;