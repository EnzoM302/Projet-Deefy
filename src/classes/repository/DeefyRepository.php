<?php

declare(strict_types=1);
namespace iutnc\deefy\repository;
use iutnc\deefy\audio\lists\Playlist;
use PDO;

class DeefyRepository{
    private \PDO $pdo;
    private static ?DeefyRepository $instance = null;
    private static array $config = [ ];

    private function __construct(array $conf) {
        $this->pdo = new \PDO($conf['dsn'], $conf['user'], $conf['pass'],
        [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
    }
    public static function getInstance(){
        if (is_null(self::$instance)) {
        self::$instance = new DeefyRepository(self::$config);
        }
        return self::$instance;
    }
    public static function setConfig(string $file) {
        $conf = parse_ini_file($file);
        if ($conf === false) {
        throw new \Exception("Error reading configuration file");
        }
        $driver = $conf['driver'];
        $host = $conf['host'];
        $database = $conf['database'];
        self::$config = [ 
            'dsn'=> "$driver:host=$host;dbname=$database;charset=utf8mb4",
            'user'=> $conf['username'],
            'pass'=> $conf['password'] ];
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


}