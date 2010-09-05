<!DOCTYPE html>
<html>
    <head class="nojs">
        <script>(function(H){H.className=H.className.replace(/\bnojs\b/,'js')})(document.documentElement)</script>
        <meta charset="UTF-8">
        <?php echo $helper->meta->getHtml() ?>
        <?php echo $helper->css->getHtml() ?>
    </head>
    <!--[if lt IE 7 ]> <body class="ie ie6 ltie7 ltie8 ltie9">     <![endif]-->
    <!--[if IE 7 ]>    <body class="ie ie7 ltie8 ltie9 gtie6">     <![endif]-->
    <!--[if IE 8 ]>    <body class="ie ie8 ltie9 gtie6 gtie7">     <![endif]-->
    <!--[if IE 9 ]>    <body class="ie ie9 gtie6 gtie7 gtie8">     <![endif]-->
    <!--[if !IE]><!--> <body class="noie gtie6 gtie7 gtie8">   <!--<![endif]-->
        <h1>MiniMVC Test</h1>
        <?php echo $helper->navi->getHtml('main') ?>

        <?php echo $layout->getSlot('main') ?>
        SIDEBAR:
        <div>
        <?php echo $layout->getSlot('sidebar') ?>
        </div>
        <?php echo $helper->js->getHtml() ?>
    </body>
</html>