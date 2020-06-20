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

namespace EightMarq\FormAnnotationBundle\Form;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Annotations\Reader;
use EightMarq\FormAnnotationBundle\Annotation\AddField;
use EightMarq\FormAnnotationBundle\Annotation\FormType;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class AbstractAnnotationType extends AbstractType
{
    /**
     * @var Reader
     */
    private Reader $reader;

    /**
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     * @throws ReflectionException
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $annotations = $this->getAnnotations($options['data_class']);

        $this->applyPropertyAnnotation($annotations['properties'], $builder);
        $this->applyClassAnnotation($annotations['class'], $builder);
    }

    /**
     * @return Slugify
     */
    public static function getSlugify(): Slugify
    {
        return new Slugify(
            [
                'regexp' => '/(?<=[[:^upper:]])(?=[[:upper:]])/',
                'separator' => '_',
                'lowercase_after_regexp' => true
            ]
        );
    }

    /**
     * @param FormType $classAnnotation
     * @param FormBuilderInterface $builder
     */
    protected function applyClassAnnotation(FormType $classAnnotation, FormBuilderInterface $builder): void
    {
        if ($action = $classAnnotation->action) {
            $builder->setAction($action);
        }

        if ($method = $classAnnotation->method) {
            $builder->setMethod($method);
        }

        if ($submit = $classAnnotation->submit) {
            $builder->add('_submit', SubmitType::class, ['label' => $submit]);
        }
    }

    /**
     * @param array | null $properties
     * @param FormBuilderInterface $builder
     */
    protected function applyPropertyAnnotation(?array $properties, FormBuilderInterface $builder): void
    {
        foreach ($properties ?? [] as $propertyAnnotations) {
            foreach ($propertyAnnotations as $propertyAnnotation) {
                switch (get_class($propertyAnnotation)) {
                    case AddField::class:
                        $this->addField($propertyAnnotation, $builder);
                        break;
                }
            }
        }
    }

    /**
     * @param AddField $propertyAnnotation
     * @param FormBuilderInterface $builder
     */
    protected function addField(AddField $propertyAnnotation, FormBuilderInterface $builder): void
    {
        $options = $propertyAnnotation->options;

        if (!isset($options['label'])) {
            $options['label'] = sprintf(
                '%s.%s',
                self::getSlugify()->slugify($builder->getName()),
                self::getSlugify()->slugify($propertyAnnotation->name)
            );
        }

        $builder->add(
            $propertyAnnotation->name,
            $propertyAnnotation->type,
            $options
        );
    }


    /**
     * @param string $class
     *
     * @return array
     *
     * @throws ReflectionException
     */
    private function getAnnotations(string $class): array
    {
        $reflectionClass = new ReflectionClass($class);

        $propertyAnnotations = [];
        foreach ($reflectionClass->getProperties() as $property) {
            $propertyName = $property->getName();

            $propertyAnnotations[$propertyName] = $this->reader->getPropertyAnnotations(
                new ReflectionProperty($class, $propertyName)
            );
        }

        $classAnnotations = $this->reader->getClassAnnotations($reflectionClass);

        return [
            'class' => reset($classAnnotations),
            'properties' => $propertyAnnotations
        ];
    }
}