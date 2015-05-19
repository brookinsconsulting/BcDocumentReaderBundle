<?php
/**
 * File containing the BcDocumentReader class part of the BcDocumentReaderBundle package.
 *
 * @copyright Copyright (C) Brookins Consulting. All rights reserved.
 * @license For full copyright and license information view LICENSE and COPYRIGHT.md file distributed with this source code.
 * @version //autogentag//
 */

namespace BrookinsConsulting\BcDocumentReaderBundle\Services;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Repository;

class BcDocumentReader extends BcDocumentReaderPersistence
{
    protected $container;
    protected $repository;
    protected $options;
    protected $theme;
    protected $logger;
    protected $helpers;
    protected $helperMimeMap;
    protected $displayDebug;
    protected $displayDebugLevel;
    protected $fileFieldIdentifiers;

    /**
     * Constructor
     *
     * @param Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param Psr\Log\LoggerInterface $logger
     * @param array $options
     * @param array $mimetypes
     */
    public function __construct( ContainerInterface $container, Repository $repository, LoggerInterface $logger = null, array $options = array(), array $mimetypes = array() )
    {
        $this->container = $container;
        $this->repository = $repository;
        $this->options = $options[0]['options'];
        $this->theme = $options[0]['theme'];
        $this->logger = $logger;
        $this->helpers = $options[0]['helpers'];
        $this->helperMimeMap = $mimetypes[0]['helper_mime_map'];
        $this->displayDebug = $this->options['display_debug'] == true ? true : false;
        $this->displayDebugLevel = is_numeric( $this->options['display_debug_level'] ) ? $this->options['display_debug_level'] : 0;
        $this->fileFieldIdentifiers = !empty( $this->theme['file_field_identifiers'] ) ? $this->theme['file_field_identifiers'] : false;

        parent::__construct( $container, $options, $mimetypes );

        if( $this->displayDebug && $this->displayDebugLevel >= 3 )
        {
            echo "<span style='color:000000;'>BcDocumentReader : __construct:</span><br />\n";
            var_dump( $this->get( 'document_reader_extensions' ) );
        }
    }

    /**
     * Adds a file extension and related meta information into the document readers extension persistant storage
     *
     * @param string $url Url of the file extension to add
     * @param string $fileMimeType File extension mimetype
     * @param \DOMElement $link Reference to the DOMElement object
     * @param string $message Message of where the file was detected
     * @return bool
     */
    public function addFileUrl( $url, $fileMimeType = null, &$link = false, $message = 'Detected within an undocumented usage' )
    {
        $results = false;
        $fileMeta = array();

        if( !empty( $url ) && strpos( $url, 'mailto' ) === false )
        {
            $urlFileName = basename( $url );

            if( $fileMimeType != null )
            {
                $urlFileNameArray = explode( '.', $urlFileName );
                $fileExtension = trim( $urlFileNameArray[1] );
            }
            else
            {
                $mimeTypeGuesser = $this->container->get('brookinsconsulting.document_reader.mimetype.extension.guesser');
                $mimeType = $mimeTypeGuesser->guessByUrl( $url );

                $fileExtension = $mimeType[0];
                $fileMimeType = isset( $mimeType[1] ) ? trim( $mimeType[1] ) : false;

                if( $this->displayDebug && $this->displayDebugLevel >= 1 )
                {
                    echo '<hr />In, BcDocumentReader : addFileUrl : Notice: Used ExtensionMimeTypeGuesser service to detect mimeType: "' . $fileMimeType . '" for file extension, ' . $fileExtension . '<hr />';
                }
            }

            if( !empty( $fileMimeType ) && !empty( $fileExtension ) )
            {
                foreach( $this->helperMimeMap as $index => $helperMimeMapMimeTypeOptions )
                {
                    $helperMimeMapMimeType = $helperMimeMapMimeTypeOptions['mimeType'];

                    if( $helperMimeMapMimeType == $fileMimeType )
                    {
                        if( $link != false && $link->hasAttribute( "class" ) )
                        {
                            $link->setAttribute( "class", "file-" . $fileExtension );
                        }

                        $fileMeta[$fileExtension] = array( 'filename' => $urlFileName, 'url' => $url, 'mimetype' => $fileMimeType, 'detected_by' => $message );

                        break;
                    }
                }

                if( !empty( $fileMeta ) )
                {
                    $results = $this->add( $fileMeta );
                }
                elseif ( $this->logger !== null )
                {
                    $message = __METHOD__ . ' - Warning: File extension, "' . $fileExtension . '" and File mimeType, "' . $fileMimeType . '" not found in yaml mimetype settings, mimetypes.yml: helper_mime_map group.';

                    $this->logger->warning( $message );

                    if( $this->displayDebug && $this->displayDebugLevel >= 2 )
                    {
                        echo '<hr />' . $message . '<hr />';
                    }
                }

                return $results;
            }
        }
    }

