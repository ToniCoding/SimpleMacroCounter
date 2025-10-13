<?php

namespace App\Controller;

use App\Form\LoginUserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\DTO\RegisterUserDTO;
use App\DTO\LoggedUserDTO;
use App\Form\RegisterUserType;
use App\Handlers\UserHandler;

class UserController extends AbstractController {
    private UserHandler $userHandler;

    public function __construct(UserHandler $userHandler) {
        $this->userHandler = $userHandler;
    }

    #[Route('/register', name: 'register_form', methods: ['GET', 'POST'])]
    public function registerUser(Request $request): RedirectResponse|Response {
        $userDTO = new RegisterUserDTO();
        
        $form = $this->createForm(RegisterUserType::class, $userDTO);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $userDTO = $form->getData();
            if ($this->userHandler->handle('register', $userDTO)) {
                return $this->redirectToRoute('register_success');
            }
        }

        return $this->render('RegisterPageTemplate.php.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/register/success', name: 'register_success')]
    public function registerSuccess(): Response {
        return $this->render('user/success.html.twig', [
            'message' => 'Successfully created the user'
        ]);
    }

    #[Route('/login', name: 'login_form')]
    public function loginUser(Request $request): RedirectResponse | Response {
        $userDTO = new LoggedUserDTO();

        $form = $this->createForm(LoginUserType::class, $userDTO);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userDTO = $form->getData();
            if ($this->userHandler->handle('login', null, $userDTO)) {
                return $this->redirectToRoute('login_success');
            }
        }

        return $this->render('LoginPageTemplate.php.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/login/success', name: 'login_success')]
    public function loginSuccess(): Response {
        return $this->render('user/success.html.twig', [
            'message' => 'Successfully logged in!'
        ]);
    }
}
