<?php

namespace App\DataFixtures\Factory;

use App\Entity\User;
use App\Enum\AccountLanguage;
use DateTimeImmutable;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<User>
 */
final class UserFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
        
    }

    public static function class(): string
    {
        return User::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'accountLanguage' => AccountLanguage::FRENCH,
            'adminReviewed' => false,
            'closed' => false,
            'createdAt' => new DateTimeImmutable(),
            'enabled' => true,
            'hellbanned' => false,
            'locked' => false,
            'roles' => [],
            'verified' => true,
            'watchlist' => false,
            'whitelist' => false,
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            ->afterInstantiate(function (User $user) {
                $user->setPassword($this->userPasswordHasher->hashPassword($user, $user->getPassword()));
            });
    }
}
