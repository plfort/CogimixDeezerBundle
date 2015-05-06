<?php
namespace Cogipix\CogimixDeezerBundle\lib;

use Cogipix\CogimixDeezerBundle\Model\DeezerPlaylist;

class DeezerApi
{

    /**
     *
     * @var string
     */
    private $apiUrl = "https://api.deezer.com/2.0";

    private $deezerToken;

    private $CURL_OPTS = array(

            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,

   );

    private $apiMethods =array(
            'SEARCH'=>'/search',
            'USERINFO'=>'/user/me',
            'USERPLAYLISTS'=>'/user/me/playlists',
            'PLAYLIST'=>'/playlist');


    protected function callApi($urlPart){
        $c = curl_init($this->apiUrl.$urlPart);

        curl_setopt_array($c, $this->CURL_OPTS);
        $output = curl_exec($c);
        if ($output === false) {
            trigger_error('curl error : ' . curl_error($c), E_USER_WARNING);
        } else {
            curl_close($c);
            return json_decode($output,true);
        }
    }

    public function getCurrentUserPlaylist(){
        $playlists= array();
        if($this->deezerToken !== null ){
            $urlPart = $this->apiMethods['USERPLAYLISTS'].'?access_token='.$this->deezerToken->getAccessToken();
            $responseDeezer= $this->callApi($urlPart);
            if(!empty($responseDeezer) && isset($responseDeezer['data'])){
                $deezerPlaylists=$responseDeezer['data'];
                    foreach($deezerPlaylists as $deezerPlaylist){

                        $playlist = new DeezerPlaylist($deezerPlaylist['id'],$deezerPlaylist['title']);
                        $playlists[]=$playlist;
                    }
            }
        }
        return $playlists;
    }

    public function getCurrentUserPlaylistTracks($playlistId){
        $tracks= array();
        if($this->deezerToken !== null ){
            $urlPart = $this->apiMethods['PLAYLIST'].'/'.$playlistId.'?access_token='.$this->deezerToken->getAccessToken();

            $responseDeezer= $this->callApi($urlPart);

            if(!empty($responseDeezer)){

                $deezerPlaylistTracks=$responseDeezer['tracks']['data'];

                return $deezerPlaylistTracks;

            }
        }
        return $tracks;
    }

    public function search($query){
        $urlPart = $this->apiMethods['SEARCH'].'?q='.$query;
        $result= $this->callApi($urlPart);
    }


    public function getDeezerToken()
    {
        return $this->deezerToken;
    }

    public function setDeezerToken($deezerToken)
    {
        $this->deezerToken = $deezerToken;
    }

}
