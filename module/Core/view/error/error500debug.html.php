<h1><?php echo $headline; ?></h1>
<?php if ($message): ?>
<p><?php echo $o->esc($message); ?></p>
<?php endif; ?>
<pre>
<?php echo $e; ?>
</pre>