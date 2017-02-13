<?php

namespace ApiBundle\Repository;

use ApiBundle\Entity\Product;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class ProductRepository extends EntityRepository
{
    /**
     * @param int $page
     * @param string $sort
     * @return Query
     */
    public function getPaginatedList($page = 1, $sort = null)
    {
        $offset = --$page * Product::PER_PAGE;

        $qb = $this->createQueryBuilder('p');
        $qb ->setMaxResults(Product::PER_PAGE)
            ->setFirstResult($offset)
        ;

        if ($sort) {
            $sort = preg_replace('/^-/', '', $sort, 1, $count);
            $direction = ($count == 0) ? 'ASC' : 'DESC';

            // validate sorting param
            if (in_array($sort, ['name', 'price', 'created'])) {
                $qb->orderBy('p.'.$sort, $direction);
                $qb->addOrderBy('p.id');
            }
        }

        return $qb->getQuery();
    }

    public function countProducts()
    {
        $qb = $this->createQueryBuilder('p')->select("COUNT(p.id)");

        return $qb->getQuery();
    }
}
