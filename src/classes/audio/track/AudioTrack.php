<?php

namespace iutnc\deefy\audio\track;

use iutnc\deefy\exception\InvalidProprietyException;

class AudioTrack{
    protected string $titre;
    protected string $artiste;
    protected int $annee;
    protected string $genre;
    protected int $duree;
    protected string $nomFichier;

    public function __construct(string $titre, string $nomFichier, int $duree){
        $this->titre = $titre;
        $this->nomFichier = $nomFichier;
        $this->duree = $duree;
    }


    public function __get(string $name): mixed{
        if (property_exists ($this, $name)) return $this->$name;
        throw new InvalidProprietyException("Erreur proprieté invalide");
    }

    public function setArtiste(string $artiste): void{
        $this->artiste = $artiste;
    }


    public function __tostring(){
        return json_encode(get_object_vars($this));
    }

}