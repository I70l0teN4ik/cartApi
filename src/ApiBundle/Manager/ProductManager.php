<?php

namespace ApiBundle\Manager;


use ApiBundle\Entity\Product;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductManager
{
    private $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @param string $name
     * @param float $price
     * @return Product
     */
    public function createProduct($name, $price)
    {
        $product = new Product();
        $product->setName($name);
        $product->setPrice($price);

        $this->em->persist($product);
        $this->em->flush();

        return $product;
    }

    /**
     * Silently removes product
     *
     * @param Product $product
     * @return self
     */
    public function deleteProduct(Product $product = null)
    {
        if ($product) {
            $this->em->remove($product);
            $this->em->flush();
        }

        return $this;
    }

    /**
     * @param Product $product
     * @param string $name
     * @param float $price
     * @return Product
     */
    public function editProduct(Product $product, $name, $price)
    {
        if (false !== $name) {
            $product->setName($name);
        }
        if (false !== $price) {
            $product->setPrice(floatval($price));
        }

        $this->em->flush();

        return $product;
    }

    public function getProductsList($page = 1, $sort = null)
    {
        return $this->em->getRepository(Product::class)->getPaginatedList($page, $sort)->getResult();
    }

    public function getProductsCount()
    {
        return $this->em->getRepository(Product::class)->countProducts()->getSingleScalarResult();
    }
}