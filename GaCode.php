<?php

/**
 * Class GaCode
 * pro Google Analytics
 *
 * @author  geniv
 * @package NetteWeb
 */
class GaCode extends Nette\Application\UI\Control
{

    /**
     * defaultni render
     */
    public function render()
    {
        // nacteni ga z konfigurace
        $ga = (isset($this->parent->context->parameters['ga']) ? $this->parent->context->parameters['ga'] : null);

        if ($ga && \Tracy\Debugger::$productionMode) {
            echo <<<GA
        <script type='text/javascript'>
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

            ga('create', '{$ga}', 'auto');
            ga('require', 'displayfeatures');
            ga('send', 'pageview');
        </script>

GA;
        } else {
            echo <<<GA
        <!-- Google Analytics: {$ga} -->

GA;
        }
    }
}
