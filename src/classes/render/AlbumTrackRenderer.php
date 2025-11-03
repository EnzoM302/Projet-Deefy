<?php

namespace iutnc\deefy\render;

use iutnc\deefy\audio\track\AlbumTrack;

class AlbumTrackRenderer extends AudioTrackRenderer implements Renderer{
    
    public function __construct(AlbumTrack $audio) {
        parent::__construct($audio);
    }


    protected function petit():string  {
         return <<< HTML
                    <p>Titre : {$this->audio->titre}</p>
                    <p>Duree : {$this->audio->duree}</p>
                    <audio src="{$this->audio->nomFichier}" controls></audio>
                HTML;
    }

    protected function grand():string  {
        if ($this->audio instanceof AlbumTrack) {
            return <<< HTML
                    <div class="track-item">
                       <p class="track-title">Titre : {$this->audio->titre}</p>
                       <p>Nom album : {$this->audio->album}</p>
                       <p>Duree : {$this->audio->duree}</p>
                       <audio src="{$this->audio->nomFichier}" controls></audio>
                    </div>
                   HTML;
        }
        return "<p>Titre : {$this->audio->titre}</p>";
    }

}
