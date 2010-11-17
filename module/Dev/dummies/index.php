<h2>CONTROLLER</h2>
<?php /* LIST VIEW
<?php if (count($entries)): ?>
<ol>
    <?php foreach ($entries as $entry): ?>
    <li>
        <h3>
            <a href="<?php $o->esc($h->url->get('MODLC.CONTROLLERLCFIRSTShow', array('id' => $entry->id))); ?>">
                <?php $o->esc($entry->title); ?>
            </a>
        </h3>
    </li>
    <?php endforeach; ?>
</ol>
<?php echo $pager->getHtml(); ?>
<?php else: ?>
    <p><?php echo $t->noEntries; ?></p>
<?php endif; ?>
 */ ?>

<?php /* CREATE LINK
<?php if ($h->url->userCanCall('MODLC.CONTROLLERLCFIRSTCreate')): ?>
    <p><?php echo $h->url->link($t->CONTROLLERLCFIRSTCreateLink, 'MODLC.CONTROLLERLCFIRSTCreate'); ?></p>
<?php endif; ?>
 */ ?>
