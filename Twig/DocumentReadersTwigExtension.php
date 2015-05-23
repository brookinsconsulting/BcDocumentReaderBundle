<?php
/**
 * File containing the DocumentReadersTwigExtension class part of the BcDocumentReaderBundle package.
 *
 * @copyright Copyright (C) Brookins Consulting. All rights reserved.
 * @license For full copyright and license information view LICENSE and COPYRIGHT.md file distributed with this source code.
 * @version //autogentag//
 */

namespace BrookinsConsulting\BcDocumentReaderBundle\Twig;

use Twig_Extension;
use Twig_Function_Method;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DocumentReadersTwigExtension extends Twig_Extension
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var bool
     */
    protected $displayDebug;

    /**
     * @var \BrookinsConsulting\BcDocumentReaderBundle\Services\BcDocumentReader
     */
    protected $documentReader;

    /**
     * Constructor.
     * @param Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param array $options
     */
    public function __construct( ContainerInterface $container, array $options )
    {
        $this->container = $container;
        $this->options = $options[0]['options'];
        $this->displayDebug = $this->options['display_debug'] == true ? true : false;
        $this->documentReader = $this->container->get( 'brookinsconsulting.document_reader' );
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'bc_document_readers' => new Twig_Function_Method( $this, 'documentReaders' ),
        );
    }

    /**
     * Implements the "bc_document_readers" function
     *
     * @return array
     */
    public function documentReaders()
    {
        // Generate document readers helper application list results
        $this->documentReader->buildDocumentReaders();

        return $this->documentReader->get( 'document_readers' );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'bc_document_readers';
    }
}
