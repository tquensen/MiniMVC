<?php echo var_dump($params)?>
<?php echo $this->registry->guard->email.'/'.$this->registry->guard->name; ?>
<?php $urls = new Helper_Url()?>
<p><a href="<?php echo $urls->get('baum', array('foo'=>'baZ'))?>">SELF</a></p>
<p>Fisch: <?php echo $fisch['name'] . ' / ' . $fisch['DevFischArt']['name']; ?></p>