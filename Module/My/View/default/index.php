HI<br />
id: <?php echo htmlspecialchars($this->registry->guard->id) ?><br />
role: <?php echo htmlspecialchars($this->registry->guard->role) ?><br />
rights: <?php echo htmlspecialchars($this->registry->guard->rights) ?><br />
name: <?php echo htmlspecialchars($this->registry->guard->name) ?><br />
email: <?php echo htmlspecialchars($this->registry->guard->email) ?><br />
slug: <?php echo htmlspecialchars($this->registry->guard->slug) ?><br />
<?php echo $pager->getHtml(); ?>
<?php
/*
<?php echo var_dump($params)?>
<?php echo $this->registry->guard->email.'/'.$this->registry->guard->name; ?>
<?php $urls = new Helper_Url()?>
<p><a href="<?php echo $urls->get('baum', array('foo'=>'baZ'))?>">SELF</a></p>
<p>Fisch: <?php echo $fisch['name'] . ' / ' . $fisch['DevFischArt']['name']; ?></p>
*/
?>