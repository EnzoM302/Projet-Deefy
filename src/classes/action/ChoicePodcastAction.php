<?php

namespace iutnc\deefy\action;
use iutnc\deefy\repository\DeefyRepository;


class ChoicePodcastAction extends Action {

    public function __invoke(): string {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $podcast_list = DeefyRepository::getInstance()->getAllPodcast();

            $options = '';
            foreach ($podcast_list as $podcast) {
                $id = htmlspecialchars(DeefyRepository::getInstance()->getIdTrack($podcast));
                $titre = htmlspecialchars($podcast->titre);
                $options .= "<option value=\"$id\">$titre</option>";
            }

            return <<<HTML
            <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Choisir une Track</title>
            </head>
            <body>
                <h1>Choisir un Podcast à ajouter</h1>
                <form method="POST" action="?action=Choice-Podcast">
                    <label for="track">Sélectionner un Podcast :</label>
                    <select id="track" name="track" required>
                        <option value="">-- Sélectionner un podcast --</option>
                        $options
                    </select><br>
                    <input type="submit" value="Ajouter le podcast">
                </form>
            </body>
            </html>
            HTML;
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $track_id = filter_var($_POST['track'], FILTER_SANITIZE_NUMBER_INT);
            $id_pl = $_SESSION['id_courant'];
            $track = DeefyRepository::getInstance()->getTrack($track_id);
            DeefyRepository::getInstance()->AjouterTrackPlaylist($id_pl, $track);
            $_SESSION['playlists']->ajouter($track);
            return "<p>Track ajoutée avec succès à la playlist.</p><a href=\"?action=playlist&id=$id_pl\">Retour à la playlist</a>";

        }
        return "Méthode non supportée.";
    }
}