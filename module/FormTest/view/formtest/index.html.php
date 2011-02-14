<h2><?php echo $t->formTestHeadline; ?></h2>
<?php if ($success): ?>
    <p>
        <a href="<?php echo $h->url->get('formtest.index'); ?>">
            <?php echo $o->esc($message); ?>
        </a>
    </p>
<?php else:
    echo $h->partial->get('form', array('form' => $form));
endif;