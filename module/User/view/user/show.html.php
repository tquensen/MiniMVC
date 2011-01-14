<h2><?php echo $t->UserShowHeadline(array('name' => htmlspecialchars($model->name))); ?></h2>



<?php if ($model->id == $this->registry->guard->getId()): ?>
    <p><?php echo $h->url->link($t->userEditLink, 'user.userEdit', array('slug' => $model->slug)); ?></p>
    <p><?php echo $h->url->link($t->userDeleteLink, 'user.userDelete', array('slug' => $model->slug), null, '', $t->userDeleteConfirmMessage); ?></p>
<?php endif; ?>