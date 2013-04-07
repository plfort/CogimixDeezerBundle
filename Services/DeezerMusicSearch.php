<?php
namespace Cogipix\CogimixDeezerBundle\Services;

use Cogipix\CogimixCommonBundle\Entity\TrackResult;

use Cogipix\CogimixCommonBundle\MusicSearch\AbstractMusicSearch;

class DeezerMusicSearch extends AbstractMusicSearch
{

   private $baseUrl = 'http://api.deezer.com/2.0/search?q=';
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
        //echo $this->baseUrl;die();
        $c = curl_init($this->baseUrl);
        /* On indique à curl quelle url on souhaite télécharger */
       //echo $this->serializer->serialize($this->searchQuery);die();

        curl_setopt_array($c, $this->CURL_OPTS);

        /* On execute la requete */
        $output = curl_exec($c);

        //echo $output;die();
        /* On a une erreur alors on la lève */
        if ($output === false) {
            $this->logger->err(curl_error($c));

            return array();
        }

        return $this->parseResponse(json_decode($output,true));

    }

    protected function buildQuery()
    {
        //$this->logger->info($this->searchQuery);

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