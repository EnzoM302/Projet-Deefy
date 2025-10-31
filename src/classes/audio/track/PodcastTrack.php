<?php

namespace iutnc\deefy\audio\track;

use iutnc\deefy\exception\InvalidPropertyValueException;

class PodcastTrack extends AudioTrack{
    protected string $date;

    public function __construct(string $titre, string $auteur, string $chemin, int $duree, string $genre, string $date){
        if ($duree <0) {
            throw new InvalidPropertyValueException("Erreur durée à 0");
        }
        parent::__construct($titre, $chemin, $duree, $auteur, '', $genre);
        $this->artiste = $auteur;
        $this->genre = $genre;
        $this->date = $date;
    }

}