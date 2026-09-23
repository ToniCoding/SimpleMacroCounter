<?php

namespace App\Controller;

use App\DTO\UserData\Request\UserRegisterRequestDTO;
use App\Exceptions\{AlreadyRegisteredUsernameException, InvalidEmailProviderException};

use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Security\Http\Authentication\{AuthenticationUtils, UserAuthenticatorInterface};
use Symfony\Component\HttpFoundation\{JsonResponse, Response};
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller responsible for rendering user views (login and registration forms) 
 * and managing user registration via API endpoints, integrating with Symfony's 
 * native stateful authentication and session management.
 *
 * @package App\Controller
 *
 * @author ToniCoding
 *
 * @see UserService Handles user registration and validation rules
 *
 * @property EntityManagerInterface $entityManager
 * @property UserAuthenticatorInterface $userAuthenticatorInterface
 *
 * @uses UserService
 */

class UserController extends AbstractController {
    /**
     * Initializes the user controller with its required dependencies.
     * 
     * @param EntityManagerInterface $entityManager Manages entity persistence and database operations.
     * @param UserAuthenticatorInterface $userAuthenticatorInterface Handles programmatic user authentication.
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserAuthenticatorInterface $userAuthenticatorInterface,
    ) {}

    /**
     * Renders the user registration page view.
     * 
     * @return Response Returns the rendered registration template.
     */
    #[Route('/register', name: 'register_form', methods: ['GET'])]
    public function registerForm(): Response {
        return $this->render('security/RegisterPageTemplate.twig.html');
    }

    /**
     * Validates and registers a new user based on the payload sent to the API endpoint.
     * 
     * @param UserRegisterRequestDTO $userRegisterRequest The request payload mapped and validated automatically from the request body.
     * @param UserService $userService The user service handling business logic, validation, and persistence.
     * @return JsonResponse Returns a JSON response containing the newly created user data with HTTP 201 (Created) 
     * on success, or an appropriate error response (HTTP 400/409) if business rules fail.
     */
    #[Route('/api/v1/register', name: 'user_register', methods: ['POST'])]
    public function register(
        #[MapRequestPayload] UserRegisterRequestDTO $userRegisterRequest,
        UserService $userService
    ): JsonResponse {
        try {
            $user = $userService->register($userRegisterRequest);

            return new JsonResponse([
                'status' => 'success',
                'message' => 'Successfully registered the user.',
                'redirect_url' => '/login',
                'data' => [
                    'id' => $user->getId(),
                    'username' => $user->getUserIdentifier(),
                    'email' => $user->getEmail(),
                ]
            ], Response::HTTP_CREATED);

        } catch (AlreadyRegisteredUsernameException $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Username already in use.',
            ], Response::HTTP_CONFLICT);

        } catch (InvalidEmailProviderException $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Invalid email domain.',
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Renders the login page view or redirects authenticated users to the home page.
     * 
     * @param AuthenticationUtils $authenticationUtils Utility to retrieve authentication errors and the last entered username.
     * @return Response Returns a redirect response if already logged in, or the rendered login template containing errors and last username.
     */
    #[Route(path: '/login', name: 'user_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        return $this->render('security/LoginPageTemplate.twig.html', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }
}
