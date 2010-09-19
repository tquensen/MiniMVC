<form id="<?php echo htmlspecialchars($form->getName())?>" action="<?php echo htmlspecialchars($form->getOption('action')) ?>" method="<?php echo strtoupper($form->getOption('method')) == 'GET' ? 'GET' : 'POST' ?>" <?php if ($form->getOption('enctype')): ?>enctype="<?php echo htmlspecialchars($form->getOption('enctype')) ?>"<?php endif; ?>>
    <ol>
    <?php if (strtoupper($form->getOption('method')) != 'GET' && strtoupper($form->getOption('method')) != 'POST'): ?>
        <li id="<?php echo htmlspecialchars($form->getName())?>__REQUEST_METHOD__wrapper" class="formHidden">
            <input type="hidden" name="REQUEST_METHOD" id="<?php echo htmlspecialchars($form->getName() . '__REQUEST_METHOD') ?>" value="<?php echo htmlspecialchars(strtoupper($form->getOption('method'))) ?>" />
        </li>
    <?php endif; ?>
    <?php foreach ($form->getElements() as $currentElement): ?>
        <?php if ($currentElement->getType() != 'fieldsetend'): ?>
        <li id="<?php echo htmlspecialchars($form->getName())?>__<?php echo htmlspecialchars($currentElement->getName())?>__wrapper" class="form<?php echo ucfirst($currentElement->getType())?>Wrapper<?php if (!$currentElement->isValid()):?> invalid<?php elseif($form->wasSubmitted): ?> valid<?php endif; ?><?php if ($currentElement->class): ?> <?php echo $currentElement->class; ?><?php endif; ?>">
        <?php endif; ?>
    <?php echo $this->get('form/' . $currentElement->getType(), array('element' => $currentElement)); ?>
    <?php if ($currentElement->info):?><span class="formInfo"><?php echo htmlspecialchars($currentElement->info)?></span><?php endif; ?>
    <?php if (!$currentElement->isValid()):?><span class="formError"><?php echo htmlspecialchars($currentElement->errorMessage)?></span><?php endif; ?>
        <?php if ($currentElement->getType() != 'fieldset'): ?>
        </li>
        <?php endif; ?>
    <?php endforeach; ?>
    </ol>
</form>