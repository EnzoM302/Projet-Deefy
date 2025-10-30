<?php

declare(strict_types=1);
namespace iutnc\deefy\repository;
use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\audio\track\AudioTrack;
use iutnc\deefy\audio\track\AlbumTrack;
use iutnc\deefy\audio\track\PodcastTrack;
use PDO;

class DeefyRepository{
    private PDO $pdo;
    private static ?DeefyRepository $instance = null;
    private static array $config;

    private function __construct(array $conf) {
        $this->pdo = new PDO(
            $conf['dsn'], 
            $conf['user'], 
            $conf['pass'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }

    public static function getInstance(): DeefyRepository {
        if (self::$instance === null) {
            if (empty(self::$config)) {
                throw new \Exception("Config not set");
            }
            self::$instance = new DeefyRepository(self::$config);
        }
        return self::$instance;
    }

    public static function setConfig(string $file): void {
        $conf = parse_ini_file($file);
        if ($conf === false) {
            throw new \Exception("Error reading configuration file");
        }
        self::$config = [
            'dsn' => "{$conf['driver']}:host={$conf['host']};dbname={$conf['database']};charset=utf8mb4",
            'user' => $conf['username'],
            'pass' => $conf['password']
        ];
    }
    // public function findPlaylistById(int $id): Playlist {
    //     return new Playlist();
    // }
    public function saveEmptyPlaylist(Playlist $pl): Playlist {
            $query = "INSERT INTO playlist (nom) VALUES (:nom)";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(['nom' => $pl->nom]);
            $pl->setID((int) $this->pdo->lastInsertId());
            return $pl;
        
     }
    public function getHashUser(String $email): ?String {
            $query = "SELECT passwd FROM User WHERE email = :email";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(['email' => $email]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (isset($result['passwd'])) ? $result['passwd']:null;

     }

     public function addUser(string $email, string $hash): void {
            $query = "INSERT INTO User (email, passwd) VALUES (:email, :passwd)";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(['email' => $email, 'passwd' => $hash]);
     }

     public function userExists(string $email): bool {
            $query = "SELECT COUNT(*) as count FROM User WHERE email = :email";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(['email' => $email]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return ($result['count'] > 0);
     }

     public function getPlaylistsUser(string $email): String {
            $query = "SELECT playlist.id ,playlist.nom FROM playlist 
                      JOIN user2playlist ON playlist.id = user2playlist.id_pl
                      JOIN user ON user2playlist.id_user = user.id
                      WHERE user.email = :email";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(['email' => $email]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $playlists = "";
            foreach ($results as $row) {
                $playlists .= "<a href='?action=playlistRender&id={$row['id']}&nom={$row['nom']}'>{$row['nom']}</a><br>";
            }
            return $playlists;

     }

     public function getTrackPlaylist(int $id_pl, string $nom): Playlist {
            $query = "SELECT track.titre, track.filename, track.duree, track.auteur_podcast, track.titre_album
                      FROM track 
                      JOIN playlist2track ON track.id = playlist2track.id_track
                      WHERE playlist2track.id_pl = :id_pl";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(['id_pl' => $id_pl]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $tracks = [];
            $playlist = new Playlist($nom, $tracks);
            foreach ($results as $row) {
                if ($row['auteur_podcast'] !== null) {
                    $track = new PodcastTrack($row['titre'], $row['filename'],$row['auteur_podcast'] ,(int)$row['duree']);
                } else {
                    $track = new AlbumTrack($row['titre'], $row['filename'],$row['titre_album'],$playlist->getNextAlbumTrackNumber(),(int)$row['duree']);
                }
                $playlist->ajouter($track);
            }
            return $playlist;
     }

     public function getNomPlaylist(int $id_pl): string {
            $query = "SELECT nom FROM playlist WHERE id = :id_pl";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(['id_pl' => $id_pl]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['nom'];
     }

}