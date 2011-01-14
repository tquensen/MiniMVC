<?php if ($success): ?>
    <h2><?php echo $t->CONTROLLERLCFIRSTEditHeadline(array('title' => htmlspecialchars($model->title))); ?></h2>
    <p>
        <a href="<?php echo $h->url->get('MODLC.CONTROLLERLCFIRSTShow', array('slug' => $model->slug)); ?>">
            <?php echo $o->esc($message); ?>
        </a>
    </p>
<?php else:
    echo $this->setFile('CONTROLLERLC/edit')->parse();
endif;