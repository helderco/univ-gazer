<?php

namespace Siriux\GalleryBundle\Form\EventListener;

use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Siriux\UserBundle\Entity\User;

class AddUserFieldSubscriber implements EventSubscriberInterface
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that we want to listen on the form.post_bind
        // event and that the postBind method should be called.
        return array(FormEvents::POST_BIND => 'postBind');
    }

    public function postBind(DataEvent $event)
    {
        $data = $event->getData();

        // check if the media object is "new"
        if (!$data->getId()) {
            $data->setUser($this->user);
            $data->setAuthorName($this->user->getName());
            $data->setEnabled(true);
        }
    }
}