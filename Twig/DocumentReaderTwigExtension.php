<?php
/**
 * File containing the DocumentReaderTwigExtension class part of the BcDocumentReaderBundle package.
 *
 * @copyright Copyright (C) Brookins Consulting. All rights reserved.
 * @license For full copyright and license information view LICENSE and COPYRIGHT.md file distributed with this source code.
 * @version //autogentag//
 */

namespace BrookinsConsulting\BcDocumentReaderBundle\Twig;

use Twig_Extension;
use Twig_Filter_Method;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DocumentReaderTwigExtension extends Twig_Extension
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
     * @param array $mimetypes
     */
    public function __construct( ContainerInterface $container, array $options )
    {
        $this->container = $container;
        $this->options = $options[0]['options'];
        $this->displayDebug = $this->options['display_debug'] == true ? true : false;
        $this->documentReader = $this->container->get( 'brookinsconsulting.document_reader' );
    }

    /**
     * Returns a list of filters to add to the existing list
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
            'bc_document_reader' => new Twig_Filter_Method( $this, 'detectDocumentReader' ),
        );
    }

    /**
     * Implements the "bc_document_reader" filter
     *
     * @param string $url Url of the file extension to add
     * @param string $fileMimeType File extension mimetype
     * @param string $message Message of where the file was detected
     * @return bool
     */
    public function detectDocumentReader( $url, $mimetype = null, $message = 'Detected by: bc_document_reader twig filter use within an undocumented template' )
    {
        $link = false;

        $this->documentReader->addFileUrl( $url, $mimetype, $link, $message );

        return false;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'bc_document_reader';
    }
}
