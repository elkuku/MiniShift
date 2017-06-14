<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserKeysType;
use AppBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class UserController
 * @package AppBundle\Controller
 */
class UserController extends Controller
{
    /**
     * @Route("/users", name="users-list")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction()
    {
        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();

        return $this->render('user/list.html.twig', ['users' => $users]);
    }

    /**
     * @Route("/user-edit-keys/{id}", name="user-edit-keys")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param User    $user
     * @param Request $request
     *
     * @return Response
     */
    public function editKeysAction(User $user, Request $request)
    {
        $form = $this->createForm(UserKeysType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'User keys have been saved.');

            return $this->redirectToRoute('users-list');
        }

        return $this->render(
            'user/form_keys.html.twig',
            [
                'form' => $form->createView(),
                'data' => $user,
            ]
        );
    }

    /**
     * @Route("/user-edit/{id}", name="user-edit")
     * @Security("has_role('ROLE_USER') or has_role('ROLE_ADMIN')")
     *
     * @param User    $user
     * @param Request $request
     *
     * @return Response
     */
    public function editAction(User $user, Request $request)
    {
        $currentUser = $this->getUser();

        if ('ROLE_USER' == $currentUser->getRole()) {
            if ($currentUser->getId() != $user->getId()) {
                throw new AccessDeniedException('You are only allowed to edit your own data.');
            }
        }

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $encoder  = $this->get('security.password_encoder');
            $password = $encoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'User has been saved.');

            if ('ROLE_ADMIN' == $currentUser->getRole()) {
                return $this->redirectToRoute('users-list');
            } else {
                return $this->redirectToRoute('homepage');
            }
        }

        return $this->render(
            'user/form.html.twig',
            [
                'form' => $form->createView(),
                'data' => $user,
            ]
        );
    }

    /**
     * @Route("/register", name="register")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request)
    {
        // Create a new blank user and process the form
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encode the new users password
            $encoder  = $this->get('security.password_encoder');
            $password = $encoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            // Set their role
            $user->setRole('ROLE_USER');

            // Save
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('users-list');
        }

        return $this->render(
            'user/register.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/user-delete/{id}", name="user-delete")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param User $user
     *
     * @return Response
     */
    public function deleteTransactionAction(User $user)
    {
        if (!$user) {
            throw $this->createNotFoundException('No User found');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        $this->addFlash('success', 'User has been deleted');

        return $this->listAction();
    }
}
