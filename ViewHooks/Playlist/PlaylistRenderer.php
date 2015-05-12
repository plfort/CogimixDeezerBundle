<?php
namespace Cogipix\CogimixDeezerBundle\ViewHooks\Playlist;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;


use Cogipix\CogimixCommonBundle\Utils\TokenStorageAwareInterface;

use Cogipix\CogimixCommonBundle\ViewHooks\Playlist\PlaylistRendererInterface;
/**
 *
 * @author plfort - Cogipix
 *
 */
class PlaylistRenderer implements PlaylistRendererInterface,
        TokenStorageAwareInterface
{

    private $deezerApi;
    private $tokenStorage;
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
    public function setTokenStorage(
            TokenStorageInterface $tokenStorage)
    {
       $this->tokenStorage=$tokenStorage;

    }

    protected function getCurrentUser() {
        $user = $this->tokenStorage->getToken()->getUser();
        if ($user instanceof AdvancedUserInterface){
            return $user;
        }

        return null;
    }

    public function getTag(){
        return 'deezer';
    }

    public function getRenderPlaylistsParameters()
    {
        return array('playlists'=>$this->getPlaylists());
    }
}
