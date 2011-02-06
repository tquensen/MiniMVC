<h2><?php echo $t->CONTROLLERLCFIRSTIndexHeadline; ?></h2>

<?php if (count($entries)): ?>
<ol>
    <?php foreach ($entries as $model): ?>
    <li>
        <h3>
            <a href="<?php $o->esc($h->url->get('MODLC.CONTROLLERLCFIRSTShow', array('slug' => $model->slug))); ?>">
                <?php $o->esc($model->title); ?>
            </a>
        </h3>
    </li>
    <?php endforeach; ?>
</ol>
<?php echo $pager->getHtml(); ?>
<?php else: ?>
    <p><?php echo $t->CONTROLLERLCFIRSTNoEntries; ?></p>
<?php endif; ?>

<?php /* CREATE LINK
<?php if ($h->url->userCanCall('MODLC.CONTROLLERLCFIRSTCreate')): ?>
    <p><?php echo $h->url->link($t->CONTROLLERLCFIRSTCreateLink, 'MODLC.CONTROLLERLCFIRSTCreate'); ?></p>
<?php endif; ?>
 */ ?>
