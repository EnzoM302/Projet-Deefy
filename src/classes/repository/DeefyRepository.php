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

    public function getUserIdByEmail(string $email): ?int {
        $query = "SELECT id FROM User WHERE email = :email";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['id'] : null;
    }
    
    public function saveEmptyPlaylist(Playlist $pl, string $userEmail): Playlist {
        $userId = $this->getUserIdByEmail($userEmail);
        if ($userId === null) {
            //Ne doit pas arriver
            return null;
        }

        try {
            $this->pdo->beginTransaction();

            $queryPl = "INSERT INTO playlist (nom) VALUES (:nom)";
            $stmtPl = $this->pdo->prepare($queryPl);
            $stmtPl->execute(['nom' => $pl->nom]);
            $playlistId = (int) $this->pdo->lastInsertId();
            $pl->setID($playlistId); 

            $queryLink = "INSERT INTO user2playlist (id_user, id_pl) VALUES (:id_user, :id_pl)";
            $stmtLink = $this->pdo->prepare($queryLink);
            $stmtLink->execute(['id_user' => $userId, 'id_pl' => $playlistId]);

            $this->pdo->commit();

            return $pl;

        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            return null;
        }
        
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

     public function getAllTrack(): array {
            $query = "SELECT * FROM track WHERE type = 'A'";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $tracks = [];
            foreach ($results as $row) {
                $track = new AlbumTrack($row['titre'], $row['filename'],$row['titre_album'],(int)$row['numero_album'],(int)$row['duree'],(int)$row['numero_album'],$row['artiste_album'],(int)$row['annee_album'],$row['genre']);
                $tracks[] = $track;
            }
            return $tracks;
     }
     public function getAllPodcast(): array {
            $query = "SELECT * FROM track WHERE type = 'P'";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $tracks = [];
            foreach ($results as $row) {
                $track = new PodcastTrack($row['titre'], $row['auteur_podcast'],$row['filename'] ,(int)$row['duree'],$row['genre'],$row['date_posdcast']);
                $tracks[] = $track;
            }
            return $tracks;
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
                    $track = new PodcastTrack($row['titre'], $row['auteur_podcast'],$row['filename'] ,(int)$row['duree'],$row['genre'],$row['date_posdcast']);
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
    if (self::getInstance()->verifTrackExist($track)) {
        $track_id = self::getInstance()->getIdTrack($track);
        $playlist = self::getInstance()->getTrackPlaylist($id_pl, $this->getNomPlaylist($id_pl));
        $query2 = "INSERT INTO playlist2track (id_pl, id_track, no_piste_dans_liste) VALUES (:id_pl, :id_track, :no_piste_dans_liste)";
        $stmt2 = $this->pdo->prepare($query2);
        $stmt2->execute(['id_pl' => $id_pl, 'id_track' => $track_id, 'no_piste_dans_liste' => $playlist->getNextAlbumTrackNumber()]);
        return;
    }
    $stmt->execute($params);

    $track_id = (int) $this->pdo->lastInsertId();
    $query2 = "INSERT INTO playlist2track (id_pl, id_track) VALUES (:id_pl, :id_track)";
    $stmt2 = $this->pdo->prepare($query2);
    $stmt2->execute(['id_pl' => $id_pl, 'id_track' => $track_id]);
}
    public function verifTrackExist(AudioTrack $track): bool {
        $query = "SELECT COUNT(*) as count FROM track WHERE filename = :filename";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['filename' => $track->nomFichier]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($result['count'] > 0);
    }

    public function SupprimerTrackPlaylist(int $id_pl, int $id_track): void {
            $query = "DELETE FROM playlist2track WHERE id_pl = :id_pl AND id_track = :id_track";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(['id_pl' => $id_pl, 'id_track' => $id_track]);
    }

    public function getTrack(int $id_track): ?AudioTrack {
            $query = "SELECT * FROM track WHERE id = :id_track LIMIT 1";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(['id_track' => $id_track]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                if ($row['type'] === 'P') {
                    return new PodcastTrack($row['titre'], $row['auteur_podcast'],$row['filename'] ,(int)$row['duree'],$row['genre'],$row['date_posdcast']);
                } else {
                    return new AlbumTrack($row['titre'], $row['filename'],$row['titre_album'],(int)$row['numero_album'],(int)$row['duree'],(int)$row['numero_album'],$row['artiste_album'],(int)$row['annee_album'],$row['genre']);
                }
            }
            return null;
    }

    public function getIdTrack(AudioTrack $track): ?int {
            $query = "SELECT id FROM track WHERE titre = :titre AND filename = :filename LIMIT 1";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(['titre' => $track->titre, 'filename' => $track->nomFichier]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? (int)$result['id'] : null;
    }

    public function getAllTrackPlaylist(int $id_pl): array {
        $query = "SELECT track.*
                  FROM track 
                  JOIN playlist2track ON track.id = playlist2track.id_track
                  WHERE playlist2track.id_pl = :id_pl";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id_pl' => $id_pl]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $tracks = [];
        foreach ($results as $row) {
            if ($row['type'] === 'P') {
                $track = new PodcastTrack($row['titre'], $row['auteur_podcast'],$row['filename'] ,(int)$row['duree'],$row['genre'],$row['date_posdcast']);
            } else {
                $track = new AlbumTrack($row['titre'], $row['filename'],$row['titre_album'],(int)$row['numero_album'],(int)$row['duree'],(int)$row['numero_album'],$row['artiste_album'],(int)$row['annee_album'],$row['genre']);
            }
            $tracks[] = $track;
        }
        return $tracks;
    }
}
