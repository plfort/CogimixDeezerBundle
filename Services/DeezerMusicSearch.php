<?php
namespace Cogipix\CogimixDeezerBundle\Services;

use Cogipix\CogimixCommonBundle\Entity\TrackResult;

use Cogipix\CogimixCommonBundle\MusicSearch\AbstractMusicSearch;

class DeezerMusicSearch extends AbstractMusicSearch
{

   private $baseUrl = 'https://api.deezer.com/2.0/search?q=';
   private $resutBuilder;
   private $CURL_OPTS = array(
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 20,
            );

    public function __construct($resutBuilder)
    {
        $this->resutBuilder=$resutBuilder;
    }

    protected function parseResponse($output)
    {

       $tracks = array();
       try{

       if(isset($output['data'])){
           $tracks=$this->resutBuilder->createArrayFromDeezerTracks($output['data']);
//            foreach($output['data'] as $item){
//                if($item['readable']==true){

//                    $track = new TrackResult();
//                    $track->setId($item['id']);
//                    $track->setEntryId($item['id']);
//                    $track->setArtist($item['artist']['name']);
//                    $track->setTitle($item['title']);
//                    $track->setTag($this->getResultTag());
//                    $track->setThumbnails($item['album']['cover'].'?size=small');
//                    $track->setIcon($this->getDefaultIcon());
//                    $tracks[]=$track;
//                }
//            }
       }
        }catch(\Exception $ex){
            $this->logger->info($ex->getMessage());
            return array();
        }
        return $tracks;
    }

    protected function executeQuery()
    {

        $c = curl_init($this->baseUrl);

        curl_setopt_array($c, $this->CURL_OPTS);
        $output = curl_exec($c);
        if ($output === false) {
            $this->logger->err(curl_error($c));

            return array();
        }

        return $this->parseResponse(json_decode($output,true));

    }

    protected function buildQuery()
    {

        if($this->searchQuery){
          $this->baseUrl.=$this->searchQuery->getSongQuery();

        }

    }

    public function getName()
    {
        return 'Deezer';
    }

    public function getAlias()
    {
        return 'deezer';
    }

    public function getResultTag()
    {
        return 'dz';
    }

    public function getDefaultIcon(){
        return 'bundles/cogimixdeezer/images/deezer-icon.png';
    }


}

?>