<?php

namespace Tests\ApiBundle\Controller;

use ApiBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductControllerTest extends WebTestCase
{
    const NAME_1 = 'TEST_PRODUCT';
    const NAME_2 = 'TEST_PRODUCT_UPDATED';
    const PRICE_1 = 1.23;
    const PRICE_2 = 2.99;

    public function testProduct()
    {
        $client = static::createClient();

        // create test Product
        $client->request('POST', '/product', ['name' => self::NAME_1, 'price' => self::PRICE_1]);
        $this->assertTrue($client->getResponse()->isSuccessful(), "Failed to create Test Product");
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            ),
            'the "Content-Type" header is "application/json"'
        );

        $newProduct = json_decode($client->getResponse()->getContent(), true);
        $id = isset($newProduct['id']) ? $newProduct['id'] : 0;

        // list products
        $client->request("GET", "/products?sort=-created");
        $this->assertContains('products', $client->getResponse()->getContent());
        $this->assertContains(self::NAME_1, $client->getResponse()->getContent());

        // edit product
        $client->request("PUT", "/product/".$id, ['name' => self::NAME_2, 'price' => self::PRICE_2]);
        $this->assertTrue($client->getResponse()->isSuccessful(), "Failed to update Product");
        $this->assertContains(self::NAME_2, $client->getResponse()->getContent());

        $editedProduct = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(self::PRICE_2, $editedProduct['price']);

        // remove test Product
        $client->request("DELETE", "/product/".$id);
        $this->assertTrue($client->getResponse()->isSuccessful(), "Failed to delete Product");
    }

    public function tearDown()
    {
        $container = static::createClient()->getContainer();
        $em = $container->get('doctrine.orm.default_entity_manager');
        $product1 = $em->getRepository(Product::class)->findOneByName(self::NAME_1);
        $product2 = $em->getRepository(Product::class)->findOneByName(self::NAME_2);

        if ($product1) $em->remove($product1);
        if ($product2) $em->remove($product2);

        $em->flush();

        parent::tearDown();
    }
}
