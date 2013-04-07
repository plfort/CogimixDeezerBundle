<?php
namespace Cogipix\CogimixDeezerBundle\ViewHooks\Javascript;
use Cogipix\CogimixCommonBundle\ViewHooks\Javascript\JavascriptImportInterface;

use Cogipix\CogimixCommonBundle\ViewHooks\Menu\MenuItemInterface;

/**
 *
 * @author plfort - Cogipix
 *
 */
class JavascriptImportRenderer implements JavascriptImportInterface
{

    public function getJavascriptImportTemplate()
    {
        return 'CogimixDeezerBundle::js.html.twig';
    }

}
