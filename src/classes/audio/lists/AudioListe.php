<?php

namespace iutnc\deefy\audio\lists;


class AudioListe{
    protected string $nom;
    protected int $nbPistes;
    protected int $dureeTot;
    private int $id;

    protected array $track;

    public function __construct(string $nom, array $track=[]){
        $this->track = $track;
        $this->nom = $nom;
        $this->nbPistes = count($track);
        $this->dureeTot = $this->calculerDuree();
    }

    public function calculerDuree():int{
        $total = 0;
        foreach ($this->track as $piste) {
            $total += $piste->duree;
        }
        return $total;
    }

    public function setID(int $id):void{
        $this->id = $id;
    }

    public function __get(string $name): mixed{
        if (property_exists ($this, $name)) return $this->$name;
        throw new \InvalidArgumentException;
    }

}