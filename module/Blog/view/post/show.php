<h2><?php $o->esc($model->title); ?></h2>
<?php $o->esc($model->text); ?>

<p>
    Tags: (<?php echo count($model->getTags()); ?>)
    <?php foreach ($model->getTags() as $tag): ?>
    <span><?php $o->esc($tag->title); ?></span>
    <?php endforeach; ?>
</p>

<p>
    Kommentare: (<?php echo count($model->getComments()); ?>)
</p>
<?php foreach ($model->getComments() as $comment): ?>
<p><?php $o->esc($comment->message); ?></p>
<?php endforeach; ?>
