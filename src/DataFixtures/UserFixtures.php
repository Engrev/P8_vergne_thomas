<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserFixtures
 * @package App\DataFixtures
 */
class UserFixtures extends Fixture
{
    public const USER_REFERENCE = 'user-fixtures';

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * UserFixtures constructor.
     *
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param ObjectManager $manager
     * php bin/console doctrine:fixtures:load --append
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('johndoe')
            ->setEmail('johndoe@demo.fr')
            ->setPassword($this->passwordEncoder->encodePassword($user, 'azertyuiop'))
            ->setRoles(['ROLE_ADMIN'])
        ;
        $manager->persist($user);

        $user = new User();
        $user->setUsername('johndoe2')
            ->setEmail('johndoe2@demo.fr')
            ->setPassword($this->passwordEncoder->encodePassword($user, 'azertyuiop'))
            ->setRoles(['ROLE_USER'])
        ;
        $this->addReference(self::USER_REFERENCE, $user);
        $manager->persist($user);

        $manager->flush();
    }
}
