<?php if ($pages > 1): ?>
	<ol class="pager"><?php
    foreach ($links as $link):
        ?><li class="<?php echo $link['type']; ?><?php if ($link['type'] == 'number' && !$link['isLink']): ?> active<?php elseif (!$link['isLink'] && !$link['active']): ?> inactive<?php endif; ?>"><?php
        if ($link['isLink']):
            ?><a href="<?php echo $link['link']?>"><?php echo $link['label']?></a><?php
        else:
            ?><span><?php echo $link['label']?></span><?php
        endif;
        ?></li><?php
    endforeach;
    ?></ol>
<?php endif; ?>