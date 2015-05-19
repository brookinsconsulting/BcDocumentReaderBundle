<?php
/**
 * File containing the BcDocumentReaderBundle class part of the BcDocumentReaderBundle package.
 *
 * @copyright Copyright (C) Brookins Consulting. All rights reserved.
 * @license For full copyright and license information view LICENSE and COPYRIGHT.md file distributed with this source code.
 * @version //autogentag//
 */

namespace BrookinsConsulting\BcDocumentReaderBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class BcDocumentReaderBundle extends Bundle
{
    /**
     * @var string
     */
    protected $name = "BcDocumentReaderBundle";

    /**
     * Enable Bundle Inheritance (of the eZ Systems, eZ Publish Platform / eZ Platform, DemoBundle)
     *
     * @return string
     */
    public function getParent()
    {
        return 'EzPublishCoreBundle';
    }
}
