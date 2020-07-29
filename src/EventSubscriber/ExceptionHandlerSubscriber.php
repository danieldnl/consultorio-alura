<?php

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use EntidadeFactoryException;
use App\Helper\ResponseFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionHandlerSubscriber implements EventSubscriberInterface
{ 
    private $logger;

    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => [
                ['handleEntityFactoryException', 1],
                ['handle404Exception', 0],
                ['handleGenericException', -1],
            ]
        ];
    }

    public function handle404Exception(ExceptionEvent $event)
    {
        if($event->getThrowable() instanceof NotFoundHttpException){
            $response = ResponseFactory::fromError($event->getThrowable())->getResponse();
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            $event->setResponse($response);
        }
    }

    public function handleEntityFactoryException(ExceptionEvent $event)
    {
        if($event->getThrowable() instanceof EntidadeFactoryException){
            $response = ResponseFactory::fromError($event->getThrowable())->getResponse();
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $event->setResponse($response);
        }
    }

    public function handleGenericException(ExceptionEvent $event)
    {
        $this->logger->critical("Uma exceção ocorreu. {stack}",["stack" => $event->getThrowable()->getTraceAsString()]);
        $event->setResponse(ResponseFactory::fromError(new \Exception("Um erro inesperado ocorreu"))->getResponse());
    }
}
