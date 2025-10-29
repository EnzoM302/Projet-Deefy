<?php

namespace iutnc\deefy\action;

class DefaultAction extends Action {
    public function __invoke() : string {
            return "<h1>Bienvenu sur Deefy!</h1>";
    }
    
}
