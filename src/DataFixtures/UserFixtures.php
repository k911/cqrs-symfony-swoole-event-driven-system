<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Application\Document\UserDocument;
use App\Domain\User\User;
use App\Domain\User\UserEmail;
use App\Infrastructure\Uuid\RamseyUuidUserId;
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
        $adminDocument = new UserDocument();
        $adminDocument->id = Uuid::uuid4()->toString();
        $adminDocument->plainPassword = 'password';
        $adminDocument->email = 'super@admin.pl';
        $adminDocument->passwordHash = $this->passwordEncoder->encodePassword($adminDocument, $adminDocument->plainPassword);
        $adminDocument->roles = [
            'ROLE_ADMIN',
        ];

        $admin = new User(
            RamseyUuidUserId::fromString($adminDocument->id),
            new UserEmail($adminDocument->email),
            $adminDocument->passwordHash,
            $adminDocument->roles
        );

        $manager->persist($admin);
        $manager->flush();
    }
}
