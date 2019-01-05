<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Domain\User\User;
use App\Domain\User\UserEmail;
use App\Infrastructure\Uuid\RamseyUuidUserId;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
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
        $admin = new User(
            RamseyUuidUserId::fromUuid4(),
            new UserEmail('super@admin.pl'),
            '',
            [
                'ROLE_ADMIN',
            ]
        );
        $admin->setPassword($this->passwordEncoder->encodePassword($admin, 'password'));
        $manager->persist($admin);
        $manager->flush();
    }
}
