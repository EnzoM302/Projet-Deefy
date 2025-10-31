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
            <title>Deefy App</title>
        </head>
        <body>
            <ul>
                <li><a href="?action=default">Home</a></li>
                <li><a href="?action=playlist">Mes Playlists</a></li>
                <li><a href="?action=add-Playlist">Add-Playlist</a></li>
                <li><a href="?action=add-Track">Add-Podcast</a></li>
                <li><a href="?action=auth">Authentification</a></li>
                <li><a href="?action=register">Inscription</a></li>
                <li><a href="?action=deconnect">Déconnexion</a></li>
            </ul>
            $content
        </body>
        </html> 
        FIN;
    }


}