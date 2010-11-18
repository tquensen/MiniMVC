<form id="<?php echo htmlspecialchars($form->getName())?>" action="<?php echo htmlspecialchars($form->getOption('action')) ?>" method="<?php echo strtoupper($form->getOption('method')) == 'GET' ? 'GET' : 'POST' ?>" class="<?php if (!$form->isValid()): ?>invalid<?php endif; ?><?php if ($form->getOption('class')):?> <?php echo $form->getOption('class') ?><?php endif; ?>" <?php if ($form->getOption('enctype')): ?>enctype="<?php echo htmlspecialchars($form->getOption('enctype')) ?>"<?php endif; ?><?php if ($form->getOption('attributes')): foreach ((array) $form->getOption('attributes') as $attr => $attrValue): ?> <?php echo ' '.$attr.'="'.$attrValue.'"'; ?><?php endforeach; endif; ?>>
    
    <?php if (!$form->isValid() && $form->getOption('showGlobalErrors') && $form->hasErrors()): ?>
        <?php echo $this->get('form/errors', array('form' => $form)); ?>
    <?php endif; ?>
    <?php if (strtoupper($form->getOption('method')) != 'GET' && strtoupper($form->getOption('method')) != 'POST'): ?>
        <div id="<?php echo htmlspecialchars($form->getName())?>__REQUEST_METHOD__wrapper" class="formHiddenWrapper">
            <input type="hidden" name="REQUEST_METHOD" id="<?php echo htmlspecialchars($form->getName() . '__REQUEST_METHOD') ?>" value="<?php echo htmlspecialchars(strtoupper($form->getOption('method'))) ?>" />
        </div>
    <?php endif; ?>
    <?php if ($form->getOption('csrfProtection')): ?>
        <div id="<?php echo htmlspecialchars($form->getName())?>___csrf_token__wrapper" class="formHiddenWrapper">
            <input type="hidden" name="_csrf_token" id="<?php echo htmlspecialchars($form->getName() . '___csrf_token') ?>" value="<?php echo htmlspecialchars($form->getCsrfToken()) ?>" />
        </div>
    <?php endif; ?>
    <?php foreach ($form->getElements() as $currentElement): ?>
        <?php if (!in_array($currentElement->getType(), array('fieldset', 'fieldsetend', 'custom'))): ?>
        <div id="<?php echo htmlspecialchars($form->getName())?>__<?php echo htmlspecialchars($currentElement->getName())?>__wrapper" class="form<?php echo ucfirst($currentElement->getType())?>Wrapper<?php if (!$currentElement->isValid()):?> invalid<?php elseif($form->wasSubmitted()): ?> valid<?php endif; ?><?php if ($currentElement->class): ?> <?php echo $currentElement->class; ?><?php endif; ?>" <?php if ($currentElement->attributes): foreach ((array) $currentElement->attributes as $attr => $attrValue): ?> <?php echo ' '.$attr.'="'.$attrValue.'"'; ?><?php endforeach; endif; ?>>
        <?php endif; ?>
    <?php echo $this->get('form/' . $currentElement->getType(), array('element' => $currentElement)); ?>
    <?php if ($currentElement->info):?><span class="formInfo"><?php echo htmlspecialchars($currentElement->info)?></span><?php endif; ?>
    <?php if (!$currentElement->isValid() && !$currentElement->globalErrors):?><span class="formError"><?php echo htmlspecialchars($currentElement->errorMessage)?></span><?php endif; ?>
        <?php if (!in_array($currentElement->getType(), array('fieldset', 'fieldsetend', 'custom'))): ?>
        </div>
        <?php endif; ?>
    <?php endforeach; ?>

</form>