<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\track\PodcastTrack;
use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\repository\DeefyRepository;

class AddPodcastTrackAction extends Action {

    function validerDate(string $input): ?string {
    try {
        $date = new \DateTime($input);
        return $date->format('Y-m-d'); // ou 'd/m/Y' selon ton besoin
    } catch (\Exception $e) {
        return null; // ou gérer l'erreur autrement
    }
    }

    public function __invoke(): string {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return <<<HTML
            <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Ajouter un podcast</title>
            </head>
            <body>
                <h1>Add Playlist</h1>
                <form action="?action=add-PodTrack" method="post" enctype="multipart/form-data">
                    <label for="titre">Titre : </label>
                    <input type="text" id="titre" name="titre" required><br>
                    <label for="genre">Genre : </label>
                    <input type="text" id="genre" name="genre" required><br>
                    <label for="artist">Auteur : </label>
                    <input type="text" id="artist" name="artist" required><br>
                    <label for="file">Fichier : </label>
                    <input type="file" id="file" name="file" required><br>
                    <label for="date">Date : </label>
                    <input type="date" id="date" name="date" required><br>
                    <input type="submit" value="Ajouter Podcast">
                </form>
            </body>
            </html>
            HTML;
        }

        $getID3 = new \getID3;
        $pl_id = $_SESSION['id_courant'];
        $name = filter_var($_POST['titre'], FILTER_SANITIZE_SPECIAL_CHARS);
        $artist = filter_var($_POST['artist'], FILTER_SANITIZE_SPECIAL_CHARS);
        $genre = filter_var($_POST['genre'], FILTER_SANITIZE_SPECIAL_CHARS);
        $date =  $this->validerDate($_POST['date']);
        if ($date === null) {
            return "Erreur : date invalide.";
        }
        $file_hash = md5_file($_FILES['file']['tmp_name']);
        $file_tmp = $_FILES['file']['tmp_name'];

        $uploadDir = dirname(__DIR__, 3) . '/Upload/Podcast/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $chemin = $uploadDir . $file_hash . '.mp3';
        $cheminWeb = "/web/ProjetDeefy/Projet-Deefy/Upload/Podcast/" . $file_hash . '.mp3';

        if ($_FILES['file']['error'] === UPLOAD_ERR_OK
            && substr($_FILES['file']['name'], -4) === '.mp3'
            && $_FILES['file']['type'] === 'audio/mpeg') {

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

            $podcast = new PodcastTrack($name, $artist, $cheminWeb, $duration, $genre, $date);
            DeefyRepository::getInstance()->AjouterTrackPlaylist($pl_id, $podcast);
            $_SESSION['playlists']->ajouter($podcast);
        } else {
            return "Erreur : le fichier doit être un MP3 valide.";
        }

        $renderer = new AudioListRenderer($_SESSION['playlists']);
        $rendu = $renderer->render(2);
        $rendu .= '<a href="?action=add-PodTrack">Ajouter une piste</a>';
        return $rendu;
    }
}