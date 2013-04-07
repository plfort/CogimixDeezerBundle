<?php
namespace Cogipix\CogimixDeezerBundle\ViewHooks\Modal;

use Cogipix\CogimixCommonBundle\ViewHooks\Modal\ModalItemInterface;
/**
 *
 * @author plfort - Cogipix
 *
 */
class ModalRenderer implements ModalItemInterface
{

    public function getModalTemplate()
    {
        return 'CogimixDeezerBundle:Modal:modals.html.twig';

    }

}
