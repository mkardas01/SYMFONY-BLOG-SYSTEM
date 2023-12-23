<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    protected $paginator;
    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Post::class);
        $this->paginator = $paginator;
    }

    public function findAllPosts(int $page)
    {
        $dbquery = $this->createQueryBuilder('p')
            ->leftJoin('p.userThatLiked', 'l')
            ->addSelect('COUNT(l) as hidden likes')
            ->orderBy('likes', 'DESC')
            ->groupBy('p')
            ->getQuery()
            ->getResult();

        return $this->paginator->paginate($dbquery, $page, 3);

    }

    public function findAllUserPosts(int $page, $userID){

        $dbquery = $this -> createQueryBuilder('p')
            ->leftJoin('p.userThatLiked', 'l')
            ->addSelect('COUNT(l) as hidden likes')
            ->orderBy('likes', 'DESC')
            ->groupBy('p')
            ->where('p.user = :id')
            ->setParameter('id', $userID)
            ->getQuery()
            ->getResult();

        return $this->paginator->paginate($dbquery, $page, 3);
    }

    public function searchPosts(string $query){

        $querybuilder = $this ->createQueryBuilder('p');
        $searchTerms = $this->prepareQuery($query);

        foreach ($searchTerms as $key => $term){
            $querybuilder
                -> orWhere('p.title LIKE :t_'.$key)
                -> orWhere('p.content LIKE :t_'.$key)
                -> setParameter('t_'.$key, '%'.trim($term).'%');
        }

        $dbquery = $querybuilder
            ->select('p.title','p.id')
            ->getQuery()
            ->getResult();

        return $dbquery;

    }
    public function prepareQuery(string $query): array {

        $terms = array_unique(explode(' ', $query));

        return array_filter($terms, function ($term){
            return mb_strlen($term) >= 2;
        });

    }

    /**
     * @throws NonUniqueResultException
     */
    public function isLiked($authUser, $postId): bool
    {
        return $this->createQueryBuilder('p')
            ->select('p.id')
            ->andWhere('p.id = :postId')
            ->andWhere('userThatLiked.id = :authUser')
            ->innerJoin('p.userThatLiked', 'userThatLiked')
            ->setParameter('authUser', $authUser)
            ->setParameter('postId', $postId)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult() != null;


    }

    /**
     * @throws NonUniqueResultException
     */
    public function isDisliked($authUser, $postId): bool
    {
        return $this->createQueryBuilder('p')
            ->select('p.id')
            ->andWhere('p.id = :postId')
            ->andWhere('usersThatDontLike.id = :authUser')
            ->innerJoin('p.usersThatDontLike', 'usersThatDontLike')
            ->setParameter('authUser', $authUser)
            ->setParameter('postId', $postId)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult() != null;
    }

//    /**
//     * @return Post[] Returns an array of Post objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Post
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
