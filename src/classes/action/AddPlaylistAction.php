<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\lists;
use iutnc\deefy\audio\lists\AudioListe;
use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\repository\DeefyRepository;

class AddPlaylistAction extends Action {
    public function __invoke() : string {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return <<<HTML
            <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Document</title>
            </head>
            <body>
                <h1>Add Playlist</h1>
                <form action="?action=add-Playlist" method="post">
                    <label for="name">Nom de la playlist:</label>
                    <input type="text" id="name" name="name" required>
                    <input type="submit" value="Créer Playlist">
            </body>
            </html>
            HTML;
        }else{
            $name = filter_var($_POST['name'], FILTER_SANITIZE_SPECIAL_CHARS);
            $playlist = new lists\Playlist($name);
            $_SESSION['playlists'] = $playlist;

            $r = DeefyRepository::getInstance();
            $playlist = $r->saveEmptyPlaylist($playlist);
            $renderer = new AudioListRenderer($playlist);
            $rendu  = $renderer->render(2);
            $rendu .= '<a href="?action=add-Track">Ajouter une piste</a>';
            return $rendu;
        }
    }

}