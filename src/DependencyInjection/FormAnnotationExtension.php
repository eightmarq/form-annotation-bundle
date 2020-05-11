<?php

declare(strict_types=1);

namespace EightMarq\FormAnnotationBundle\DependencyInjection;

use Exception;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

/**
 * Class FormAnnotationExtension
 * @package EightMarq\FormAnnotationBundle\DependencyInjection
 */
class FormAnnotationExtension extends Extension
{
    /**
     * @param array $configs
     * @param ContainerBuilder $container
     *
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {

    }
}