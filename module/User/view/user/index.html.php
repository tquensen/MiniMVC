<h2><?php echo $t->userIndexHeadline; ?></h2>

<?php if (count($entries)): ?>
<ol>
    <?php foreach ($entries as $model): ?>
    <li>
        <h2>
            <a href="<?php $o->esc($h->url->get('user.userShow', array('slug' => $model->slug))); ?>">
                <?php $o->esc($model->name); ?>
            </a>
        </h2>
    </li>
    <?php endforeach; ?>
</ol>
<?php echo $pager->getHtml(); ?>
<?php else: ?>
    <p><?php echo $t->userNoEntries; ?></p>
<?php endif; ?>

<?php if ($h->url->userCanCall('user.userLogin')): ?>
    <p><?php echo $h->url->link($t->userLoginLink, 'user.userLogin'); ?></p>
<?php endif; ?>
<?php if ($h->url->userCanCall('user.userNew')): ?>
    <p><?php echo $h->url->link($t->userNewLink, 'user.userNew'); ?></p>
<?php endif; ?>
