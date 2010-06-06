<form action="<?php echo htmlspecialchars($form->getOption('action')) ?>" method="<?php echo htmlspecialchars($form->getOption('method')) ?>" <?php if ($form->getOption('enctype')): ?>enctype="<?php echo htmlspecialchars($form->getOption('enctype')) ?>"<?php endif; ?>>
    <ol>
    <?php foreach ($form->getElements() as $currentElement): ?>
        <li class="form<?php echo ucfirst($currentElement->getType())?><?php if (!$currentElement->isValid()):?> hasError<?php endif; ?>">
    <?php echo $this->get('form/' . $currentElement->getType(), array('element' => $currentElement)); ?>
    <?php if (!$currentElement->isValid()):?><span class="formError"><?php echo htmlspecialchars($currentElement->errorMessage)?></span><?php endif; ?>
        </li>
    <?php endforeach; ?>
    </ol>
</form>