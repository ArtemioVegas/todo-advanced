<?php
namespace AppBundle\EventListener;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use FOS\UserBundle\Event\GetResponseUserEvent;
use Symfony\Component\HttpFoundation\Session\Session;

class RedirectAfterRegistrationSubscriber implements EventSubscriberInterface
{
    private $router;
    private $session;

    public function __construct(RouterInterface $router, Session $session)
    {
        $this->router = $router;
        $this->session = $session;
    }
    public function onRegistrationConfirm(GetResponseUserEvent $event)
    {
        $url = $this->router->generate('task_all');
        $response = new RedirectResponse($url);
        $event->setResponse($response);
    }
    public function onRegistrationSuccess(FormEvent $event)
    {
        $url = $this->router->generate('task_all');
        $response = new RedirectResponse($url);
        $event->setResponse($response);
    }
    public static function getSubscribedEvents()
    {
        return [
            FOSUserEvents::REGISTRATION_CONFIRM => 'onRegistrationConfirm',
            FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess'
        ];
    }
}