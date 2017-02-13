<?php

namespace ApiBundle\Manager;


use ApiBundle\Entity\Cart;
use ApiBundle\Entity\Product;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CartManager
{
    private $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * Create new card. Add product to created cart if specified.
     *
     * @param int $prodId
     * @return Cart
     */
    public function createCart($prodId = 0)
    {
        $cart = new Cart();
        /** @var Product $product */
        $product = $this->em->getRepository(Product::class)->find($prodId);
        if ($product) {
            $cart->addProduct($product);
        }

        $this->em->persist($cart);
        $this->em->flush();

        return $cart;
    }

    /**
     * Silently removes cart
     *
     * @param Cart $cart
     * @return self
     */
    public function deleteCart(Cart $cart = null)
    {
        if ($cart) {
            $this->em->remove($cart);
            $this->em->flush();
        }

        return $this;
    }

    /**
     * @param Cart $cart
     * @param int $prodId
     * @param string $action
     * @return Cart
     * @throws \Exception
     */
    public function editCart(Cart $cart, $prodId, $action)
    {
        /** @var Product $product */
        $product = $this->em->getRepository(Product::class)->find($prodId);

        if (!$product) {
            throw new NotFoundHttpException(sprintf('Product %s not found', $prodId));
        }

        if ($product && $action == 'add') {
            if (in_array($product, $cart->getProducts()->toArray())) {
                throw new NotAcceptableHttpException(sprintf('Product %s already added to cart', $prodId));
            }
            $cart->addProduct($product);
        } elseif ($product && $action == 'remove') {
            $cart->removeProduct($product);
        }

        $this->em->flush();

        return $cart;
    }
}