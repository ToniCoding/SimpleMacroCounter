<?php

namespace src\Controller;

use src\DTO\RegisterUserDTO;
use src\DTO\LoggedUserDTO;
use src\Entity\User;
use src\Entity\AccessToken;
use src\Form\LoginUserType;
use src\Form\RegisterUserType;
use src\Security\AccessTokenHandler;
use src\Handlers\UserHandler;
use src\Security\AppAuthenticator;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\HttpFoundation\{RedirectResponse, JsonResponse, Request, Response};
use Symfony\Component\Routing\Annotation\Route;

/**
 * === USER DOMAIN CONTROLLER ===
 * 
 * Controller responsible for rendering and processing everything related
 * to the user experience, including registration and login workflows.
 *
 * @package App\Controller\User
 *
 * @author ToniCoding
 *
 * @see UserHandler Handles user-related business logic
 * @see AccessTokenHandler Manages token creation and validation
 *
 * @property UserHandler $userHandler
 * @property AccessTokenHandler $accessTokenHandler
 * @property EntityManagerInterface $entityManager
 * @property UserAuthenticatorInterface $userAuthenticatorInterface
 *
 * @uses UserHandler
 * @uses AccessTokenHandler
 */
class UserController extends AbstractController {
    public function __construct(
        private UserHandler $userHandler,
        private AccessTokenHandler $accessTokenHandler,
        private EntityManagerInterface $entityManager,
        private UserAuthenticatorInterface $userAuthenticatorInterface,
        private AppAuthenticator $appAuthenticator) {}

    /**
     * Process the user registration by rendering and processing the register form.
     * @param Request $request
     * @return Response | RedirectResponse
     */
    #[Route('/register', name: 'register_form', methods: ['GET', 'POST'])]
    public function registerUser(Request $request): Response | RedirectResponse {
        $userDTO = new RegisterUserDTO();
        
        $form = $this->createForm(RegisterUserType::class, $userDTO);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $userDTO = $form->getData();
            if ($this->userHandler->handle('register', $userDTO)) {
                return $this->redirect('login');
            }
        }

        return $this->render('RegisterPageTemplate.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Process the login user process by rendering and processing the login form.
     * It also creates and returns an access token to the user that will be required from all
     * the SMC endpoints and extracted by the Symfony extractor, check reference.
     * Ref: config/packages/security.yaml
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return JsonResponse|Response
     */
    #[Route('/login', name: 'login_form', methods: ['GET', 'POST'])]
    public function loginUser(Request $request): Response | JsonResponse | RedirectResponse {
        $user = $this->getUser();

        if ($user !== null) {
            $accessToken = $this->accessTokenHandler->setUserBadgeIn($user);

            return $this->json([
                'message' => 'Login successful',
                'token' => $accessToken->getValue(),
                'expires_at' => $accessToken->getExpiresAt()
            ], 200);
        }

        $userDTO = new LoggedUserDTO();

        $form = $this->createForm(LoginUserType::class, $userDTO);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userDTO = $form->getData();
            $loginSuccess = $this->userHandler->handle('login', null, $userDTO);

            if (!$loginSuccess) {
                return $this->json([
                    'error' => 'Invalid credentials'
                ], 401);
            }

            $user = $this->entityManager->getRepository(User::class)
                ->findOneBy(['username' => $userDTO->getUsername()]);

            $accessToken = $this->accessTokenHandler->setUserBadgeIn($user);

            if ($accessToken) {
                return $this->userAuthenticatorInterface->authenticateUser(
                    $user,
                    $this->appAuthenticator,
                    $request
                );
            }
        }

        return $this->render('LoginPageTemplate.twig', [
            'form' => $form->createView()
        ]);
    }
}
