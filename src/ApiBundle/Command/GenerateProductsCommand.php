<?php

namespace ApiBundle\Command;


use ApiBundle\Manager\ProductManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateProductsCommand extends ContainerAwareCommand
{
    /**
     * @var ProductManager
     */
    private $productMng;

    private $defaultProducts;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('api:generate:products')->setDescription('Generate default products');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->productMng = $this->getContainer()->get('product_manager');
        $this->defaultProducts = array(
            ["price" => 1.99, "name" => "Fallout"],
            ["price" => 2.99, "name" => "Don’t Starve"],
            ["price" => 3.99, "name" => "Baldur’s Gate"],
            ["price" => 4.99, "name" => "Icewind Dale"],
            ["price" => 5.99, "name" => "Bloodborne"],
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Please wait. This action gonna take 5 seconds...');
        foreach ($this->defaultProducts as $product) {
            try {
                $this->productMng->createProduct($product['name'], $product['price']);
            } catch (\Exception $e) {
                $output->writeln('Failed to generate default products. Details:');
                $output->writeln($e->getMessage());
                return;
            }
            $output->write('.');
            // to allow sorting by created timestamp in future
            sleep(1);
        }
        $output->writeln('');
        $output->writeln('Default products generated successfully.');
    }
}