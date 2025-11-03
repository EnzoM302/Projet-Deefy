<?php

namespace iutnc\deefy\Dispatch;

use iutnc\deefy\action;

class Dispatcher {
    private string $action;

    public function __construct(string $action) {
        if (!isset($_GET['action'])) {
            $_GET['action'] = 'default';
        }
        $this->action = $action;
    }
    public function run() : void{
        $html = '';
       switch ($this->action) {
        case 'playlist':
            $html = (new action\DisplayPlaylist())();
            break;
        case 'add-Playlist':
            $html = (new action\AddPlaylistAction())();
            break;
        case 'add-Track':
            $html = (new action\addTrack())();
            break;
        case 'add-PodTrack':
            $html = (new action\AddPodcastTrackAction())();
            break;
        case 'auth':
            $html = (new action\AuthAction())();
            break;
        case 'register':
            $html = (new action\RegisterAction())();
            break;
        case 'playlistRender':
            $html = (new action\playlistAction())();
            break;
        case 'deconnect':
            $html = (new action\DeconnectAction())();
            break;
        case 'supprimerTrack':
            $html = (new action\SupprTrackAction())();
            break;
        case 'Choice-Track':
            $html = (new action\ChoiceTrackAction())();
            break;
        default:
            $html = (new action\DefaultAction())();
            break;
       }
       $this->renderPage($html);
    }

    private function renderPage(string $content): void {
        echo <<<FIN
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="stylesheet" href="css/style.css">
            <title>Deefy App</title>
        </head>
        <body>
            <header class="container">
            <h1>DEEFY</h1>
            <nav>
                
                    <a href="?action=default">Home</a>
                    <a href="?action=playlist">Mes Playlists</a>
                    <a href="?action=add-Playlist">Ajouter Playlist</a>
                    <a href="?action=auth">Authentification</a>
                    <a href="?action=register">Inscription</a>
                    <a href="?action=deconnect">Déconnexion</a>
                
            </nav>    
            </header>
            $content
        </body>
        </html> 
        FIN;
    }


}