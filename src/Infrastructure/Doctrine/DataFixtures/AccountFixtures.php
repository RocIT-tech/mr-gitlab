<?php

namespace App\Infrastructure\Doctrine\DataFixtures;

use App\Infrastructure\Doctrine\Entity\Account;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Uid\Ulid;

class AccountFixtures extends Fixture
{
    public function __construct(
        private readonly ClockInterface $clock,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $account = new Account(
            id: (new Ulid(Ulid::generate($this->clock->now())))->toRfc4122(),
            name: 'Test account.'
        );

        $manager->persist($account);
        $manager->flush();

        $this->addReference('account', $account);
    }
}
