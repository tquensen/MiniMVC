<form id="<?php echo htmlspecialchars($form->getName())?>" action="<?php echo htmlspecialchars($form->getOption('action')) ?>" method="<?php echo strtoupper($form->getOption('method')) == 'GET' ? 'GET' : 'POST' ?>" class="<?php if (!$form->isValid()): ?>invalid<?php endif; ?><?php if ($form->getOption('class')):?> <?php echo $form->getOption('class') ?><?php endif; ?>" <?php if ($form->getOption('enctype')): ?>enctype="<?php echo htmlspecialchars($form->getOption('enctype')) ?>"<?php endif; ?><?php if ($form->getOption('attributes')): foreach ((array) $form->getOption('attributes') as $attr => $attrValue): ?> <?php echo ' '.$attr.'="'.$attrValue.'"'; ?><?php endforeach; endif; ?>>
    
    <?php if (!$form->isValid() && $form->getOption('showGlobalErrors') && $form->hasErrors()): ?>
        <?php echo $this->get('form/errors', array('form' => $form)); ?>
    <?php endif; ?>
    <?php if (strtoupper($form->getOption('method')) != 'GET' && strtoupper($form->getOption('method')) != 'POST'): ?>
        <div id="<?php echo htmlspecialchars($form->getName())?>__REQUEST_METHOD__wrapper" class="formHiddenWrapper">
            <input type="hidden" name="REQUEST_METHOD" id="<?php echo htmlspecialchars($form->getName() . '__REQUEST_METHOD') ?>" value="<?php echo htmlspecialchars(strtoupper($form->getOption('method'))) ?>" />
        </div>
    <?php endif; ?>
    <?php if ($authToken = $form->getAuthToken()): ?>
        <div id="<?php echo htmlspecialchars($form->getName())?>___auth_token__wrapper" class="formHiddenWrapper">
            <input type="hidden" name="auth_token" id="<?php echo htmlspecialchars($form->getName() . '___auth_token') ?>" value="<?php echo htmlspecialchars($authToken) ?>" />
        </div>
    <?php endif; ?>
    <?php echo $this->get('form/wrapper/' . $form->getOption('wrapper'), array('form' => $form)); ?>

</form>