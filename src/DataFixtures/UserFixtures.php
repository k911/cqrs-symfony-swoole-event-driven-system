<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Application\Document\UserDocument;
use App\Domain\User\Event\UserCreated;
use App\Domain\User\User;
use App\Domain\User\UserEmail;
use App\Domain\User\UserEventNormalizerInterface;
use App\Domain\User\UserEventStore;
use App\Domain\User\UserId;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;
    private $normalizer;

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        UserEventNormalizerInterface $normalizer
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->normalizer = $normalizer;
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
            UserId::fromString($adminDocument->id),
            new UserEmail($adminDocument->email),
            $adminDocument->passwordHash,
            $adminDocument->roles
        );

        $event = new UserCreated(
            $adminDocument->id,
            $adminDocument->email,
            $adminDocument->passwordHash,
            $adminDocument->roles
        );

        $userEvent = UserEventStore::fromEvent($event, $admin, $this->normalizer);

        $manager->persist($admin);
        $manager->persist($userEvent);
        $manager->flush();
    }
}
