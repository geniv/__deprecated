<?php declare(strict_types=1);

namespace Translator\Bridges\Tracy;

use Latte\Engine;
use Locale\ILocale;
use Nette\Application\Application;
use Nette\Localization\ITranslator;
use Latte\MacroTokens;
use Latte\Parser;
use Latte\PhpWriter;
use Nette\SmartObject;
use Tracy\Debugger;
use Tracy\IBarPanel;


/**
 * Class Panel
 *
 * @author  geniv
 * @package Translator\Bridges\Tracy
 */
class Panel implements IBarPanel
{
    use SmartObject;

    /** @var ITranslator */
    private $translator;
    /** @var ILocale */
    private $locale;
    /** @var Application */
    private $application;


    /**
     * Panel constructor.
     *
     * @param ILocale     $locale
     * @param Application $application
     */
    public function __construct(ILocale $locale, Application $application)
    {
        $this->locale = $locale;
        $this->application = $application;
    }


    /**
     * Register to Tracy.
     *
     * @param ITranslator $translator
     */
    public function register(ITranslator $translator)
    {
        $this->translator = $translator;
        Debugger::getBar()->addPanel($this);
    }


    /**
     * Renders HTML code for custom tab.
     *
     * @return string
     */
    public function getTab(): string
    {
        return '<span title="Translator">
<?xml version="1.0" ?><svg height="16" viewBox="0 0 48 48" width="16" xmlns="http://www.w3.org/2000/svg"><path d="M0 0h48v48h-48z" fill="none"/><path d="M25.74 30.15l-5.08-5.02.06-.06c3.48-3.88 5.96-8.34 7.42-13.06h5.86v-4.01h-14v-4h-4v4h-14v3.98h22.34c-1.35 3.86-3.46 7.52-6.34 10.72-1.86-2.07-3.4-4.32-4.62-6.7h-4c1.46 3.26 3.46 6.34 5.96 9.12l-10.17 10.05 2.83 2.83 10-10 6.22 6.22 1.52-4.07zm11.26-10.15h-4l-9 24h4l2.25-6h9.5l2.25 6h4l-9-24zm-5.25 14l3.25-8.67 3.25 8.67h-6.5z"/></svg>' .
            'Translator' .
            '</span>';
    }


    /**
     * Renders HTML code for custom panel.
     *
     * @return string
     * @throws \Latte\CompileException
     */
    public function getPanel(): string
    {
        $presenter = $this->application->getPresenter();  // load presenter

        $translateMap = new TranslateMap;
        // load translate from @layout
        $layoutFileTranslate = false;
        if ($presenter->template->getFile()) {
            $layoutFileTranslate = dirname($presenter->template->getFile()) . '/../@layout.latte';
        }
        $layoutTranslate = ($layoutFileTranslate && file_exists($layoutFileTranslate) ? $this->extractFile($layoutFileTranslate, $translateMap) : []);
        // load translate from current file
        $contentTranslate = ($presenter->template->getFile() ? $this->extractFile($presenter->template->getFile(), $translateMap) : []);

        $params = [
            // locales
            'locales'          => $this->locale->getLocales(),
            'localeCode'       => $this->locale->getCode(),
            // translates
            'translateLayout'  => $layoutTranslate,
            'translateContent' => $contentTranslate,
            'translateClass'   => get_class($this->translator),
            'translateSearch'  => $this->translator->searchTranslate(array_merge($layoutTranslate, $contentTranslate)),   // vyhledani prekladu v driveru prekladace
            'translatesMap'    => $translateMap->toArray(),     // mapper translate from latte
            'defaultTranslate' => $this->translator->getListAllDefaultTranslate(), // list translate from default translate
            'usedTranslate'    => array_flip($this->translator->getListUsedTranslate()),    // list used translate index
            'dictionary'       => $this->translator->getDictionary(),   // list dictionary
        ];
        $latte = new Engine;
        return $latte->renderToString(__DIR__ . '/PanelTemplate.latte', $params);
    }


    /**
     * Extract file.
     *
     * @param string       $file
     * @param TranslateMap $translateMap
     * @return array
     * @throws \Latte\CompileException
     */
    private function extractFile(string $file, TranslateMap $translateMap): array
    {
        $buffer = null;
        $parser = new Parser();

        $result = [];
        $tokens = $parser->parse(file_get_contents($file));
        foreach ($tokens as $token) {

            // vylouceni zbytecnych tagu
            if ($token->type !== $token::MACRO_TAG || !in_array($token->name, ['_', '/_', 'include'], true)) {
                // pokud neni buffer null tak vklada text
                if ($buffer !== null) {
                    $buffer .= $token->text;
                }
                continue;
            }

            // pokud je konec prekladu a nebo jednoduchy uzavreny preklad tak preda buffer
            if ($token->name === '/_' || ($token->name === '_' && $token->closing === true)) {
                $result[] = $buffer;
                $translateMap->add($buffer, realpath($file), $token->line);

                $buffer = null;

                // pokud nazazi na blok include
            } elseif ($token->name === 'include') {

                // vezme aktualni slozku, spoji s hodnotou includ, pokud existuje tak na ni rekurzivne aplikuje extractFile
                $res = null;
                if (file_exists(dirname($file) . '/' . $token->value)) {
                    $res = $this->extractFile(dirname($file) . '/' . $token->value, $translateMap);
                }

                // slouceni pole nactenych z include bloku
                if ($res) {
                    $result = array_merge($result, $res);
                }

                // pokud je zacatek prekladu a je prazdna hodnota tak vyprazdni buffer
            } elseif ($token->name === '_' && !$token->value) {
                $buffer = '';

            } else {
                $writer = new PhpWriter(new MacroTokens($token->value), $token->modifiers);
                $message = $writer->write('%node.word');
                // pokud text obsahuje uvozovku, apostrof, tak vezme text mezi znaky
                if (in_array(substr(trim($message), 0, 1), ['"', '\''], TRUE)) {
                    $message = substr(trim($message), 1, -1);
                }
                $result[] = $message;
                $translateMap->add($message, realpath($file), $token->line);
            }
        }
        return $result;
    }
}
