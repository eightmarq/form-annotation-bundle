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

namespace EightMarq\FormAnnotationBundle\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class AddField
{
    /**
     * @var string
     */
    public string $name;

    /**
     * @var string | null
     */
    public ?string $type = null;

    /**
     * @var array
     */
    public array $options = [];
}