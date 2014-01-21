<?php

namespace VM\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class VMUserBundle extends Bundle {

    public function getParent() {
        return 'FOSUserBundle';
    }

}
