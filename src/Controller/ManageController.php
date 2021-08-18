<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
use App\Form\ProductType;
use App\Form\RegistrationFormType;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ManageController extends AbstractController
{
    /**
     * @Route("/manage/products", name="manage_products")
     */
    public function manageProducts(Request $request): Response
    {
        $products = $this->getDoctrine()->getManager()->getRepository(Product::class)->findAll();

        return $this->render('pages/manage/products.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @Route("/manage/products/{id}/delete", name="manage_delete_product", methods={"GET"})
     */
    public function deleteProductAction(Request $request, string $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository(Product::class)->find($id);
        if (!$product) {
            throw $this->createNotFoundException('No guest found');
        }
        $em->remove($product);
        $em->flush();

        return $this->redirect($this->generateUrl('manage_products'));
    }

    /**
     * @Route("/manage/products/create", name="manage_create_product")
     */
    public function manageCreateProduct(Request $request, SluggerInterface $slugger): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $preview */
            $preview = $form->get('preview')->getData();
            if ($preview) {
                $originalFilename = pathinfo($preview->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $preview->guessExtension();
                try {
                    $preview->move(
                        $this->getParameter('previews'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $product->setPreview($newFilename);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            // ... persist the $product variable or any other work

            return $this->redirectToRoute('manage_products');
        }

        return $this->render('pages/manage/create_product.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/manage/products/{id}/edit", name="manage_edit_product")
     */
    public function manageEditProduct(Request $request, SluggerInterface $slugger, string $id): Response
    {
        $product = $this->getDoctrine()->getManager()->getRepository(Product::class)->find($id);
        $display = $product;
        $display->setPreview(null);
        $form = $this->createForm(ProductType::class, $display);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $preview */
            $preview = $form->get('preview')->getData();
            if ($preview) {
                $originalFilename = pathinfo($preview->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $preview->guessExtension();
                try {
                    $preview->move(
                        $this->getParameter('previews'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $product->setPreview($newFilename);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            // ... persist the $product variable or any other work

            return $this->redirectToRoute('manage_products');
        }

        return $this->render('pages/manage/create_product.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/manage/users", name="manage_users")
     */
    public function manageUsers(Request $request): Response
    {
        $users = $this->getDoctrine()->getManager()->getRepository(User::class)->findAll();

        return $this->render('pages/manage/users.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/manage/users/{id}/edit", name="manage_edit_users")
     */
    public function manageEditUsers(Request $request, string $id): Response
    {
        $user = $this->getDoctrine()->getManager()->getRepository(User::class)->find($id);
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles($form->get("roles")->getData());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
        }
        return $this->render('pages/manage/edit_user.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * @Route("/manage/users/{id}/delete", name="manage_delete_user", methods={"GET"})
     */
    public function deleteUserAction(Request $request, string $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        if (!$user) {
            throw $this->createNotFoundException('No user found');
        }
        $em->remove($user);
        $em->flush();

        return $this->redirect($this->generateUrl('manage_users'));
    }
}
