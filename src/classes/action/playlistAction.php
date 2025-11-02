<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\render\AudioListRenderer;

class PlaylistAction extends Action {

    public function __invoke() : string {
        if (isset($_GET['id'])) {
            $_SESSION['id_courant'] = (int) $_GET['id'];
            $nom_pl = DeefyRepository::getInstance()->getNomPlaylist($_SESSION['id_courant']);
            $playlist = DeefyRepository::getInstance()->getTrackPlaylist($_SESSION['id_courant'], $nom_pl);
            $renderer = new AudioListRenderer($playlist);
            $rendu = $renderer->render(2);
            $rendu .= '<a href="?action=add-PodTrack">Ajouter un Podcast</a> <br>';
            $rendu .= '<a href="?action=add-Track">Ajouter une musique</a> <br>';
            $rendu .= '<a href="?action=supprimerTrack">Supprimer une track</a>';
            return $rendu;
        } else {
            return "<p>Aucune track disponible. Veuillez vous connectez ou créer un compte.</p>";
        }
    }

}