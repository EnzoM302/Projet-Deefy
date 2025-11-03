<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\render\AudioListRenderer;

class DefaultAction extends Action {
    public function __invoke() : string {
        if (isset($_SESSION['user'])) {
            if (isset($_SESSION['id_courant'])){
                $html = "<h1>Playlist Courante</h1>";
                $nom_pl = DeefyRepository::getInstance()->getNomPlaylist($_SESSION['id_courant']);
                $playlist = DeefyRepository::getInstance()->getTrackPlaylist($_SESSION['id_courant'], $nom_pl);
                $renderer = new AudioListRenderer($playlist);
                $html .= $renderer->render(2);
            } else{
                $html = "<p class='center'>Auncune Playlist n'a été selectionné</p>";
            }
        }else{
            $html = "<h1>Bienvenu sur Deefy !</h1>";
            $html .= "<p class='center'>Veuillez vous connecter</p>";
        }
        return $html;
    }
}
