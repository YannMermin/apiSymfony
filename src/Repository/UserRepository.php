<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry, private UserPasswordHasherInterface $hasher)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }


    /* @return User[] Returns an array of User objects
    *
    */
    public function create($datas)
    {
        $checkExistsUser = ($this->findOneBy(['email' => $datas->email]));

        if ($checkExistsUser) {
            return [
                'error' => true,
                'message' => 'Cette adresse email est déjà utilisée'
            ];
        }

        $user = new User();
        $user->setName($datas->name)
            ->setEmail($datas->email);

        $password = $this->hasher->hashPassword($user, $datas->password);

        $user->setPassword($password);

        $this->_em->persist($user);
        $this->_em->flush();

        return [
            'error' => false,
            'user' => $user
        ];
    }
}
