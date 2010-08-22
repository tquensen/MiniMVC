<?php if (!empty($navi)): ?>
<ul><?php foreach ($navi as $entry): ?>
    <li class="<?php if ($entry['active']) echo 'active' ?><?php if (!empty($entry['submenu'])) echo ' sub' ?>">
        <?php if ($entry['url']):
        ?><a href="<?php echo htmlspecialchars($entry['url'])?>"><?php echo htmlspecialchars($entry['title'])?></a><?php
        else:
        ?><span><?php echo htmlspecialchars($entry['title'])?></span><?php
        endif; ?>
        <?php if (!empty($entry['submenu'])) echo $this->get('navi', array('navi' => $entry['submenu'])); ?>
    </li>
<?php endforeach; ?></ul>
<?php endif; ?>