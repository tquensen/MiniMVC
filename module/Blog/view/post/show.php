<h2><?php echo htmlspecialchars($model->title); ?></h2>
<?php echo htmlspecialchars($model->text); ?>

<p>
    Tags: (<?php echo count($model->getTags()); ?>)
    <?php foreach ($model->getTags() as $tag): ?>
    <span><?php echo $tag->title; ?></span>
    <?php endforeach; ?>
</p>

<p>
    Kommentare: (<?php echo count($model->getComments()); ?>)
</p>
<?php foreach ($model->getComments() as $comment): ?>
<p><?php echo $comment->message; ?></p>
<?php endforeach; ?>
