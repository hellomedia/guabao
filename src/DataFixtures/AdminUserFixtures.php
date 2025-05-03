<?php

namespace App\DataFixtures;

use App\DataFixtures\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AdminUserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::createOne([
            'name' => 'Admin',
            'email' => 'admin@guabao.be',
            'roles' => ['ROLE_SUPER_ADMIN'],
            'password' => 'test',
        ]);
    }
}
