<?php

namespace Tests\ApiBundle\Controller;

use ApiBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CartControllerTest extends WebTestCase
{
    const NAME_1 = 'TEST_PRODUCT';
    const PRICE_1 = 1.23;

    public function testCart()
    {
        $client = static::createClient();

        // create test Product
        $client->request('POST', '/product', ['name' => self::NAME_1, 'price' => self::PRICE_1]);
        $this->assertTrue($client->getResponse()->isSuccessful(), "Test Product created successfully");
        $this->assertTrue($client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
        ));

        $newProduct = json_decode($client->getResponse()->getContent(), true);
        $productId = isset($newProduct['id']) ? $newProduct['id'] : 0;

        $client->request('POST', '/cart');
        $this->assertTrue($client->getResponse()->isSuccessful(), "Test Cart created successfully");
        $this->assertTrue($client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
        ));
        $newCart = json_decode($client->getResponse()->getContent(), true);
        $id = isset($newCart['id']) ? $newCart['id'] : 0;

        $client->request('GET', '/cart/'.$id);
        $this->assertEquals(0, $newCart['total_price']);

        // add Product to Cart
        $client->request('PUT', '/cart/'.$id, ['action' => 'add', 'product' => $productId]);
        $this->assertTrue($client->getResponse()->isSuccessful(), "Product added to Cart successfully");
        $this->assertTrue($client->getResponse()->headers->contains(
            'Content-Type',
            'application/json'
        ));
        $editCart = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(self::PRICE_1, $editCart['total_price']);

        // remove Product from cart
        $client->request('PUT', '/cart/'.$id, ['action' => 'remove', 'product' => $productId]);
        $this->assertTrue($client->getResponse()->isSuccessful(), "Product added to Cart successfully");
        $this->assertTrue($client->getResponse()->headers->contains(
            'Content-Type',
            'application/json'
        ));
        $emptyCart = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(0, $emptyCart['total_price']);

        // delete cart
        $client->request('DELETE', '/cart/'.$id);
        $this->assertTrue($client->getResponse()->isSuccessful(), "Product added to Cart successfully");
    }

    public function tearDown()
    {
        $container = static::createClient()->getContainer();
        $em = $container->get('doctrine.orm.default_entity_manager');
        $product1 = $em->getRepository(Product::class)->findOneByName(self::NAME_1);

        if ($product1) $em->remove($product1);

        $em->flush();

        parent::tearDown();
    }
}
