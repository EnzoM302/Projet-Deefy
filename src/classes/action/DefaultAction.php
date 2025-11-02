<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\render\AudioListRenderer;

class DefaultAction extends Action {
    public function __invoke() : string {
        $html = "<h1>Bienvenu sur Deefy!</h1>";
        if (isset($_SESSION['user'])) {
            if (isset($_SESSION['id_courant'])){

                $html .= "<h2>Votre playlist courante</h2>";

                $nom_pl = DeefyRepository::getInstance()->getNomPlaylist($_SESSION['id_courant']);
                $playlist = DeefyRepository::getInstance()->getTrackPlaylist($_SESSION['id_courant'], $nom_pl);
                $renderer = new AudioListRenderer($playlist);
                $html .= $renderer->render(2);
            }
            
        }
        return $html;
    }
}
