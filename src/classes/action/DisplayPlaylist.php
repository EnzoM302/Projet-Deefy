<?php

namespace iutnc\deefy\action;

use iutnc\deefy\repository\DeefyRepository;

class DisplayPlaylist extends Action {

    public function __invoke() : string {
        if (isset($_SESSION['email'])) {
            $email = $_SESSION['email'];
            $playlist = DeefyRepository::getInstance()->getPlaylistsUser($email);
            // $renderer = new AudioListRenderer($playlist);
            // $rendu .= '<a href="?action=add-Podcast">Ajouter une Podcast</a> <br>';
            // $rendu .= '<a href="?action=add-Track">Ajouter une musique</a>';
            $html = "<div class='playlist-list'>" . $playlist . "</div>";
            return $html;
        } else {
            return "<p>Aucune playlist disponible. Veuillez en créer une d'abord.</p>";
        }
    }

}