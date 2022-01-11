<?php

namespace App\Repository;

use App\Entity\Product;
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
    private $hasher;
    private $productRepository;

    public function __construct(ManagerRegistry $registry, UserPasswordHasherInterface $hasher, ProductRepository $productRepository)
    {
        $this->hasher = $hasher;
        $this->productRepository = $productRepository;

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


    /*
    * Création User
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

    /*
    * Ajout produit aux favoris du User
    */
    public function addFavorite($productDatas, User $user)
    {
        // Si le produit existe, on le récupère

        $product = $this->productRepository->findOneBy(['ean' => $productDatas['ean']]);

        if (!$product) {
            $product = new Product();
            $product->setName($productDatas['name'])
                ->setEan($productDatas['ean'])
                ->setBrand($productDatas['brand']);

            $this->_em->persist($product);
            $this->_em->flush();
        }

        $user->addFavoritesProduct($product);

        $this->_em->persist($user);
        $this->_em->flush();

        return true;
    }

    /*
    * Supprime un produit des favoris du User
    */
    public function removeFavorite($codeEan, User $user)
    {
        if ($user->getFavoritesProducts()->count() > 0) {
            foreach ($user->getFavoritesProducts() as $product) {
                if ($product->getEan() === $codeEan) {
                    $user->removeFavoritesProduct($product);

                    $this->_em->persist($user);
                    $this->_em->flush();

                    return true;
                }
            }
        }

        return false;
    }

    /*
    * Supprime les produits favoris du User
    */
    public function clearFavorites(User $user)
    {
        if ($user->getFavoritesProducts()->count() > 0) {
            foreach ($user->getFavoritesProducts() as $product) {

                $user->removeFavoritesProduct($product);

                $this->_em->persist($user);
                $this->_em->flush();
            }
        }

        return true;
    }

    /*
    * Ajout produit aux produits exclus du User
    */
    public function addExcluded($productDatas, User $user)
    {
        $product = $this->productRepository->findOneBy(['ean' => $productDatas['ean']]);

        if (!$product) {
            $product = new Product();
            $product->setName($productDatas['name'])
                ->setEan($productDatas['ean'])
                ->setBrand($productDatas['brand']);

            $this->_em->persist($product);
            $this->_em->flush();
        }

        $user->addExcludedProduct($product);

        $this->_em->persist($user);
        $this->_em->flush();

        return true;
    }
}
