<?php

use Doctrine\Common\Collections\ArrayCollection;
use Enlight\Event\SubscriberInterface;

class EventManager extends Enlight_Event_EventManager
{
    /**
     * @var Enlight_Event_EventManager
     */
    private $events;

    /**
     * @return \Shopware_Plugins_Frontend_SwagProfiling_Bootstrap
     */
    protected function getPluginBootstrap()
    {
        return Shopware()->Plugins()->Frontend()->SwagProfiling();
    }

    /**
     * @param $events Enlight_Event_EventManager
     */
    public function __construct(Enlight_Event_EventManager $events)
    {
        $this->listeners = $events->getAllListeners();
        $this->events = $events;
    }

    /**
     * @param string $event
     * @param null $eventArgs
     * @return Enlight_Event_EventArgs|null
     * @throws Enlight_Event_Exception
     */
    public function notify($event, $eventArgs = null)
    {
        $this->getPluginBootstrap()->addEvent(
            $event,
            'notify',
            $this->getListeners($event),
            $eventArgs
        );

        return parent::notify($event, $eventArgs);
    }

    /**
     * @param string $event
     * @param mixed $value
     * @param null $eventArgs
     * @return mixed
     * @throws Enlight_Event_Exception
     */
    public function filter($event, $value, $eventArgs = null)
    {
        if (isset($eventArgs) && is_array($eventArgs)) {
            $eventArgs = new Enlight_Event_EventArgs($eventArgs);
        } elseif (!isset($eventArgs)) {
            $eventArgs = new Enlight_Event_EventArgs();
        } elseif (!$eventArgs instanceof Enlight_Event_EventArgs) {
            throw new Enlight_Event_Exception('Parameter "eventArgs" must be an instance of "Enlight_Event_EventArgs"');
        }
        $eventArgs->setReturn($value);

        $this->getPluginBootstrap()->addEvent(
            $event,
            'filter',
            $this->getListeners($event),
            $eventArgs
        );

        return parent::filter($event, $value, $eventArgs);
    }

    /**
     * @param string $event
     * @param null $eventArgs
     * @return Enlight_Event_EventArgs|null
     * @throws Enlight_Exception
     */
    public function notifyUntil($event, $eventArgs = null)
    {
        $this->getPluginBootstrap()->addEvent(
            $event,
            'notifyUntil',
            $this->getListeners($event),
            $eventArgs
        );

        return parent::notifyUntil($event, $eventArgs);
    }

    /**
     * @param $event
     * @return Enlight_Event_Handler[]
     */
    public function getListeners($event)
    {
        $additionalEventListeners = $this->getPluginBootstrap()->getAdditionalListeners($event);

        foreach ($additionalEventListeners as $additionalEventListener) {
            $this->registerListener($additionalEventListener);
        }

        return parent::getListeners($event);
    }

    /**
     * @param SubscriberInterface $subscriber
     */
    public function addSubscriber(SubscriberInterface $subscriber)
    {
        return $this->events->addSubscriber($subscriber);
    }

    /**
     * @param $eventName
     * @param $listener
     * @param int $priority
     * @return Enlight_Event_EventManager
     */
    public function addListener($eventName, $listener, $priority = 0)
    {
        return $this->events->addListener($eventName, $listener, $priority);
    }

    /**
     * @param $event
     * @param ArrayCollection $collection
     * @param null $eventArgs
     * @return ArrayCollection|null
     * @throws Enlight_Event_Exception
     */
    public function collect($event, ArrayCollection $collection, $eventArgs = null)
    {
        return $this->events->collect($event, $collection, $eventArgs);
    }

    /**
     * @return array
     */
    public function getAllListeners()
    {
        return $this->events->getAllListeners();
    }

    /**
     * @return array
     */
    public function getEvents()
    {
        return $this->events->getEvents();
    }

    /**
     * @param string $event
     * @return bool
     */
    public function hasListeners($event)
    {
        return $this->events->hasListeners($event);
    }

    /**
     * @param Enlight_Event_Handler $handler
     * @return Enlight_Event_EventManager
     */
    public function registerListener(Enlight_Event_Handler $handler)
    {
        return parent::registerListener($handler);
    }

    /**
     * @param Enlight_Event_Subscriber $subscriber
     */
    public function registerSubscriber(Enlight_Event_Subscriber $subscriber)
    {
        $this->events->registerSubscriber($subscriber);
    }

    /**
     * @param Enlight_Event_Handler $handler
     * @return Enlight_Event_EventManager
     */
    public function removeListener(Enlight_Event_Handler $handler)
    {
        return $this->events->removeListener($handler);
    }

    /**
     * @return Enlight_Event_EventManager
     */
    public function reset()
    {
        return $this->events->reset();
    }
}
