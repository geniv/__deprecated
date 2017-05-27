<?php


/**
 * Class GoogleTagManager
 * pro Google Tag Manager
 *
 * @author  geniv
 * @package NetteWeb
 */
class GoogleTagManager extends Nette\Application\UI\Control
{

    //TODO dopsat!!!
    public function renderHead() { }


    public function renderBody() { }


    /**
     * defaultni render
     */
    public function render()
    {
        // nacteni ga z konfigurace
        $gtm = (isset($this->parent->context->parameters['gtm']) ? $this->parent->context->parameters['gtm'] : null);

        if ($gtm && \Tracy\Debugger::$productionMode) {
            echo <<<GA
        <!-- Google Tag Manager -->
        <noscript><iframe src="//www.googletagmanager.com/ns.html?id={$gtm}"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        '//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','{$gtm}');</script>
        <!-- End Google Tag Manager -->

GA;
        } else {
            echo <<<GA
        <!-- Google Tag Manager -->

GA;
        }
    }
}

/*
Dle domluvy s Radkem zasílám nové požadavky na umisťování kódů. Oproti předchozímu, je kód rozdělen na 2 části a nově se umisťuje na co nejvyšší možnou pozici ve značce <head> a navíc i těsně za úvodní značku <body>:

Přílad viz níže:
Vložte tento kód na co nejvyšší možnou pozici ve značce <head> na této stránce:

<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-WLPL3F');</script>
<!-- End Google Tag Manager -->
Navíc vložte kód i těsně za úvodní značku <body>:

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WLPL3F"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
Další informace o instalaci fragmentu kódu Správce značek Google naleznete v Úvodní příručce.
*/
