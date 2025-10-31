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
            $query = "SELECT *
                      FROM track 
                      JOIN playlist2track ON track.id = playlist2track.id_track
                      WHERE playlist2track.id_pl = :id_pl";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(['id_pl' => $id_pl]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $tracks = [];
            $playlist = new Playlist($nom, $tracks);
            foreach ($results as $row) {
                if ($row['type'] === 'P') {
                    $track = new PodcastTrack($row['titre'], $row['filename'],$row['auteur_podcast'] ,(int)$row['duree'],$row['genre'],$row['date_posdcast']);
                } else {
                    $track = new AlbumTrack($row['titre'], $row['filename'],$row['titre_album'],$playlist->getNextAlbumTrackNumber(),(int)$row['duree'],(int)$row['numero_album'],$row['artiste_album'],(int)$row['annee_album'],$row['genre']);
                }
                $playlist->ajouter($track);
            }
            $_SESSION['playlists'] = $playlist;
            return $playlist;
     }

     public function getNomPlaylist(int $id_pl): string {
            $query = "SELECT nom FROM playlist WHERE id = :id_pl";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(['id_pl' => $id_pl]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['nom'];
     }

    public function AjouterTrackPlaylist(int $id_pl, AudioTrack $track): void {

    $query = "INSERT INTO track (titre, genre, duree, filename, type, artiste_album, titre_album, annee_album, numero_album, auteur_podcast, date_posdcast)
              VALUES (:titre, :genre, :duree, :filename, :type, :artiste_album, :titre_album, :annee_album, :numero_album, :auteur_podcast, :date_posdcast)";

    $stmt = $this->pdo->prepare($query);

    $type = $track instanceof PodcastTrack ? 'P' : 'A';

    $params = [
        'titre' => $track->titre,
        'genre' => $track->genre,
        'duree' => $track->duree,
        'filename' => $track->nomFichier,
        'type' => $type,
        'artiste_album' => null,
        'titre_album' => null,
        'annee_album' => null,
        'numero_album' => null,
        'auteur_podcast' => null,
        'date_posdcast' => null
    ];

    if ($track instanceof AlbumTrack) {
        $params['artiste_album'] = $track->artiste;
        $params['titre_album'] = $track->album;
        $params['annee_album'] = $track->annee;
        $params['numero_album'] = $track->numeroPiste;
    } else {
        $params['auteur_podcast'] = $track->artiste;
        $params['date_posdcast'] = $track->date;
    }

    $stmt->execute($params);

    $track_id = (int) $this->pdo->lastInsertId();
    $query2 = "INSERT INTO playlist2track (id_pl, id_track) VALUES (:id_pl, :id_track)";
    $stmt2 = $this->pdo->prepare($query2);
    $stmt2->execute(['id_pl' => $id_pl, 'id_track' => $track_id]);
}


     

// id
// titre
// genre
// duree
// filename
// type
// artiste_album
// titre_album
// annee_album
// numero_album
// auteur_podcast
// date_posdcast

}