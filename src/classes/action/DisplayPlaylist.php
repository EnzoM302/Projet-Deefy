<?php

namespace iutnc\deefy\action;

class DisplayPlaylist extends Action {

    public function __invoke() : string {
        if (isset($_SESSION['playlists'])) {
            $playlist = $_SESSION['playlists'];
            $renderer = new \iutnc\deefy\render\AudioListRenderer($playlist);
            $rendu  = $renderer->render(2);
            $rendu .= '<a href="?action=add-Podcast">Ajouter une Podcast</a> <br>';
            $rendu .= '<a href="?action=add-Track">Ajouter une musique</a>';
            return $rendu;
        } else {
            return "<p>Aucune playlist disponible. Veuillez en créer une d'abord.</p>";
        }
    }

}