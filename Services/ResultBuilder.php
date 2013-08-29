<?php
namespace Cogipix\CogimixDeezerBundle\Services;

use Cogipix\CogimixCommonBundle\Entity\TrackResult;

class ResultBuilder{


    public function createFromDeezerTrack($deezerTrack){
        $track=null;

        if($deezerTrack['readable']==true){
            $track = new TrackResult();
            $track->setId($deezerTrack['id']);
            $track->setEntryId($deezerTrack['id']);
            $track->setArtist($deezerTrack['artist']['name']);
            $track->setTitle($deezerTrack['title']);
            $track->setTag($this->getResultTag());
            if(isset($deezerTrack['album']) && isset($deezerTrack['album']['cover'])){
                $track->setThumbnails($deezerTrack['album']['cover'].'?size=94x94');
            }else{
                $track->setThumbnails($this->getDefaultIcon());
            }
            $track->setIcon($this->getDefaultIcon());

        }
        return $track;
    }

    public function createFromDeezerAlbum($deezerAlbum){
        $cover = $deezerAlbum['cover'].'?size=small';
        $tracks= array();
        if(isset($deezerAlbum['tracks']) && isset($deezerAlbum['tracks']['data'])){
            $tracks= $this->createArrayFromDeezerTracks($deezerAlbum['tracks']['data']);
        }
        return $tracks;
    }

    public function createFromDeezerPlaylist($deezerPlaylist){

        $tracks= array();
        if(isset($deezerPlaylist['tracks']) && isset($deezerPlaylist['tracks']['data'])){
            $tracks= $this->createArrayFromDeezerTracks($deezerPlaylist['tracks']['data']);
        }
        return $tracks;
    }

    public function createArrayFromDeezerTracks($deezerTracks){

        $tracks = array();
        if(is_array($deezerTracks)){
            $count=count($deezerTracks);
            $tracks = array();
            for($i=0;$i<$count;$i++){
                $track = $this->createFromDeezerTrack($deezerTracks[$i]);
                if($track!=null){
                    $tracks[]=$track;
                }
            }
        }else{
            $track = $this->createFromDeezerTrack($deezerTracks);
            if($track!=null){
                $tracks[]=$track;
            }
        }
        return $tracks;
    }

    public function getResultTag()
    {
        return 'dz';
    }

    public function getDefaultIcon(){
        return 'bundles/cogimixdeezer/images/deezer-icon.png';
    }
}
