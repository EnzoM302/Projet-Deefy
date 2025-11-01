<?php

namespace iutnc\deefy\action;
use iutnc\deefy\repository\DeefyRepository;

class SupprTrackAction extends Action{
    public function __invoke(): string {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $playlist_id = $_SESSION['id_courant'];
            $track_list = DeefyRepository::getInstance()->getAllTrackPlaylist($playlist_id);

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
                    <title>Supprimer une piste</title>
                </head>
                <body>
                    <h1>Supprimer une piste de la playlist</h1>
                    <form method="post" action="?action=supprimerTrack">
                        <label for="track">Choisir une piste :</label>
                        <select name="id_track" id="track" required>
                            $options
                        </select>
                        <input type="hidden" name="id_pl" value="$playlist_id">
                        <input type="submit" value="Supprimer la piste">
                    </form>
                </body>
                </html>
            HTML;

        
        }else{
            $id_pl = (int) $_POST['id_pl'];
                $id_track = (int) $_POST['id_track'];
            DeefyRepository::getInstance()->SupprimerTrackPlaylist($id_pl, $id_track);
            return "<p>Piste supprimée avec succès de la playlist.</p><a href=\"?action=playlist&id=$id_pl\">Retour à la playlist</a>";
        }
    }
}