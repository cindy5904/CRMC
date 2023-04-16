<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\Formation;
use App\Entity\Publication;
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
        $posts = [];
        $genders = ['male', 'female'];
        $roles = ['ROLE_USER', 'ROLE_COMPANY', 'ROLE_FORMATION'];
        $professions = ["développeur", "intégrateur", "concepteur développeur d'application", "testeur"];
        $postTitles = ['Développeur Web', 'Designer UX', 'Analyste de Données', 'Chef de Projet IT', 'Ingénieur Logiciel', 'Spécialiste en Sécurité Informatique', 'Architecte Cloud', 'Administrateur de Bases de Données', 'Développeur Mobile', 'Expert en Réseaux Informatiques'];
        $types = ['stage', 'alternance', 'emploi'];
        $typesF = ['formation', 'alternance'];

        $user = new User();
        $user->setName('Mélanie');
        $user->setEmail('melanie@live.fr');
        $user->setPassword($this->hasher->hashPassword($user, 'crmc'));
        $user->setRoles(['ROLE_ADMIN']);
        $manager->persist($user);

        $user = new User();
        $user->setName('Cindy');
        $user->setEmail('cindy@live.fr');
        $user->setPassword($this->hasher->hashPassword($user, 'crmc'));
        $user->setRoles(['ROLE_ADMIN']);
        $manager->persist($user);

        $user = new User();
        $user->setName('Coline');
        $user->setEmail('coline@live.fr');
        $user->setPassword($this->hasher->hashPassword($user, 'crmc'));
        $user->setRoles(['ROLE_ADMIN']);
        $manager->persist($user);

        $user = new User();
        $user->setName('Romain');
        $user->setEmail('romain@live.fr');
        $user->setPassword($this->hasher->hashPassword($user, 'crmc'));
        $user->setRoles(['ROLE_ADMIN']);
        $manager->persist($user);
        
        for ($i = 0; $i < 50; $i++) {
            $user = new User();
            $gender = $faker->randomElement($genders);
            $role = $faker->randomElement($roles);
            $firstName = $faker->firstName();
            $logo = 'https://randomuser.me/api/portraits/';
            $logoId = $faker->numberBetween(1, 99) . '.jpg';
            $company = new Company();
            $formation = new Formation();
            $type = $faker->randomElement($types);
            $typeF = $faker->randomElement($typesF);

            if ($role === 'ROLE_USER') {
                $profession = $faker->randomElement($professions);
            } else {
                $profession = null;
            }

            if ($role === 'ROLE_COMPANY' || $role === 'ROLE_FORMATION') {
                $firstName = null;
            }


            if ($role === 'ROLE_COMPANY' || $role === 'ROLE_FORMATION') {
                $statu = $faker->randomElement(['SAS', 'SARL']);
            } else {
                $statu = $faker->randomElement(['DISPONIBLE', 'EN FORMATION', 'EN POSTE', 'EN RECONVERSION']);
            }

            if ($gender == 'male') {
                $logo = $logo . 'men/' . $logoId;
                $firstName = $faker->firstNameMale();
            } else {
                $logo = $logo . 'women/' . $logoId;
                $firstName = $faker->firstNameFemale();
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
                if($role ==='ROLE_COMPANY'){
                $user->setUserEntreprise($company);
                }elseif ($role === 'ROLE_FORMATION') {
                    $user->setUserFormation($formation);
                }
            $manager->persist($user);

            if($user->getUserEntreprise() != null){
                $company->setNumSiret(12345678912345)
                    ->setNameRef($faker->lastName())
                    ->setDescription($faker->text(700))
                    ->setDomaine('IT')
                    ->setLogo($logo)
                    ->setPartenaire($faker->boolean())
                    ->setWebSite("url")
                    ->setName($user->getName());
                $manager->persist($company);
            }
            if($user->getUserFormation() != null){
                $formation
                    ->setNumSiret(12345678912345)
                    ->setNameRef($faker->name())
                    ->setDescription($faker->text(700))
                    ->setDomain('Métiers de l\'IT')
                    ->setWebSite($faker->url());
                $manager->persist($formation);
            }

            for($j = 1; $j <= rand(2, 5); $j++){
                $post = new Publication();
                $post->setContent($faker->text(700))
                    ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-2 years')))
                    ->setTitle($faker->randomElement($postTitles))
                    ->setPublicationUser($user);
                    if($user->getUserEntreprise() != null){
                        $post->setType($faker->randomElement($types));
                        $post->setPublicationCompany($company);
                    } elseif($user->getUserFormation() != null){
                        $post->setType($typeF);
                        $post->setPublicationFormation($formation);
                    } else $post->settype('A définir');
                $manager->persist($post);
                $posts[] = $post;
            }
            $users[] = $user;
        }
        $manager->flush();
    }
}
