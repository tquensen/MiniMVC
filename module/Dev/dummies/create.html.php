<?php if ($success): ?>
    <h2><?php echo $t->CONTROLLERLCFIRSTNewHeadline; ?></h2>
    <p>
        <a href="<?php echo $h->url->get('MODLC.CONTROLLERLCFIRSTShow', array('slug' => $model->slug)); ?>">
            <?php echo $o->esc($message); ?>
        </a>
    </p>
<?php else:
    echo $this->setFile('CONTROLLERLC/new')->parse();
endif;