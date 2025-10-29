<?php

declare(strict_types=1);

namespace loader;

class Autoload {

    private string $prefix;
    private string $racine;

    public function __construct(String $prefix , String $racine){
        $this->prefix = $prefix;
        $this->racine = $racine;
    }

    public function loadClass(String $class) :void {
        $file = $this->racine. str_replace($this->prefix, '', $class)."/";
        $file = str_replace('\\', '/', $file) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }

    public static function register(): void {
        spl_autoload_register([new self("iutnc\\deefy", "src/"), "loadClass"]);
    }
}