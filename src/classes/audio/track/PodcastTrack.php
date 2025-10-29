<?php

namespace iutnc\deefy\audio\track;

use iutnc\deefy\exception\InvalidPropertyValueException;

class PodcastTrack extends AudioTrack{

    public function __construct(string $titre, string $auteur, string $chemin, int $duree ){
        if ($duree <0) {
            throw new InvalidPropertyValueException("Erreur durée à 0");
        }
        parent::__construct($titre, $chemin, $duree);
        $this->artiste = $auteur;
    }

}