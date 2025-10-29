<?php

namespace iutnc\deefy\render;

use iutnc\deefy\audio\track\AudioTrack;

abstract class AudioTrackRenderer implements Renderer {
    protected AudioTrack $audio;

    public function __construct(AudioTrack $audio) {
        $this->audio = $audio;
    }

       public function render(int $selector): string {
        switch ($selector) {
            case Renderer::COMPACT:
                return $this->petit();
            case Renderer::LONG:
                return $this->grand();
            default:
                return $this->petit();
        }
    }

    public function __get(string $name): mixed{
        if (property_exists ($this, $name)) return $this->$name;
        throw new \InvalidArgumentException;
    }
    
    abstract protected function petit(): string;
    abstract protected function grand(): string;
}
