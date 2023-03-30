<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $users = [];
        $genders = ['male', 'feminine'];
        $roles = ['ROLE_USER', 'ROLE_COMPANY', 'ROLE_FORMATION',];
        $professions = ["développeur", "intégrateur", "concepteur développeur d'application", "testeur"];

        for ($i=0; $i < 50; $i++) { 
            $user = new User();
            $gender = $faker->randomElement($genders);
            $role = $faker->randomElement($roles);
            $firstName = $faker->firstName();
            $logo = 'https://randomuser.me/api/portraits/';
            $logoId = $faker->numberBetween(1,99).'.jpg';
            

            if ($role === 'ROLE_USER') {
                $profession = $faker->randomElement($professions);
            } else {
                $profession = null;
            }

            if ($role === 'ROLE_COMPANY' || $role === 'ROLE_FORMATION') {
                $firstName = null;
            }
            

            if ($role === 'ROLE_COMPANY' || $role === 'ROLE_FORMATION') {
                $statu = $faker->randomElement(['SAS','SARL']);
            } else {
                $statu = $faker->randomElement(['DISPONIBLE', 'EN FORMATION', 'EN POSTE', 'EN RECONVERSION']);
            }

            if ($gender == 'male') {
                $logo = $logo.'men/'.$logoId;
            } else {
                $logo = $logo.'women/'.$logoId;
            }

            $user->setName($faker->lastName())
                 ->setAdress($faker->streetAddress())
                 ->setPostalCode($faker->postcode())
                 ->setCity($faker->city())
                 ->setTel($faker->phoneNumber())
                 ->setEmail($faker->email())
                 ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-2 years', 'now')))
                 ->setFirstName($firstName)
                 ->setProfession($profession)
                 ->setStatus($statu)
                 ->setPassword($this->hasher->hashPassword($user, 'password'))
                 ->setRoles([$role])
                 ->setLogo($logo);

                 $manager->persist($user);
                 $users[] = $user;
        }

        $manager->flush();
    }
}
