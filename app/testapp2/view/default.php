<?php
	header('Content-Type: text/html; charset=utf-8');
?><!DOCTYPE html>
<!--[if lt IE 7 ]> <html class="nojs ie ie6 ltie7 ltie8 ltie9">     <![endif]-->
<!--[if IE 7 ]>    <html class="nojs ie ie7 ltie8 ltie9 gtie6">     <![endif]-->
<!--[if IE 8 ]>    <html class="nojs ie ie8 ltie9 gtie6 gtie7">     <![endif]-->
<!--[if IE 9 ]>    <html class="nojs ie ie9 gtie6 gtie7 gtie8">     <![endif]-->
<!--[if !IE]><!--> <html class="nojs noie gtie6 gtie7 gtie8">   <!--<![endif]-->
    <head>
        <script>(function(H){H.className=H.className.replace(/\bnojs\b/,'js')})(document.documentElement)</script>
        <meta charset="UTF-8">
        <?php echo $layout->getSlot('meta') ?>
        <?php echo $helper->CSS->getHtml() ?>
    </head>
    <body>
        <?php echo $layout->getSlot('navigation') ?>

        <?php echo $layout->getSlot('main') ?>
        <?php echo $layout->getSlot('sidebar') ?>

        <?php echo $helper->JS->getHtml() ?>
    </body>
</html>