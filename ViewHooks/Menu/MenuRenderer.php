<?php
namespace Cogipix\CogimixDeezerBundle\ViewHooks\Menu;
use Cogipix\CogimixCommonBundle\ViewHooks\Menu\MenuItemInterface;

/**
 *
 * @author plfort - Cogipix
 *
 */
class MenuRenderer implements MenuItemInterface{

    public function getMenuItemTemplate()
    {
          return 'CogimixDeezerBundle:Menu:menu.html.twig';

    }
}