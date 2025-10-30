<?php

namespace iutnc\deefy\action;

class DeconnectAction extends Action {

    public function __invoke() : string {
        $mail = $_SESSION['email'];
        session_destroy();
        return "<p>Vous avez été déconnecté avec succès {$mail}.</p><a href='?action=auth'>Se reconnecter</a>";
    }

}