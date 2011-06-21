<?php
	header('Content-Type: text/html; charset=utf-8');
?><!DOCTYPE html>
<html class="nojs">
    <head>
        <script>(function(H){H.className=H.className.replace(/\bnojs\b/,'js')})(document.documentElement)</script>
        <meta charset="UTF-8">
        <?php echo $h->meta->getHtml() ?>
        <?php echo $h->css->getHtml() ?>
        <?php /*
        <!--[if lt IE 9]>
        <script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
        <![endif]-->
         */ ?>
    </head>
    <!--[if lt IE 7 ]> <body class="ie ie6 ltie7 ltie8 ltie9">     <![endif]-->
    <!--[if IE 7 ]>    <body class="ie ie7 ltie8 ltie9 gtie6">     <![endif]-->
    <!--[if IE 8 ]>    <body class="ie ie8 ltie9 gtie6 gtie7">     <![endif]-->
    <!--[if IE 9 ]>    <body class="ie ie9 gtie6 gtie7 gtie8">     <![endif]-->
    <!--[if !IE]><!--> <body class="noie gtie6 gtie7 gtie8">   <!--<![endif]-->
        <header role="banner">
            <a href="<?php echo $h->url->get(''); ?>"><h1><?php echo $t->pageTitle; ?></h1></a>
            <nav>
                <?php echo $h->navi->getHtml('main') ?>
            </nav>
            <?php echo $h->messages->getHtml() ?>
        </header>
        <section role="main">
            <?php echo $layout->getSlot('main') ?>
        </section>
        <aside>
            <?php /* put each sidebar widget in a seperate section */ ?>
            <section><?php echo $layout->getSlot('sidebar', array(), '</section><section>') ?></section>
        </aside> 
        <footer role="contentinfo">
            <?php /* secondary navigation, copyright notice, contact information... */ ?>
        </footer>       
        <?php echo $h->js->getHtml() ?>
    </body>
</html>