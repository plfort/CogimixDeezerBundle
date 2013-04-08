<?php
namespace Cogipix\CogimixDeezerBundle\ViewHooks\Widget;
use Cogipix\CogimixCommonBundle\ViewHooks\Widget\WidgetRendererInterface;

/**
 *
 * @author plfort - Cogipix
 *
 */
class WidgetRenderer implements WidgetRendererInterface
{

    private $appId;

    public function __construct($appId){
        $this->appId=$appId;
    }

    public function getWidgetTemplate()
    {
        return 'CogimixDeezerBundle:Widget:widget.html.twig';
    }

    public function getParameters(){
        return array('appId'=>$this->appId);
    }

}
