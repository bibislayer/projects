<?php

//src/SO\UserBundle/Security/User/OpenIdUserManager.php

namespace SO\UserBundle\Security;

use Fp\OpenIdBundle\Model\UserManager;
use Fp\OpenIdBundle\Model\IdentityManagerInterface;
use Doctrine\ORM\EntityManager;
use SO\UserBundle\Entity\OpenIdIdentity;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\InsufficientAuthenticationException;

class OpenIdUserManager extends UserManager {

    // we will use an EntityManager, so inject it via constructor
    public function __construct(IdentityManagerInterface $identityManager, EntityManager $entityManager, $userManager) {
        parent::__construct($identityManager);
        $this->userManager = $userManager;
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $identity
     *  an OpenID token. With Google it looks like:
     *  https://www.google.com/accounts/o8/id?id=SOME_RANDOM_USER_ID
     * @param array $attributes
     *  requested attributes (explained later). At the moment just
     *  assume there's a 'contact/email' key
     */
    public function createUserFromIdentity($identity, array $attributes = array()) {
        // put your user creation logic here
        // what follows is a typical example
        // $identity = "https://www.google.com/accounts/o8/id";
        if (false === isset($attributes['contact/email'])) {
            throw new \Exception('We need your e-mail address!');
        }
        // in this example, we fetch User entities by e-mail
        $user = $this->entityManager->getRepository('SOUserBundle:User')->findOneBy(array(
            'email' => $attributes['contact/email']
                ));

        if (null === $user) {
            $user = $this->userManager->createUser();
            $user->setEnabled(true);
            $user->setPassword('');
            $user->setData($attributes, 'g');
            $this->userManager->updateUser($user);
            //throw new BadCredentialsException('No corresponding user!');
        }

        // we create an OpenIdIdentity for this User
        $openIdIdentity = new OpenIdIdentity();
        $openIdIdentity->setIdentity($identity);
        $openIdIdentity->setAttributes($attributes);
        $openIdIdentity->setUser($user);

        $this->entityManager->persist($openIdIdentity);
        $this->entityManager->flush();

        // end of example

        return $user; // you must return an UserInterface instance (or throw an exception)
    }

}