    /**
     * Adds a file extension and related meta information into the document readers extension persistant storage from a eZ\Publish\Core\Repository\Values\Content\Content object
     *
     * @param eZ\Publish\Core\Repository\Values\Content\Content $content Content object containing a file field's file extension to add
     * @param \DOMElement $link Reference to the DOMElement object
     * @param string $message Message of where the file was detected
     * @return bool
     */
    public function addContent( Content $content = null, &$link = false, $message = null )
    {
        $fileExtension = false;
        $fileMimeType = false;

        foreach( $this->fileFieldIdentifiers as $identifier )
        {
            $file = $content->getField( $identifier );

            if( !empty( $file->value ) )
            {
                $fileValue = $file->value;
                $contentInfo =  $content->contentInfo;

                $fileName = $fileValue->fileName;
                $fileNameArray = explode( '.', $fileName );
                $fileExtension = trim( $fileNameArray[1] );
                $fileMimeType = $fileValue->mimeType;
                $fileUri = 'content/download/' . $contentInfo->id . '/' . $file->id . '/version/' . $contentInfo->currentVersionNo .  "/file/" . $fileName;

                break;
            }
        }

        if( !empty( $fileMimeType ) && !empty( $fileExtension ) )
        {
            return $this->addFileUrl( $fileUri, $fileMimeType, $link, $message );
        }

        return false;
    }

    /**
     * Search for item in array and return resulting containing array
     *
     * @param string $needle Text to find
     * @param array $haystack Array of content to search within
     * @param bool $strict Wheather to perform strict matching. Unused
     * @return array
     */
    public function findNeedleInArrayAndReturnContainingArray( $needle, $haystack = false, $strict = true )
    {
        $found = false;

        if( $haystack === false )
        {
            $haystack = $this->helperMimeMap;
        }

        foreach( $haystack as $key => $item )
        {
            if( $item == $needle )
            {
                $found = true;
                break;
            }
            elseif ( is_array( $item ) )
            {
                $found = $this->findNeedleInArrayAndReturnContainingArray( $needle, $item, $strict );
                if( $found === true )
                {
                    $found = $item;
                    break;
                }
            }
        }

        return $found;
    }

