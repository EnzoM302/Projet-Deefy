<?php
namespace iutnc\deefy\Auth;
use iutnc\deefy\exception\AuthException;
use iutnc\deefy\repository\DeefyRepository;

class AuthProvider {

    public static function signin(string $email,string $passwd2check): void {
        $hash = DeefyRepository::getInstance()->getHashUser($email);
        if (!password_verify($passwd2check, $hash))
            throw new AuthException("AUTH ERROR");
        $_SESSION['user'] = $email;
    }
    public static function register(string $email,string $pass): void {
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new AuthException("REGISTER ERROR");
        }
        if (DeefyRepository::getInstance()->userExists($email)) {
            throw new AuthException("REGISTER ERROR");
        }else{
            if (!self::checkPasswordStrength($pass, 10)) {
                throw new AuthException("REGISTER ERROR");
            }
            $hash = password_hash($pass, PASSWORD_DEFAULT, ['cost'=>12]);
            DeefyRepository::getInstance()->addUser($email, $hash);
            $_SESSION['user'] = $email;
        }
    }

    public static function checkPasswordStrength(string $pass,int $minimumLength): bool {
        $length = (strlen($pass) >= $minimumLength); // longueur minimale
        $digit = preg_match("#[\d]#", $pass); // au moins un digit
        $special = preg_match("#[\W]#", $pass); // au moins un car. spécial
        $lower = preg_match("#[a-z]#", $pass); // au moins une minuscule
        $upper = preg_match("#[A-Z]#", $pass); // au moins une majuscule
        if (!$length || !$digit || !$special || !$lower || !$upper) {
            return false;
        }
        return true;
    
    }
//     public static function getSignedInUser( ): User {
//         if ( !isset($_SESSION['user']))
//             throw new AuthException("Auth error : not signed in");

//         return unserialize($_SESSION['user'] ) ;
// }

}