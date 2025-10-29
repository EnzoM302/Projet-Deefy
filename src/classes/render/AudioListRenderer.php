<?php
declare(strict_types=1);

namespace iutnc\deefy\render;

use iutnc\deefy\audio\lists\AudioListe;
use iutnc\deefy\audio\track\AlbumTrack;
use iutnc\deefy\audio\track\PodcastTrack;
class AudioListRenderer implements Renderer{
    private AudioListe $audiolist;
    
    public function __construct(AudioListe $audiolist){
        $this->audiolist = $audiolist;
    }

    public function render (int $selector): string{
        $html = "<div class='audiolist'>\n";
        $html .= "<h2>{$this->audiolist->nom}</h2>\n";
        $html .= "<ul>\n";
        foreach ($this->audiolist->track as $piste) {
            if ($piste instanceof AlbumTrack) {
                $trackRenderer = new AlbumTrackRenderer($piste);
            } elseif ($piste instanceof PodcastTrack) {
                $trackRenderer = new \iutnc\deefy\render\PodcastRenderer($piste);
            } else {
                continue; 
            }
            $html .= "<li>" . $trackRenderer->render(Renderer::COMPACT) . "</li>\n";
        }

        $html .= "</ul>\n";
        $html .= "<p><strong>Nombre de pistes :</strong> {$this->audiolist->nbPistes}</p>\n";
        $html .= "<p><strong>Durée totale :</strong> {$this->audiolist->dureeTot} secondes</p>\n";
        $html .= "</div>\n";

        return $html;
    }

}