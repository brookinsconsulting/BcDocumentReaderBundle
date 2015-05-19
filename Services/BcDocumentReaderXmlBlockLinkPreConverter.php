<?php
/**
 * File containing the BcDocumentReaderXmlBlockLinkPreConverter class part of the BcDocumentReaderBundle package.
 *
 * @copyright Copyright (C) Brookins Consulting. All rights reserved.
 * @license For full copyright and license information view LICENSE and COPYRIGHT.md file distributed with this source code.
 * @version //autogentag//
 */

namespace BrookinsConsulting\BcDocumentReaderBundle\Services;

use Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\Core\FieldType\XmlText\Converter;

class BcDocumentReaderXmlBlockLinkPreConverter implements Converter
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @var \eZ\Publish\API\Repository\Repository
     */
    protected $repository;

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
     * Constructor
     *
     * @param Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param eZ\Publish\API\Repository\Repository $repository
     * @param array $options
     */
    public function __construct( ContainerInterface $container, Repository $repository, array $options = array() )
    {
        $this->container = $container;
        $this->repository = $repository;
        $this->options = $options[0]['options'];
        $this->displayDebug = $this->options['display_debug'] == true ? true : false;
        $this->documentReader = $this->container->get('brookinsconsulting.document_reader');
    }

    /**
     * Converts internal links (eznode:// and ezobject://) to URLs.
     *
     * @param \DOMDocument $xmlDoc
     *
     * @return string|null
     */
    public function convert( \DOMDocument $xml )
    {
        $contentService = $this->repository->getContentService();
        $locationService = $this->repository->getLocationService();

        foreach( $xml->getElementsByTagName( "link" ) as $link )
        {
            if( $link->hasAttribute( "object_id" ) || $link->hasAttribute( "node_id" ) )
            {
                if( $link->hasAttribute( "node_id" ) )
                {
                    try
                    {
                        $location = $locationService->loadLocation(
                            $link->getAttribute( "node_id" )
                        );
                        $content = $contentService->loadContentByContentInfo(
                            $location->getContentInfo()
                        );
                        $this->documentReader->addContent( $content, $link, 'Detected by: ' . __METHOD__ . ' node_id elseif block' );
                    }
                    catch( Exception $e )
                    {
                        continue;
                    }
                }
                else
                {
                    try
                    {
                        $content = $contentService->loadContent(
                            $link->getAttribute( "object_id" )
                        );
                        $this->documentReader->addContent( $content, $link, 'Detected by: ' . __METHOD__ . ' object_id elseif block' );
                    }
                    catch( Exception $e )
                    {
                        continue;
                    }
                }
            }
            elseif( $link->hasAttribute( "url" ) )
            {
                try
                {
                    $url = $link->getAttribute( "url" );
                    $this->documentReader->addFileUrl( $url, null, $link, 'Detected by: ' . __METHOD__ . ' url elseif block' );
                }
                catch( Exception $e )
                {
                    continue;
                }
            }

        }
    }
}
