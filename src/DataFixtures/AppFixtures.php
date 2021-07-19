<?php
declare(strict_types = 1);

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Debt;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }


    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail('user@nomail.com');
        $password = $this->encoder->encodePassword($user, 'user');
        $user->setPassword($password);
        $manager->persist($user);

        for ($i = 1; $i < 30; $i++) {
            $d = new Debt();
            $d->setSymbol("SYMBOL " . ($i * 10));
            $d->setValue(sprintf("%.2F", round($i * 12 * 1.3315, 2)));
            $d->setDate(new \DateTime("$i days ago"));
            $manager->persist($d);
        }
        for ($i = 1; $i < 30; $i++) {
            $d = new Debt();
            $d->setSymbol("SYMBOL " . ($i * 10 + 1));
            $d->setValue(sprintf("%.2F", round($i * 10 * 1.3327, 2)));
            $d->setDate(new \DateTime("+$i day"));
            $manager->persist($d);
        }

        $manager->flush();
    }
}
