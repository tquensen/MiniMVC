<!DOCTYPE html>
<!--[if lt IE 7 ]> <html class="nojs ie ie6 ltie7 ltie8 ltie9">     <![endif]-->
<!--[if IE 7 ]>    <html class="nojs ie ie7 ltie8 ltie9 gtie6">     <![endif]-->
<!--[if IE 8 ]>    <html class="nojs ie ie8 ltie9 gtie6 gtie7">     <![endif]-->
<!--[if IE 9 ]>    <html class="nojs ie ie9 gtie6 gtie7 gtie8">     <![endif]-->
<!--[if !IE]><!--> <html class="nojs noie gtie6 gtie7 gtie8">   <!--<![endif]-->
    <head>
        <script>(function(H){H.className=H.className.replace(/\bnojs\b/,'js')})(document.documentElement)</script>
        <meta charset="UTF-8">
        <?php echo $helper->meta->getHtml() ?>
        <?php echo $helper->css->getHtml() ?>
    </head>
    <body>
        <script type="text/javascript">
        window.Meebo||function(b){function p(){return["<",i,' onload="var d=',g,";d.getElementsByTagName('head')[0].",
        j,"(d.",h,"('script')).",k,"='//",b.stage?"stage-":"","cim.meebo.com/cim?iv=",a.v,
        "&",q,"=",b[q],b[l]?"&"+l+"="+b[l]:"",b[e]?"&"+e+"="+b[e]:"","'\"></",i,">"].join("")}
        var f=window,a=f.Meebo=f.Meebo||function(){(a._=a._||[]).push(arguments)},d=document,
        i="body",m=d[i],r;if(!m){r=arguments.callee;return setTimeout(function(){r(b)},
        100)}a.$={0:+new Date};a.T=function(u){a.$[u]=new Date-a.$[0]};a.v=4;var j="appendChild",
        h="createElement",k="src",l="lang",q="network",e="domain",n=d[h]("div"),v=n[j](d[h]("m")),
        c=d[h]("iframe"),g="document",o,s=function(){a.T("load");a("load")};f.addEventListener?
        f.addEventListener("load",s,false):f.attachEvent("onload",s);n.style.display="none";
        m.insertBefore(n,m.firstChild).id="meebo";c.frameBorder="0";c.id="meebo-iframe";
        c.allowTransparency="true";v[j](c);try{c.contentWindow[g].open()}catch(w){b[e]=
        d[e];o="javascript:var d="+g+".open();d.domain='"+d.domain+"';";c[k]=o+"void(0);"}try{var t=
        c.contentWindow[g];t.write(p());t.close()}catch(x){c[k]=o+'d.write("'+p().replace(/"/g,
        '\\"')+'");d.close();'}a.T(1)}({network:"scarax_qa82ju"});
        Meebo("makeEverythingSharable");
        </script>
        <?php echo $helper->navi->getHtml('main') ?>

        <?php echo $layout->getSlot('main') ?>
        SIDEBAR:
        <div>
        <?php echo $layout->getSlot('sidebar') ?>
        </div>
        <?php echo $helper->js->getHtml() ?>
        <script type="text/javascript">
          Meebo("domReady");
        </script>
    </body>
</html>