<?php

namespace SO\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class SOUserBundle extends Bundle {

    public function getParent() {
        return 'FOSUserBundle';
    }

}
