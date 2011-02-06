<?php if ($element->helper): ?>
<?php echo $this->get($element->helper, is_array($element->data) ? $element->data : array(), $element->module ? $element->module : null, $element->app ? $element->app : null); ?>
<?php elseif ($element->content): ?>
<?php echo $element->content; ?>
<?php endif; ?>
