<?php
namespace iutnc\deefy\action;

use iutnc\deefy\audio\track\AlbumTrack;
use iutnc\deefy\audio\track\PodcastTrack;
use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\auth\AuthProvider;
use iutnc\deefy\exception\AuthException;

class RegisterAction extends Action{
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
                <h1>Inscription</h1>
                <form action="?action=register" method="post" enctype="multipart/form-data">
                    <label for="email">Email :</label>
                    <input type="email" id="email" name="email" required>
                    <br>
                    <label for="mdp">Mot de passe :</label>
                    <input type="password" id="mdp" name="mdp" required>
                    <br>
                    <input type="submit" value="S'enregistrer">
                </form>
            </body>
            </html>
            HTML;
        }else{
            $mail = $_POST['email'];
            if (filter_var($mail, FILTER_VALIDATE_EMAIL)) {
                AuthProvider::register($mail, $_POST['mdp']);
            }else{
                throw new AuthException("REGISTER ERROR");
            }
            $_SESSION['email'] = $mail;
            return "<p class='center'>Inscription réussie pour l'utilisateur : $mail</p>";
        }
    }

}
