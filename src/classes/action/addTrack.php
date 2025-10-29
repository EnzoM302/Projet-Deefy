<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\track\AlbumTrack;
use iutnc\deefy\audio\track\PodcastTrack;
use iutnc\deefy\render\AudioListRenderer;


class AddPodcastTrackAction extends Action {
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
                <form action="?action=add-PodTrack" method="post">
                    <label for="name">Titre : </label>
                    <input type="text" id="name" name="name" required>
                    <label for="artist">Auteur : </label>
                    <input type="text" id="artist" name="artist" required>
                    <label for="path">Chemin : </label>
                    <input type="text" id="path" name="path" required>
                    <label for="album">Album : </label>
                    <input type="text" id="album" name="album" required>
                    <input type="submit" value="Ajouter Podcast">
                </form>
            </body>
            </html>
            HTML;
        }else{
            $getID3 = new \getID3;
            $name = filter_var($_POST['name'], FILTER_SANITIZE_SPECIAL_CHARS);
            $Album = filter_var($_POST['album'], FILTER_SANITIZE_SPECIAL_CHARS);
            $artist = filter_var($_POST['artist'], FILTER_SANITIZE_SPECIAL_CHARS);
            $file_hash = md5_file($_FILES['fichier']['tmp_name']);
            $file_tmp = $_FILES['fichier']['tmp_name'];
            $uploadDir = 'C:\xampp\htdocs\web\td11\Upload\\';
            $urlPath = '/web/td11/Upload/Podacast/';
            if ($_FILES['fichier']['error'] === UPLOAD_ERR_OK
            && substr($_FILES['fichier']['name'],-4) === '.mp3'
            && $_FILES['fichier']['type'] === 'audio/mpeg') {
                $chemin = $uploadDir.$file_hash.'.mp3';
                $info = $getID3->analyze($chemin);
                if (file_exists($chemin)) {
                    print "Le fichier existe déjà ! il sera juste ajouté à la playlist.";
                    $cheminWeb = $urlPath.$file_hash.'.mp3';
                    $duration = round($info['playtime_seconds']);
                    $track = new AlbumTrack($name, $artist, $cheminWeb, $Album, $duration);
                    $_SESSION['playlists']->ajouter($track);
                }else{

                    if (move_uploaded_file($file_tmp, $chemin)) {
                        print "Fichier téléchargé avec succès.";
                        $cheminWeb = $urlPath.$file_hash.'.mp3';
                        $duration = round($info['playtime_seconds']);
                        $track = new AlbumTrack($name, $artist, $cheminWeb, $Album, $duration);
                        $_SESSION['playlists']->ajouter($track);
                    } else {
                        print "Erreur lors du téléchargement du fichier.";
                    }
                    
                }
                
        }
        $renderer = new AudioListRenderer($_SESSION['playlists']);
        $rendu  = $renderer->render(2);
        $rendu .= '<a href="?action=add-PodTrack">Ajouter une piste</a>';
        return $rendu;
    }
}
}