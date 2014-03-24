<?php

namespace Poker\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class PokerUserBundle extends Bundle {

    public function getParent() {
        return 'FOSUserBundle';
    }

}
