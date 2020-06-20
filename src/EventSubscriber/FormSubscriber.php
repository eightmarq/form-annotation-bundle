<?php

/**
 * This file is part of the Eightmarq Symfony bundles.
 *
 * (c) Norbert Schvoy <norbert.schvoy@eightmarq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace EightMarq\FormAnnotationBundle\EventSubscriber;

use Doctrine\Common\Annotations\Reader;
use EightMarq\FormAnnotationBundle\Annotation\CreateForm;
use EightMarq\FormAnnotationBundle\Form\AbstractAnnotationType;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

class FormSubscriber implements EventSubscriberInterface
{
    /**
     * @var FormFactoryInterface
     */
    private FormFactoryInterface $formFactory;

    /**
     * @var Reader
     */
    private Reader $reader;

    /**
     * @param FormFactoryInterface $formFactory
     * @param Reader $reader
     */
    public function __construct(FormFactoryInterface $formFactory, Reader $reader)
    {
        $this->formFactory = $formFactory;
        $this->reader = $reader;
    }

    /**
     * @return array|string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.controller' => 'initializeForm'
        ];
    }

    /**
     * @param ControllerEvent $controllerEvent
     *
     * @throws ReflectionException
     */
    public function initializeForm(ControllerEvent $controllerEvent): void
    {
        $controllerInfo = $controllerEvent->getController();

        if (is_array($controllerInfo)) {
            foreach ($this->getControllerMethodAnnotations($controllerInfo) as $annotation) {
                if ($annotation instanceof CreateForm) {
                    $controllerEvent->getRequest()->attributes->set(
                        lcfirst($annotation->name),
                        $this->createForm($annotation)
                    );
                }
            }
        }
    }

    /**
     * @param CreateForm $annotation
     *
     * @return FormInterface
     */
    private function createForm(CreateForm $annotation): FormInterface
    {
        return $this->formFactory->createNamed(
            AbstractAnnotationType::getSlugify()->slugify($annotation->name),
            $annotation->type,
            new $annotation->dataClass()
        );
    }

    /**
     * @param array $controllerInfo
     *
     * @return array
     * @throws ReflectionException
     */
    private function getControllerMethodAnnotations(array $controllerInfo): array
    {
        $reflectionClass = new ReflectionClass($controllerInfo[0]);

        return $this->reader->getMethodAnnotations($reflectionClass->getMethod($controllerInfo[1]));
    }
}