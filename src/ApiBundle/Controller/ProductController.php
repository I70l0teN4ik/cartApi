<?php

namespace ApiBundle\Controller;

use ApiBundle\Entity\Product;
use ApiBundle\Manager\ProductManager;
use ApiBundle\Response\ErrorResponse;
use ApiBundle\Response\ProductsResponse;
use ApiBundle\Response\SerializedResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends Controller
{
    /**
     * Add a new product
     *
     * @Route("/product", name="create_product")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse
     */
    public function postCreateProductAction(Request $request)
    {
        $name = $request->request->get('name', null);
        $price = $request->request->get('price', 0);

        /** @var ProductManager $productMng */
        $productMng = $this->get('product_manager');

        try {
            $product = $productMng->createProduct($name, $price);
        } catch (\Exception $e) {
            return new ErrorResponse($e);
        }

        return new SerializedResponse($this->get('serializer')->serialize($product, 'json'));
    }

    /**
     * Remove a product if it exists
     *
     * @Route("/product/{id}", requirements={"id": "\d+"}, name="remove_product")
     * @Method("DELETE")
     * @param Request $request
     * @param Product $product
     * @return JsonResponse
     */
    public function deleteRemoveProductAction(Request $request, Product $product = null)
    {
        /** @var ProductManager $productMng */
        $productMng = $this->get('product_manager');
        $productMng->deleteProduct($product);

        return new SerializedResponse();
    }

    /**
     * Update product title and/or price
     *
     * @Route("/product/{id}", requirements={"id": "\d+"}, name="edit_product")
     * @Method("PUT")
     * @param Request $request
     * @param Product $product
     * @return JsonResponse
     */
    public function putUpdateProductAction(Request $request, Product $product = null)
    {
        if (!$product) {
            return new ErrorResponse(sprintf('Product %s not found', $request->get('id')));
        }

        $name = $request->request->get('name', false);
        $price = $request->request->get('price', false);

        /** @var ProductManager $productMng */
        $productMng = $this->get('product_manager');
        try {
            $product = $productMng->editProduct($product, $name, $price);
        } catch (\Exception $e) {
            return new ErrorResponse($e);
        }

        return new SerializedResponse($this->get('serializer')->serialize($product, 'json'));
    }

    /**
     * Get product info
     *
     * @Route("/product/{id}", name="product_info")
     * @Method("GET")
     * @param Request $request
     * @param Product $product
     * @return JsonResponse
     */
    public function getProductAction(Request $request, Product $product = null)
    {
        if (!$product) {
            return new ErrorResponse(sprintf('Product %s not found', $request->get('id')));
        }
        return new SerializedResponse($this->get('serializer')->serialize($product, 'json'));
    }

    /**
     * This method returns paginated list of products (max 3 items per page).
     * List could be sorted by 'name', 'price' or 'created' timestamp.
     * If sorting param prepended with "-" sorting will be done in descending order
     *
     * @Route("/products", name="products_list")
     * @Method("GET")
     * @param Request $request
     * @return JsonResponse
     */
    public function getProductsListAction(Request $request)
    {
        $page = $request->query->getInt('page', 1);
        $sort = $request->query->get('sort', null);

        /** @var ProductManager $productMng */
        $productMng = $this->get('product_manager');

        $products = $productMng->getProductsList($page, $sort);

        $pagination = [];
        if (($page - 1) > 0) {
            $pagination['prev'] = $this->generateUrl('products_list', ['page' => ($page - 1), 'sort' => $sort], 0);
        }
        if (sizeof($products) == Product::PER_PAGE  && $productMng->getProductsCount() > $page * Product::PER_PAGE) {
            $pagination['next'] =  $this->generateUrl('products_list', ['page' => ($page + 1), 'sort' => $sort], 0);
        }

        return new ProductsResponse($this->get('serializer')->toArray($products), $pagination);
    }
}
