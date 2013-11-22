<?php

namespace FP\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class FPUserBundle extends Bundle {

    public function getParent() {
        return 'FOSUserBundle';
    }

}
