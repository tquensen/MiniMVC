<h1><?php echo $t->error404Headline; ?></h1>
<?php if ($message): ?>
<p><?php echo $o->esc($message); ?></p>
<?php endif; ?>
<pre>
<?php echo $e; ?>
</pre>