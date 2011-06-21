<?php if ($success): ?>
    <h1><?php echo $t->userLoginHeadline; ?></h1>
    <p>
        <a href="<?php echo $h->url->get('user.userShow', array('slug' => $model->slug)); ?>">
            <?php echo $o->esc($message); ?>
        </a>
    </p>
<?php else:
    echo $this->setFile('user/login')->parse();
endif;