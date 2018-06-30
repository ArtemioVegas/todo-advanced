<?php
namespace AppBundle\Security;
use AppBundle\Form\LoginForm;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use FOS\UserBundle\Model\UserManager;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    private $formFactory;
    private $em;
    private $router;
    private $passwordEncoder;
    private $userManager;

    public function __construct(FormFactoryInterface $formFactory, EntityManager $em, RouterInterface $router, UserPasswordEncoder $passwordEncoder,UserManager $userManager)
    {
        $this->formFactory = $formFactory;
        $this->em = $em;
        $this->router = $router;
        $this->passwordEncoder = $passwordEncoder;
        $this->userManager = $userManager;
    }

    public function getCredentials(Request $request)
    {
        $isLoginSubmit = $request->getPathInfo() == '/login_check' && $request->isMethod('POST');
        if (!$isLoginSubmit) {
            // skip authentication
            return;
        }
        //$form = $this->formFactory->create(LoginForm::class);
        //$form->handleRequest($request);
        //$data = $form->getData();
        $data['_username'] = trim($request->request->get('_username'));
        $data['_password'] = trim($request->request->get('_password'));
        $data['captcha']   = trim($request->request->get('captcha'));
        $data['phrase']    = trim($request->getSession()->get('gcb_captcha')['phrase']);

        /*
        if ( trim($request->request->get('captcha')) !== trim($request->getSession()->get('gcb_captcha')['phrase']) ){
            throw new CustomUserMessageAuthenticationException(
                'Указан неверный код с картинки'
            );
        }
        */

        $request->getSession()->set(
            Security::LAST_USERNAME,
            $data['_username']
        );
        return $data;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $username = $credentials['_username'];

        if( $credentials['phrase'] !== $credentials['captcha'] ){
            throw new CustomUserMessageAuthenticationException(
                'Указан неверный код с картинки'
            );
        }

        /*
        $userInstance = $this->em->getRepository('AppBundle:User')
            ->findOneBy(['email' => $username]);
        */

        $userInstance = $this->userManager->findUserByUsernameOrEmail($username);

        if (!$userInstance) {
            throw new CustomUserMessageAuthenticationException(
                'Указанный пользователь не найден'
            );
        }

        return $userInstance;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        $password = $credentials['_password'];
        if ($this->passwordEncoder->isPasswordValid($user, $password)) {
            return true;
        }
        throw new CustomUserMessageAuthenticationException(
            'Неверный пароль'
        );

        //return false;
    }

    protected function getLoginUrl()
    {
        return $this->router->generate('fos_user_security_login');
    }

    protected function getDefaultSuccessRedirectUrl()
    {
        return $this->router->generate('task_all');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $targetPath = null;

        // if the user hit a secure page and start() was called, this was
        // the URL they were on, and probably where you want to redirect to
        if ($request->getSession() instanceof SessionInterface) {
            $targetPath = $request->getSession()->get('_security.' . $providerKey . '.target_path');
            // add flash message to the user
            $request->getSession()->getBag('flashes')->add('success', 'Hello, ' . $token->getUsername());
        }

        if (!$targetPath) {
            $targetPath = $this->getDefaultSuccessRedirectUrl();
        }

        return new RedirectResponse($targetPath);
    }
}