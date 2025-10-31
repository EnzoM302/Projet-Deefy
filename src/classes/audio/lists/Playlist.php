<?php

namespace iutnc\deefy\audio\lists;

use iutnc\deefy\audio\track\AudioTrack;

class Playlist extends AudioListe   {

        private int $nextTrack = 0;

        public function __construct(String $nom, array $track = [])
        {
            parent::__construct($nom, $track);
        }

        public function ajouter(AudioTrack $track){
            foreach ($this->track as $t) {
                if ($t->titre == $track->titre && $t->artiste == $track->artiste) {
                    return;
                }
            }
            $this->track[] = $track;
            $this->nbPistes ++;
            $this->dureeTot += $track->duree; 
            
        }

        public function supprimer(int $place){
            if ($place >= 0 && $place < count($this->track)) {
                $piste = $this->track[$place];
                unset($this->track[$place]);
                $this->track = array_values($this->track);
                $this->nbPistes--;
                $this->dureeTot -= $piste;
                $this->nextTrack--;
            }
        }

        public function ajouterPistes(array $pistes){
            foreach ($pistes as $key => $value) {
                if (!in_array($pistes, $this->track,true)) {
                    $this->track[] = $value;
                    $this->nbPistes ++;
                    $this->dureeTot += $value->duree;
                }
            }
        }


        public function __toString(){
            $res = "";
            foreach ($this->track as $key => $value) {
              $res .= $value->__toString()."<br>";
            }
            return $res;
        }

        public function getNextAlbumTrackNumber(): int {
            $this->nextTrack++;
            return $this->nextTrack;
        }
    
}
