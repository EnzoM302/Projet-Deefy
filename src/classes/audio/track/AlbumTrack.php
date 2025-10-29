<?php

namespace iutnc\deefy\audio\track;

use iutnc\deefy\exception\InvalidPropertyValueException;

class AlbumTrack extends AudioTrack{

    protected string $album;
    protected int $numeroPiste;
    public function __construct(string $titre, string $chemin,string $album, int $num, int $duree){
        if ($duree <0) {
            throw new InvalidPropertyValueException("Erreur durée à 0");
            
        }
        parent::__construct($titre, $chemin, $duree);
        $this->numeroPiste = $num;
        $this->album = $album;
    }


}