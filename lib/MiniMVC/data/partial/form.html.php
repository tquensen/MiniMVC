<form id="<?php echo htmlspecialchars($form->getName())?>" action="<?php echo htmlspecialchars($form->getOption('action')) ?>" method="<?php echo strtoupper($form->getOption('method')) == 'GET' ? 'GET' : 'POST' ?>" class="<?php if (!$form->isValid()): ?>invalid<?php endif; ?><?php if ($form->getOption('class')):?> <?php echo $form->getOption('class') ?><?php endif; ?>" <?php if ($form->getOption('enctype')): ?>enctype="<?php echo htmlspecialchars($form->getOption('enctype')) ?>"<?php endif; ?><?php if ($form->getOption('attributes')): foreach ((array) $form->getOption('attributes') as $attr => $attrValue): ?> <?php echo ' '.$attr.'="'.$attrValue.'"'; ?><?php endforeach; endif; ?>>
        
    <?php echo $this->get('form/errors', array('form' => $form)); ?>
    
    <?php echo $this->get('form/hiddenFields', array('form' => $form)); ?>
        
    <?php echo $this->get('form/wrapper/' . $form->getOption('wrapper'), array('form' => $form)); ?>

</form>