    /**
     * Generate document readers from document reader extensions
     *
     * @return bool
     */
    public function buildDocumentReaders()
    {
        $pageDataFileExtensions = array();
        $results = array();

        try
        {
            $documentReaderExtensions = $this->get();

            if( $this->displayDebug && $this->displayDebugLevel >= 1 )
            {
                echo 'In, BcDocumentReader : buildDocumentReadersResults : Current Document Reader Extensions List : ';
                var_dump( $documentReaderExtensions );
            }

            if ( empty( $documentReaderExtensions ) )
            {
                return $results;
            }

            foreach ( $documentReaderExtensions as $fileTypeExtension => $fileTypeDocumentInfo )
            {
                if( empty( $fileTypeDocumentInfo['mimetype'] ) )
                {
                    $mimeTypeGuesser = $this->container->get('brookinsconsulting.document_reader.mimetype.extension.guesser');
                    $mimeType = $mimeTypeGuesser->guess( $fileTypeExtension );
                    $mimeContentType = $mimeType[1];

                    if ( !$mimeType && $this->logger !== null )
                    {
                        $message =  __METHOD__ . ' - Warning: File extension, "' . $fileTypeExtension . '" and File mimeType, "' . $fileMimeType . '" not found in ExtensionMimeTypeGuesser static mimeType / fileExtension mappings';

                        $this->logger->warning( $message );

                        if( $this->displayDebug && $this->displayDebugLevel >= 2 )
                        {
                            echo '<hr />' . $message . '<hr />';
                        }

                        continue;
                    }
                    elseif( $this->displayDebug && $this->displayDebugLevel >= 1 )
                    {
                        echo '<hr />In, BcDocumentReader : buildDocumentReadersResults : Notice: Used ExtensionMimeTypeGuesser service to detect mimeType: "' . $mimeContentType . '" for file extension, ' . $fileTypeExtension . '<hr />';
                    }
                }
                else
                {
                    $mimeContentType = $fileTypeDocumentInfo['mimetype'];
                }

                if( $mimeContentType )
                {
                    $mimeTypeHelperMapping = $this->findNeedleInArrayAndReturnContainingArray( $mimeContentType );
                    $mimeTypeHelperMappingType = $mimeTypeHelperMapping["type"];
                    $mimeTypeHelperGroup = $this->helpers[$mimeTypeHelperMappingType];

                    if( $this->displayDebug && $this->displayDebugLevel >= 2 )
                    {
                        echo 'In, BcDocumentReader : buildDocumentReadersResults : Detected Matching Document Reader Helper Application Group for mimeType: ' . $mimeContentType;
                        var_dump( $mimeTypeHelperMappingType );
                    }

                    if ( !$mimeTypeHelperGroup )
                    {
                        if ( $this->logger !== null )
                        {
                            $message =  __METHOD__ . ' - Warning: File extension, "' . $fileTypeExtension . '" and File mimeType, "' . $fileMimeType . '" not found in yaml mimetype settings, mimetypes.yml: helper_mime_map group.';

                            $this->logger->warning( $message );

                            if( $this->displayDebug && $this->displayDebugLevel >= 2 )
                            {
                                echo '<hr />' . $message . '<hr />';
                            }
                        }

                        continue;
                    }

                    $mimeTypeIconGuesser = $this->container->get('brookinsconsulting.document_reader.mimetype.icon.guesser');
                    $mimeTypeIcon = $mimeTypeIconGuesser->guess( $mimeContentType, false );

                    if( !$this->findNeedleInArrayAndReturnContainingArray( $mimeTypeHelperMappingType, $results ) )
                    {
                        $results[] = array( 'file_extension' => $fileTypeExtension,
                                            'mime_type_group' => $mimeTypeHelperMappingType,
                                            'file_type_name' => $mimeTypeHelperGroup['name'],
                                            'viewer_name' => $mimeTypeHelperGroup['viewer_name'],
                                            'viewer_url' => $mimeTypeHelperGroup['viewer_url'],
                                            'mime_type' => $mimeContentType, //$mimeTypeHelperGroup["icon"],
                                            'mime_type_icon' => $mimeTypeIcon['image'],
                                            'mime_type_icon_height' => $mimeTypeIcon['height'],
                                            'mime_type_icon_width' => $mimeTypeIcon['width'] );
                    }
                }
            }

            $this->add( $results, 'document_readers' );

            return true;
        }
        catch( \eZ\Publish\API\Repository\Exceptions\NotFoundException $e )
        {
            if ( $this->logger !== null )
            {
                $message = __METHOD__ . ' - Warning: NotFoundException: "' . $e . '"';
                $this->logger->warning( $message );

                if( $this->displayDebug && $this->displayDebugLevel >= 2 )
                {
                    echo '<hr />' . $message . '<hr />';
                }
            }

            return false;
        }
    }
}
