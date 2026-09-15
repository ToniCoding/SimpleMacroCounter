<?php

namespace App\Controller;

use App\DTO\{RegisterUserDTO, LoggedUserDTO, UserRegisterRequestDTO};
use App\Entity\User;
use App\Exceptions\AgeNotAllowedException;
use App\Exceptions\AlreadyRegisteredUsernameException;
use App\Exceptions\InvalidEmailProviderException;
use App\Form\LoginUserType;
use App\Security\AppAuthenticator;
use App\Handlers\UserHandler;

use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
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
 * @property EntityManagerInterface $entityManager
 * @property UserAuthenticatorInterface $userAuthenticatorInterface
 *
 * @uses UserHandler
 * @uses AccessTokenHandler
 */
class UserController extends AbstractController {
    public function __construct(
        private UserHandler $userHandler,
        private EntityManagerInterface $entityManager,
        private UserAuthenticatorInterface $userAuthenticatorInterface,
        private AppAuthenticator $appAuthenticator
    ) {}

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
                'message' => 'Usuario registrado con éxito',
                'data' => [
                    'id' => $user->getId(),
                    'username' => $user->getUserIdentifier(),
                    'email' => $user->getEmail(),
                ]
            ], Response::HTTP_CREATED);

        } catch (AlreadyRegisteredUsernameException $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'El nombre de usuario ya está en uso.',
            ], Response::HTTP_CONFLICT);

        } catch (InvalidEmailProviderException $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'El dominio del correo electrónico no es válido.',
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Process the login user process by rendering and processing the login form.
     * It also creates and returns an access token to the user that will be required from all
     * the SMC endpoints and extracted by the Symfony extractor, check reference.
     * Ref: config/packages/security.yaml
     * @param Request $request
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

        return $this->render('LoginPageTemplate.twig.html', [
            'form' => $form->createView()
        ]);
    }
}
