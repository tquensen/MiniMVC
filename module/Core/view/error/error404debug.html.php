<h2><?php echo $t->error404Headline; ?></h2>
<?php if ($message): ?>
<p><?php echo $o->esc($message); ?></p>
<?php endif; ?>
<pre>
<?php echo $e; ?>
</pre>