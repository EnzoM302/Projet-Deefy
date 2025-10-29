<?php
namespace iutnc\deefy\action;

use iutnc\deefy\audio\track\AlbumTrack;
use iutnc\deefy\audio\track\PodcastTrack;
use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\auth\AuthProvider;
use iutnc\deefy\exception\AuthException;

class AuthAction extends Action{
    public function __invoke(): string {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
           return <<<HTML
            <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Document</title>
            </head>
            <body>
                <h1>Authentification</h1>
                <form action="?action=auth" method="post" enctype="multipart/form-data">
                    <label for="email">Email :</label>
                    <input type="email" id="email" name="email" required>
                    <br>
                    <label for="mdp">Mot de passe :</label>
                    <input type="password" id="mdp" name="mdp" required>
                    <br>
                    <input type="submit" value="S'authentifier">
                </form>
            </body>
            </html>
            HTML;
        }else{
            $mail = $_POST['email'];
            if (filter_var($mail, FILTER_VALIDATE_EMAIL)) {
                AuthProvider::signin($mail, $_POST['mdp']);
            }else{
                throw new AuthException("AUTH ERROR");
            }
            var_dump($_SESSION);
            return "<p>Authentification réussie pour l'utilisateur : $mail</p>";
        }
    }

}
