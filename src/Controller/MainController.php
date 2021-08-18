<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Entity\Product;
use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class MainController extends AbstractController
{
    /**
     * @Route("/homepage", name="app_register")
     */
    public function homepage(Request $request): Response
    {
        $products = $this->getDoctrine()->getManager()->getRepository(Product::class)->findAll();

        return $this->render('pages/home.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @Route("/products/{id}", name="product_page")
     */
    public function productPage(Request $request, string $id): Response
    {
        $product = $this->getDoctrine()->getManager()->getRepository(Product::class)->find($id);
        $addToCart = $request->request->get('add-to-cart');
        if ($addToCart) {
            $currentCart = $request->getSession()->get("cart", []);
            if (!isset($currentCart[$id])) {
                $product->counter = 0;
                $currentCart[$id] = $product;
            }
            $currentCart[$id]->counter = intval($currentCart[$id]->counter) + 1;
            $request->getSession()->set("cart", $currentCart);
        }
        return $this->render('pages/product.html.twig', [
            'product' => $product
        ]);
    }

    /**
     * @Route("/cart", name="cart_page")
     */
    public function cartPage(Request $request, UserInterface $user): Response
    {
        $totalPrice = 0;
        $cart = $request->getSession()->get("cart", []);
        foreach ($cart as $product) {
            $totalPrice += $product->getPrice() * $product->counter;
        }

        if ($request->request->get("order") == "confirmed") {
            // $this->getDoctrine()->getManager()->getRepository(Orde)
            // firstly create order
            $newOrder = new Order();
            $newOrder->setOwner($user->getUserIdentifier());
            $newOrder->setCreationTime(time());
            $em = $this->getDoctrine()->getManager();
            $em->persist($newOrder);
            $em->flush();

            $newOrderId = $newOrder->getId();
            // then save order products
            foreach ($cart as $product) {
                $orderSector = new OrderProduct();
                $orderSector->setOrderId($newOrderId);
                $orderSector->setProductId($product->getId());
                $orderSector->quantity = $product->counter;
                $em->persist($orderSector);
                $em->flush();
            }
            // then redirect to success page
            return $this->redirectToRoute('order_success');
        }

        return $this->render('pages/cart.html.twig', [
            'total_price' => $totalPrice,
            'products' => $request->getSession()->get("cart", []),
            'date' => date('d/m/Y')
        ]);
    }
    /**
     * @Route("/order_success", name="order_success")
     */
    public function orderSuccessPage(Request $request, UserInterface $user): Response
    {
        $totalPrice = 0;
        $cart = $request->getSession()->get("cart", []);
        foreach ($cart as $product) {
            $totalPrice += $product->getPrice() * $product->counter;
        }

        return $this->render('pages/order_success.html.twig', [
            'total_price' => $totalPrice,
            'products' => $request->getSession()->get("cart", []),
            'date' => date('d/m/Y')
        ]);
    }
}
