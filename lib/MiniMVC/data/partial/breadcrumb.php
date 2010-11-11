<?php if ($current = array_shift($navi)): ?>
<ul><li class="<?php if ($entry['active']) echo 'active' ?>">
        <?php if ($entry['url']):
        ?><a href="<?php echo htmlspecialchars($entry['url'])?>"><?php echo htmlspecialchars($entry['title'])?></a><?php
        else:
        ?><span><?php echo htmlspecialchars($entry['title'])?></span><?php
        endif; ?>
        <?php $this->get($_partial, array('navi' => $navi), $_module, $_app); ?></li></ul>
<?php endif; ?>