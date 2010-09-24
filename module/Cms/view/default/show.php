<h2><?php $o->esc($article->title); ?></h2>
<?php if ($article->status == 'draft'): ?><p class="notice message">This is a draft!</p><?php endif; ?>