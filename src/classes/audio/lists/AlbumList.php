<?php

namespace iutnc\deefy\audio\lists;

class AlbumList extends AudioListe {
    protected string $artiste;
    protected string $date;

    public function __construct(string $nom, array $track=[], string $artiste, string $date){
        if (count($track) === 0) {
            throw new \Exception("tableau piste vide");
        }
        
        $this->artiste = $artiste;
        $this->date = $date;
        parent::__construct($nom, $track);
        
    }

    public function setArtiste(string $artiste): void {
        $this->artiste = $artiste;
    }

    public function setdate(string $date): void {
        $this->date = $date;
    }
}