<h2>Edit</h2>
<pre><?php var_dump(Doctrine_Core::getTable('User')->findOneById($this->registry->guard->getId())->toArray()); ?></pre>