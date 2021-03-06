<?php

namespace App\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Entity\User;

/**
 * Class LoadUserData
 */
class LoadUserData implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('admin');
        //$user->setSalt(md5(uniqid()));

        $encoder  = $this->container->get('security.password_encoder');
        $password = $encoder->encodePassword($user, 'test');

        $user->setPassword($password);
        $user->setRole('ROLE_ADMIN');
        $user->setEmail('admin@example.org');

        $manager->persist($user);
        $manager->flush();
    }
}
