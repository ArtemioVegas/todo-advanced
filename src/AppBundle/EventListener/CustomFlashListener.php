<?php

namespace AppBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CustomFlashListener implements EventSubscriberInterface
{
    /**
     * @var string[]
     */
    private static $successMessages = array(
        FOSUserEvents::CHANGE_PASSWORD_COMPLETED => 'change_password.flash.success',
        FOSUserEvents::GROUP_CREATE_COMPLETED => 'group.flash.created',
        FOSUserEvents::GROUP_DELETE_COMPLETED => 'group.flash.deleted',
        FOSUserEvents::GROUP_EDIT_COMPLETED => 'group.flash.updated',
        FOSUserEvents::PROFILE_EDIT_COMPLETED => 'profile.flash.updated',
        FOSUserEvents::REGISTRATION_COMPLETED => 'registration.flash.user_created',
        FOSUserEvents::RESETTING_RESET_COMPLETED => 'resetting.flash.success',
    );

    /**
     * @var Session
     */
    private $session;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    private $token;

    /**
     * FlashListener constructor.
     *
     * @param Session $session
     * @param TranslatorInterface $translator
     */
    public function __construct(Session $session, TranslatorInterface $translator, TokenStorageInterface $token)
    {
        $this->session = $session;
        $this->translator = $translator;
        $this->token = $token;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::CHANGE_PASSWORD_COMPLETED => 'addSuccessFlash',
            FOSUserEvents::GROUP_CREATE_COMPLETED => 'addSuccessFlash',
            FOSUserEvents::GROUP_DELETE_COMPLETED => 'addSuccessFlash',
            FOSUserEvents::GROUP_EDIT_COMPLETED => 'addSuccessFlash',
            FOSUserEvents::PROFILE_EDIT_COMPLETED => 'addSuccessFlash',
            FOSUserEvents::REGISTRATION_COMPLETED => array('addSuccessFlash', 15),
            FOSUserEvents::RESETTING_RESET_COMPLETED => 'addSuccessFlash',
        );
    }

    /**
     * @param Event $event
     * @param string $eventName
     */
    public function addSuccessFlash(Event $event, $eventName)
    {
        if (!isset(self::$successMessages[$eventName])) {
            throw new \InvalidArgumentException('This event does not correspond to a known flash message');
        }

        $this->session->getFlashBag()->add('success', $this->trans(self::$successMessages[$eventName], ['%usr%' => $this->token->getToken()->getUsername()]));
    }

    /**
     * @param string $message
     * @param array $params
     *
     * @return string
     */
    private function trans($message, array $params = array())
    {
        return $this->translator->trans($message, $params, 'FOSUserBundle');
    }
}
