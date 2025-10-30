<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\render\AudioListRenderer;

class PlaylistAction extends Action {

    public function __invoke() : string {
        if (isset($_GET['id'])) {

            $_SESSION['pl_courante'] = (int) $_GET['id'];
            $nom_pl = DeefyRepository::getInstance()->getNomPlaylist($_SESSION['pl_courante']);
            $playlist = DeefyRepository::getInstance()->getTrackPlaylist($_SESSION['pl_courante'], $nom_pl);
            $renderer = new AudioListRenderer($playlist);
            $rendu = $renderer->render(2);
            $rendu .= '<a href="?action=add-Podcast">Ajouter une Podcast</a> <br>';
            $rendu .= '<a href="?action=add-Track">Ajouter une musique</a>';
            return $rendu;
        } else {
            return "<p>Aucune track disponible. Veuillez en créer une d'abord.</p>";
        }
    }

}