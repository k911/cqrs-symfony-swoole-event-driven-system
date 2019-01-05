<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param ObjectManager $manager
     *
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
        $admin = new User(Uuid::uuid4(), 'super@admin.pl');
        $admin->setRoles([
            'ROLE_ADMIN',
        ]);
        $admin->setPassword(
            $this->passwordEncoder->encodePassword($admin, 'password')
        );
        $manager->persist($admin);

        $manager->flush();
    }
}
