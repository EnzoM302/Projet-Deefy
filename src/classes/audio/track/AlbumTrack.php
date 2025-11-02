<?php

namespace iutnc\deefy\audio\track;

use iutnc\deefy\exception\InvalidPropertyValueException;
class AlbumTrack extends AudioTrack{

    protected string $album;
    protected int $numeroPiste;
    protected int $numeroAlbum;
    public function __construct(string $titre, string $chemin,string $album, int $num, int $duree, int $numeroAlbum, string $artiste, int $annee, string $genre){
        if ($duree <0) {    
            throw new InvalidPropertyValueException("Erreur durée à 0");
            
        }
        parent::__construct($titre, $chemin, $duree, $artiste, $annee, $genre);
        $this->numeroPiste = $num;
        $this->album = $album;
        $this->numeroAlbum = $numeroAlbum;
    }
    public function setTrackNumber(int $num): void {
        $this->numeroPiste = $num;
    }


}