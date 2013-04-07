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
            $track->setThumbnails($deezerTrack['album']['cover'].'?size=small');
            $track->setIcon($this->getDefaultIcon());

        }
        return $track;
    }

    public function createArrayFromDeezerTracks($deezerTracks){
        $count=count($deezerTracks);
        $tracks = array();
        for($i=0;$i<$count;$i++){
            $track = $this->createFromDeezerTrack($deezerTracks[$i]);
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
