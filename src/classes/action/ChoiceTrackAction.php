<?php

namespace iutnc\deefy\action;
use iutnc\deefy\repository\DeefyRepository;


class ChoiceTrackAction extends Action {

    public function __invoke(): string {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $track_list = DeefyRepository::getInstance()->getAllTrack();

            $options = '';
            foreach ($track_list as $track) {
                $id = htmlspecialchars(DeefyRepository::getInstance()->getIdTrack($track));
                $titre = htmlspecialchars($track->titre);
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
                <h1>Choisir une Track à ajouter</h1>
                <form method="POST" action="?action=Choice-Track">
                    <label for="track">Sélectionner un Podcasr :</label>
                    <select id="track" name="track" required>
                        <option value="">-- Sélectionner une track --</option>
                        $options
                    </select><br>
                    <input type="submit" value="Ajouter la track">
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