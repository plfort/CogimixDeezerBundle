<?php

namespace Cogipix\CogimixDeezerBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Request;



use Cogipix\CogimixDeezerBundle\Entity\DeezerToken;

use Cogipix\CogimixCommonBundle\Utils\AjaxResult;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * @Route("/deezer")
 * @author plfort - Cogipix
 *
 */
class DefaultController extends Controller
{
    /**
     * @Route("/channel{_query}",name="_deezer_channel",requirements={"_query"="([a-zA-Z0-9=&|]+)?"},defaults={"_query"=""})
     */
    public function getChannelAction()
    {
        $maxage= 60*60*24*360;
        $response = new Response();
        $response->headers->set('Pragma','public');
        $response->headers->set('Cache-Control','maxage='.$maxage);
        $response->headers->set('Expires',gmdate('D, d M Y H:i:s', time()+$maxage) . ' GMT');
        $response->setContent($this->renderView('CogimixDeezerBundle::channel.html.twig'));
        return $response;
    }

    /**
     * @Secure("ROLE_USER")
     * @Route("/login",name="_deezer_login_success",options={"expose"=true})
     */
    public function loginSuccessAction(Request $request)
    {
        $response = new AjaxResult();
        $deezerAuthResponse = $request->request->get('authResponse',null);
        $deezerUserId = $request->request->get('userID',null);
        $this->get('logger')->info('Deezer login success ');

        if($deezerAuthResponse!==null && $deezerUserId !==null ){
            $user = $this->getUser();
            $em = $this->getDoctrine()->getManager();
            $deezerToken = $em->getRepository('CogimixDeezerBundle:DeezerToken')->findOneByUser($this->getUser());
            if($deezerToken!==null){
                $deezerToken->setDateCreated(new \DateTime());
                $deezerToken->setUserId($deezerUserId);
                $deezerToken->setAccessToken($deezerAuthResponse['accessToken']);
                $deezerToken->setExpiresIn($deezerAuthResponse['expire']);
            }else{
                $deezerToken = new DeezerToken();
                $deezerToken->setUser($user);
                $deezerToken->setDateCreated(new \DateTime());
                $deezerToken->setUserId($deezerUserId);
                $deezerToken->setAccessToken($deezerAuthResponse['accessToken']);
                $deezerToken->setExpiresIn($deezerAuthResponse['expire']);
                $em->persist($deezerToken);
            }
            $user->addRole('ROLE_DEEZER');
            $em->flush();
            $response->setSuccess(true);
            $this->get('security.context')->getToken()->setAuthenticated(false);
            $playlistRenderer = $this->get('deezer_music.playlist_renderer');
            $response->addData('playlistsHtml', $this->renderView($playlistRenderer->getListTemplate(),array('playlists'=>$playlistRenderer->getPlaylists())));
            $response->setHtml($this->renderView('CogimixDeezerBundle:Login:logoutLink.html.twig'));
        }

        return $response->createResponse();
    }

    /**
     * @Secure("ROLE_USER")
     * @Route("/logout",name="_deezer_logout",options={"expose"=true})
     */
    public function logoutAction(Request $request)
    {
        $response = new AjaxResult();
        $em = $this->getDoctrine()->getManager();
        $user=$this->getUser();
        $deezerToken = $em->getRepository('CogimixDeezerBundle:DeezerToken')->findOneByUser($user);
        $user->removeRole('ROLE_DEEZER');
        $this->get('security.context')->getToken()->setAuthenticated(false);
        if($deezerToken!==null){
            $em->remove($deezerToken);
        }
        $em->flush();
        $response->setSuccess(true);
        $response->setHtml($this->renderView('CogimixDeezerBundle:Login:loginLink.html.twig'));
        return $response->createResponse();
    }

    /**
     * @Secure("ROLE_DEEZER")
     * @Route("/playlist/track/{playlistId}",name="_deezer_playlist_songs",options={"expose"=true})
     */
    public function getPlaylistTracksAction($playlistId){
            $response = new AjaxResult();
            $em = $this->getDoctrine()->getManager();
            $user=$this->getUser();
            $deezerToken = $em->getRepository('CogimixDeezerBundle:DeezerToken')->findOneByUser($user);
            $deezerApi = $this->get('deezer_music.api');
            $deezerApi->setDeezerToken($deezerToken);
            $tracks= $deezerApi->getCurrentUserPlaylistTracks($playlistId);

            $response->setSuccess(true);
            $response->addData('tracks', $this->get('deezer_music.result_builder')->createArrayFromDeezerTracks($tracks));
            return $response->createResponse();
    }
}
