<ul class="languages">
    <?php foreach ($languages as $language): ?>
    <li class="language_<?php $o->esc($language['key']); ?><?php if ($language['key'] == $currentLanguage): ?> active<?php endif; ?>">
        <a href="<?php $o->esc($language['url']); ?>"><?php $o->esc($language['title']); ?></a>
    </li>
    <?php endforeach; ?>
</ul>