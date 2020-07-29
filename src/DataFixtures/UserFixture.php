<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        $user = new User();
        $user
            ->setUsername('daniel')
            ->setPassword('$argon2id$v=19$m=65536,t=4,p=1$cVl6VWF4MnZvelp6VVd6NQ$XCXKhAZRLV+1ImSxGKOPabNroplGfNnXsd0GT8D4/CE');
        
        $manager->persist($user);
        $manager->flush();
    }
}
