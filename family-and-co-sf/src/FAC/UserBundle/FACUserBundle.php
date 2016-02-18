<?php

namespace FAC\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class FACUserBundle extends Bundle
{
	public function getParent()
    {
        return 'FOSUserBundle';
    }
}
