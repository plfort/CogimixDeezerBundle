<?php
namespace Cogipix\CogimixDeezerBundle\Services;

use Cogipix\CogimixCommonBundle\Model\ParsedUrl;

use Cogipix\CogimixCommonBundle\MusicSearch\UrlSearcherInterface;

class DeezerUrlSearch implements UrlSearcherInterface
{
    private $baseUrl = 'https://api.deezer.com/2.0/';
    private $regexHost = '#^(?:www\.)?(?:deezer\.com|deezer\.fr)#';
    private $resultBuilder;
    private $CURL_OPTS = array(
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
    );
    public function __construct(ResultBuilder $resultBuilder){
        $this->resultBuilder = $resultBuilder;
    }


    public function canParse($host)
    {

        preg_match($this->regexHost, $host,$matches);

       return isset($matches[0]) ? $matches[0] : false;

    }

    public function searchByUrl(ParsedUrl $url)
    {

        if( ($match = $this->canParse($url->host)) !== false){

            $urlDeezer = null;
            $result = null;
            $type =null;
            if(in_array('album', $url->path)){
               $urlDeezer=$this->baseUrl.'album/'.end($url->path);
               $type='album';
            }
            if(in_array('artist', $url->path)){
                $urlDeezer=$this->baseUrl.'artist/'.end($url->path).'/top';
                $type='artist';
            }
            if(in_array('track', $url->path)){
               $urlDeezer=$this->baseUrl.'track/'.end($url->path);
               $type='track';
            }
            if(in_array('playlist', $url->path)){
                $urlDeezer=$this->baseUrl.'playlist/'.end($url->path);
                $type='playlist';
            }
            $c = curl_init($urlDeezer);
            curl_setopt_array($c, $this->CURL_OPTS);
            $outputJson = curl_exec($c);

            if ($outputJson === false) {
                 return null;
            }

            $output = json_decode($outputJson,true);
           
            switch($type){
                case 'album': return  $this->resultBuilder->createFromDeezerAlbum($output);break;
                case 'artist': return  $this->resultBuilder->createArrayFromDeezerTracks($output['data']);break;
                case 'playlist': return  $this->resultBuilder->createFromDeezerPlaylist($output);break;
                case 'track': return  $this->resultBuilder->createFromDeezerTrack($output);break;
            }


        }else{
            return null;
        }


    }

}
