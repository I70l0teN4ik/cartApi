<?php

namespace ApiBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

/**
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\CartRepository")
 * @ORM\Table(name="cart")
 */
class Cart
{
    const MAX_PRODUCTS = 3;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="Product")
     */
    private $products;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $created;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->created = new \DateTime();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Cart
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Add product
     *
     * @param Product $product
     * @return Cart
     * @throws ConflictHttpException
     */
    public function addProduct(Product $product)
    {
        if (sizeof($this->products) == self::MAX_PRODUCTS) {
            throw new ConflictHttpException('Reached maximum number of products per cart.');
        }
        $this->products[] = $product;

        return $this;
    }

    /**
     * Remove product
     *
     * @param Product $product
     *
     * @return Cart
     */
    public function removeProduct(Product $product)
    {
        $this->products->removeElement($product);

        return $this;
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Get Products total price
     *
     * @return float
     */
    public function getTotalPrice()
    {
        $totalPrice = 0;
        /** @var Product $product */
        foreach ($this->products as $product) {
            $totalPrice += $product->getPrice();
        }
        return $totalPrice;
    }
}
