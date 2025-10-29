<?php

namespace iutnc\deefy\action;


use iutnc\deefy\audio\track\AlbumTrack;
use iutnc\deefy\audio\track\PodcastTrack;
use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\audio\lists\Playlist;


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
                <form action="?action=add-PodTrack" method="post" enctype="multipart/form-data">
                    <label for="name">Titre : </label>
                    <input type="text" id="name" name="name" required>
                    <br>
                    <label for="artist">Auteur : </label>
                    <input type="text" id="artist" name="artist" required>
                    <br>
                    <input type="file" id="file" name="fichier" required>
                    <!-- <br>
                    <label for="duration">Duree en (secondes) : </label>
                    <input type="text" id="duration" name="duration" required> -->
                    <br>
                    <input type="submit" value="Ajouter Podcast">
                </form>
            </body>
            </html>
            HTML;
        }else{
            $getID3 = new \getID3;
            $name = filter_var($_POST['name'], FILTER_SANITIZE_SPECIAL_CHARS);
            // $duration = filter_var($_POST['duration'], FILTER_SANITIZE_NUMBER_INT);
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
                    $podcast = new PodcastTrack($name, $artist, $cheminWeb, $duration);
                    $_SESSION['playlists']->ajouter($podcast);
                }else{

                    if (move_uploaded_file($file_tmp, $chemin)) {
                        print "Fichier téléchargé avec succès.";
                        $cheminWeb = $urlPath.$file_hash.'.mp3';
                        $duration = round($info['playtime_seconds']);
                        $podcast = new PodcastTrack($name, $artist, $cheminWeb, $duration);
                        $_SESSION['playlists']->ajouter($podcast);
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