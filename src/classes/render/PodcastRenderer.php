<?php
namespace iutnc\deefy\render;

use iutnc\deefy\audio\track\PodcastTrack;

class PodcastRenderer extends AudioTrackRenderer  implements Renderer{

     public function __construct(PodcastTrack $audio) {
        parent::__construct($audio);
    }
     

    protected function petit():string  {
         return <<< HTML
               
                    <p>Titre : {$this->audio->titre}</p>
                    <p>Duree : {$this->audio->duree}</p>
                    <audio src="{$this->audio->nomFichier}" type="audio/mpeg" controls></audio>
                HTML;
    }

    protected function grand():string  {
         return <<< HTML
               
                    <p>Titre : {$this->audio->titre}</p>
                    <p>Nom podcast : {$this->audio->genre}</p>
                    <p>Duree : {$this->audio->duree}</p>
                    <audio src="{$this->audio->nomFichier}" type="audio/mpeg" controls></audio>
                HTML;
    }
    

}

