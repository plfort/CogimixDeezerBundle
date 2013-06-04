<?php
namespace Cogipix\CogimixDeezerBundle\ViewHooks\Playlist;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;

use Symfony\Component\Security\Core\SecurityContextInterface;

use Cogipix\CogimixCommonBundle\Utils\SecurityContextAwareInterface;

use Cogipix\CogimixCommonBundle\ViewHooks\Playlist\PlaylistRendererInterface;
/**
 *
 * @author plfort - Cogipix
 *
 */
class PlaylistRenderer implements PlaylistRendererInterface,
        SecurityContextAwareInterface
{

    private $deezerApi;
    private $securityContext;
    private $om;


    public function __construct(ObjectManager $om, $deezerApi){
        $this->om = $om;
        $this->deezerApi=$deezerApi;
    }

    public function getListTemplate()
    {
        return 'CogimixDeezerBundle:Playlist:list.html.twig';

    }

    public function getPlaylists()
    {

        $user=$this->getCurrentUser();
        if($user!==null){
            $deezerToken = $this->om->getRepository('CogimixDeezerBundle:DeezerToken')->findOneByUser($user);
            if($deezerToken!==null){
                $this->deezerApi->setDeezerToken($deezerToken);
                return $this->deezerApi->getCurrentUserPlaylist();
            }
        }

        return array();
    }
    public function setSecurityContext(
            SecurityContextInterface $securityContext)
    {
       $this->securityContext=$securityContext;

    }

    protected function getCurrentUser() {
        $user = $this->securityContext->getToken()->getUser();
        if ($user instanceof AdvancedUserInterface){
            return $user;
        }

        return null;
    }

    public function getTag(){
        return 'deezer';
    }
}
