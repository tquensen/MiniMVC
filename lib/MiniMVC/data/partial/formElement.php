<?php if (empty($wrapper)) { $wrapper = 'div'; } ?>
<<?php echo $wrapper; ?> id="<?php echo htmlspecialchars($form->getName())?>__<?php echo htmlspecialchars($element->getName())?>__wrapper" class="form<?php echo ucfirst($element->getType())?>Wrapper<?php if (!$element->isValid()):?> invalid<?php elseif($form->wasSubmitted()): ?> valid<?php endif; ?><?php if ($element->wrapperClass): ?> <?php echo $element->wrapperClass; ?><?php endif; ?><?php if ($element->required): ?> <?php echo 'required'; ?><?php endif; ?>" <?php if ($element->wrapperAttributes): foreach ((array) $element->wrapperAttributes as $attr => $attrValue): ?> <?php echo ' '.$attr.'="'.$attrValue.'"'; ?><?php endforeach; endif; ?>>


<?php echo $this->get('form/' . $element->getType(), array('element' => $element)); ?>
<?php if ($element->info):?><span class="formInfo"><?php echo htmlspecialchars($element->info)?></span><?php endif; ?>
<?php if (!$element->isValid() && !$element->globalErrors):?><span class="formError"><?php echo htmlspecialchars($element->errorMessage)?></span><?php endif; ?>

</<?php echo $wrapper; ?>>