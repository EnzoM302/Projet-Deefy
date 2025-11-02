<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\track\AlbumTrack;
use iutnc\deefy\audio\track\PodcastTrack;
use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\repository\DeefyRepository;

class addTrack extends Action {


    public function __invoke(): string {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return <<<HTML
            <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Ajouter une piste</title>
            </head>
            <body>
                <h1>Add Track</h1>
                <form action="?action=add-Track" method="post" enctype="multipart/form-data">
                    <label for="titre">Titre : </label>
                    <input type="text" id="titre" name="titre" required><br>
                    <label for="genre">Genre : </label>
                    <input type="text" id="genre" name="genre" required><br>
                    <label for="artist">Auteur : </label>
                    <input type="text" id="artist" name="artist" required><br>
                    <label for="file">Fichier : </label>
                    <input type="file" id="file" name="file" required><br>
                    <label for="album">Album : </label>
                    <input type="text" id="album" name="album" required><br>
                    <label for="numero_album">Numéro de l'album : </label>
                    <input type="number" id="numero_album" name="numero_album" required><br>
                    <label for="Annee">Année : </label>
                    <input type="date" id="Annee" name="Annee" required><br>
                    <input type="submit" value="Ajouter Musique">
                </form>
            </body>
            </html>
            HTML;
        }

        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            return "Erreur : Aucun fichier n'a été téléchargé.";
        }

        $getID3 = new \getID3;
        $name = filter_var($_POST['titre'], FILTER_SANITIZE_SPECIAL_CHARS);
        $album = filter_var($_POST['album'], FILTER_SANITIZE_SPECIAL_CHARS);
        $artist = filter_var($_POST['artist'], FILTER_SANITIZE_SPECIAL_CHARS);
        $genre = filter_var($_POST['genre'], FILTER_SANITIZE_SPECIAL_CHARS);
        $annee_album = intval(substr($_POST['Annee'], 0, 4));
        $numero_album = filter_var($_POST['numero_album'], FILTER_SANITIZE_NUMBER_INT);
        $file_hash = md5_file($_FILES['file']['tmp_name']);
        $file_tmp = $_FILES['file']['tmp_name'];

        $uploadDir = dirname(__DIR__, 3) . '/Upload/Musique/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $chemin = $uploadDir . $file_hash . '.mp3';
        $cheminWeb = "/web/ProjetDeefy/Projet-Deefy/Upload/Musique/" . $file_hash . '.mp3';
        $pl_id = $_SESSION['id_courant'];

        if ($_FILES['file']['type'] === 'audio/mpeg' && substr($_FILES['file']['name'], -4) === '.mp3') {

            if (!file_exists($chemin)) {
                if (!move_uploaded_file($file_tmp, $chemin)) {
                    return "Erreur lors du téléchargement du fichier.";
                }
                print "Fichier téléchargé avec succès.<br>";
            } else {
                print "Le fichier existe déjà ! Il sera juste ajouté à la playlist.<br>";
            }

            $info = $getID3->analyze($chemin);
            $duration = isset($info['playtime_seconds']) ? round($info['playtime_seconds']) : 0;

            $track = new AlbumTrack(
                $name,
                $cheminWeb,
                $album,
                $_SESSION['playlists']->getNextAlbumTrackNumber(),
                $duration,
                $numero_album,
                $artist,
                $annee_album,
                $genre
            );

            DeefyRepository::getInstance()->AjouterTrackPlaylist($pl_id, $track);
            $_SESSION['playlists']->ajouter($track);
        } else {
            return "Erreur : le fichier doit être un MP3 valide.";
        }

        $renderer = new AudioListRenderer($_SESSION['playlists']);
        $rendu = $renderer->render(2);
        $rendu .= '<a href="?action=add-Track">Ajouter une piste</a>';
        return $rendu;
    }
}