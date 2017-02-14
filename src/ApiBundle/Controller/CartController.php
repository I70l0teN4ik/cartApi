<?php

namespace ApiBundle\Controller;

use ApiBundle\Entity\Cart;
use ApiBundle\Manager\CartManager;
use ApiBundle\Response\ErrorResponse;
use ApiBundle\Response\SerializedResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

class CartController extends Controller
{
    /**
     * Create new cart. Add Product to cart if specified.
     *
     * @Route("/cart", name="create_cart")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse
     */
    public function postCreateCartAction(Request $request)
    {
        $productId = $request->request->getInt('product');

        /** @var CartManager $cartMng */
        $cartMng = $this->get('cart_manager');
        $cart = $cartMng->createCart($productId);

        $cartArray = $this->get('serializer')->toArray($cart);
        $cartArray['total_price'] = $cart->getTotalPrice();

        return new JsonResponse($cartArray);
    }

    /**
     * Silently delete Cart
     *
     * @Route("/cart/{id}", requirements={"id": "\d+"}, name="remove_cart")
     * @Method("DELETE")
     * @param Cart $cart
     * @return JsonResponse
     */
    public function deleteRemoveCartAction(Cart $cart = null)
    {
        /** @var CartManager $cartMng */
        $cartMng = $this->get('cart_manager');
        $cartMng->deleteCart($cart);

        return new SerializedResponse();
    }

    /**
     * Update Cart: add or remove a Product
     *
     * @Route("/cart/{id}", requirements={"id": "\d+"}, name="update_cart_products_list")
     * @Method("PUT")
     * @param Request $request
     * @param Cart $cart
     * @return JsonResponse
     */
    public function putUpdateCartAction(Request $request, Cart $cart)
    {
        $productId = $request->request->getInt('product');
        $action = $request->request->get('action', null);

        if (!in_array($action, ['add', 'remove'])) {
            throw new NotAcceptableHttpException(sprintf('Not allowed action: %s', $action));
        }

        /** @var CartManager $cartMng */
        $cartMng = $this->get('cart_manager');
        try {
            $cart = $cartMng->editCart($cart, $productId, $action);
        } catch (\Exception $e) {
            return new ErrorResponse($e);
        }
        $cartArray = $this->get('serializer')->toArray($cart);
        $cartArray['total_price'] = $cart->getTotalPrice();

        return new JsonResponse($cartArray);
    }

    /**
     * Get Cart info with total price of added Products
     *
     * @Route("/cart/{id}", name="cart_products_list")
     * @Method("GET")
     * @param Request $request
     * @param Cart $cart
     * @return JsonResponse
     */
    public function getCartProductsListAction(Request $request, Cart $cart)
    {
        $cartArray = $this->get('serializer')->toArray($cart);
        $cartArray['total_price'] = $cart->getTotalPrice();

        return new JsonResponse($cartArray);
    }

    /**
     * @Route("/carts", name="carts_list")
     * @Method("GET")
     * @param Request $request
     * @return JsonResponse
     */
    public function getCartsListAction(Request $request)
    {
        /** @var CartManager $cartMng */
        $cartMng = $this->get('cart_manager');

        $carts = $this->get('serializer')->serialize($cartMng->getCartsList(), 'json');

        return new SerializedResponse($carts);
    }
}
