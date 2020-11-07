<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $userOne = new User();
        $userOne->setUserName('muratozturk');
        $userOne->setEmail('murat@boom.com');
        $userOne->setPassword($this->encoder->encodePassword($userOne, '121221'));
        $userOne->setRoles(['ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_USER']);
        $manager->persist($userOne);

        $userTwo = new User();
        $userTwo->setUserName('dervisgelmez');
        $userTwo->setEmail('dervis@boom.com');
        $userTwo->setPassword($this->encoder->encodePassword($userTwo, '121221'));
        $userTwo->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $manager->persist($userTwo);

        $userThree = new User();
        $userThree->setUserName('mertavci');
        $userThree->setEmail('mert@boom.com');
        $userThree->setPassword($this->encoder->encodePassword($userThree, '121221'));
        $userThree->setRoles(['ROLE_USER']);
        $manager->persist($userThree);

        $manager->flush();
    }
}