<?php if (!empty($navi)): ?>
<ul><?php foreach ($navi as $entry): ?>
    <li class="<?php if ($entry['active']) echo 'active' ?><?php if (!empty($entry['submenu'])) echo ' sub' ?><?php if (!empty($entry['data']['class'])) echo ' '.$entry['data']['class'] ?>">
        <?php if ($entry['url']):
        ?><a href="<?php echo htmlspecialchars($entry['url'])?>"><?php echo htmlspecialchars($entry['title'])?></a><?php
        else:
        ?><span><?php echo htmlspecialchars($entry['title'])?></span><?php
        endif; ?>
        <?php if (!empty($entry['submenu'])) echo $this->get($_partial, array('navi' => $entry['submenu']), $_module, $_app); ?>
    </li>
<?php endforeach; ?></ul>
<?php endif; ?>