<?php
/**
 * File containing the BcDocumentReaderPersistence class part of the BcDocumentReaderBundle package.
 *
 * @copyright Copyright (C) Brookins Consulting. All rights reserved.
 * @license For full copyright and license information view LICENSE and COPYRIGHT.md file distributed with this source code.
 * @version //autogentag//
 */

namespace BrookinsConsulting\BcDocumentReaderBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;

class BcDocumentReaderPersistence
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @var \BrookinsConsulting\BcPageDataBundle\Services\BcPageDataPersistence
     */
    protected $pageDataPersistence;

    /**
     * @var array
     */
    protected $documentReaderExtensions;

    /**
     * @var array
     */
    protected $documentReaders;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var bool
     */
    protected $displayDebug;

    /**
     * @var int
     */
    protected $displayDebugLevel;

    /**
     * Constructor
     *
     * @param Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param array $options
     */
    public function __construct( ContainerInterface $container, array $options = array() )
    {
        $this->container = $container;
        $this->options = $options[0]['options'];
        $this->displayDebug = $this->options['display_debug'] == true ? true : false;
        $this->displayDebugLevel = is_numeric( $this->options['display_debug_level'] ) ? $this->options['display_debug_level'] : 0;
        $this->pageDataPersistence = $this->container->get( 'brookinsconsulting.page_data_persistence' );

        if ( $this->pageDataPersistence->has( 'document_readers' ) )
        {
            $this->documentReaders = $this->pageDataPersistence->get( 'document_readers' );
        }
        else
        {
            $this->set( array(), 'document_readers' );
            $this->documentReaders = $this->pageDataPersistence->get( 'document_readers' );
        }

        if ( $this->pageDataPersistence->has( 'document_reader_extensions' ) )
        {
            $this->documentReaderExtensions = $this->pageDataPersistence->get( 'document_reader_extensions' );
        }
        else
        {
            $this->set( array() );
            $this->documentReaderExtensions = $this->pageDataPersistence->get( 'document_reader_extensions' );
        }

        if ( $this->displayDebug && $this->displayDebugLevel >= 3 )
        {
            echo "<span style='color:000000;'>BcDocumentReaderPersistence : __construct : Document Reader Extensions</span><br />\n";
            var_dump( $this->pageDataPersistence->get( 'document_reader_extensions' ) );

            echo "<span style='color:000000;'>BcDocumentReaderPersistence : __construct : Document Readers</span><br />\n";
            var_dump( $this->get( 'document_readers' ) );
        }
    }

    /**
     * Test if persistent storage variable exists and return true
     *
     * @param string $name Persistent storage variable name
     * @return bool
     */
    public function has( $name = 'document_reader_extensions' )
    {
        return $this->pageDataPersistence->has( $name );
    }

    /**
     * Gets the value of persistent storage variable
     *
     * @param string $name Persistent storage variable name
     * @param string $default The default value if the parameter key does not exist
     * @param bool $default If true, a path like foo[bar] will find deeper items
     * @return mixed
     */
    public function get( $name = 'document_reader_extensions', $default = null, $deep = false )
    {
        return $this->pageDataPersistence->get( $name, $default, $deep );
    }

    /**
     * Sets the value of persistent storage variable
     *
     * @param mixed $value String or Array (of arrays) containing the file extension and related file meta information
     * @param string $name Persistent storage variable name
     */
    public function set( $value, $name = 'document_reader_extensions' )
    {
        $this->pageDataPersistence->set( $name, $value );
    }

    /**
     * Adds an array of file extension and related meta information into the persistent storage if file extension does not already exist within the persistent storage
     *
     * @param mixed $value String or Array (of arrays) containing the file extension and related file meta information
     * @param string $name Persistent storage variable name
     */
    public function add( $value, $name = 'document_reader_extensions' )
    {
        if ( !is_array( $value ) )
        {
            $value = array( $value );
        }
        else
        {
            $valueKeys = array_keys( $value );
            $valueKeys = $valueKeys[0];
        }

        if ( !$this->inArrayRecursive( $valueKeys, array_keys( $this->get( $name ) ) ) )
        {
            $this->pageDataPersistence->set( $name, array_merge( $this->get( $name ), $value ) );

            if ( $this->displayDebug && $this->displayDebugLevel >= 0 && $name === 'document_reader_extensions' )
            {
                echo 'In, BcDocumentReaderPersistence : add : Added New Unique Mimetype Document Reader: ';
                var_dump( $value );

                if ( $this->displayDebugLevel >= 3 )
                {
                    echo 'In, BcDocumentReaderPersistence : add : Post Add Current Document Reader Extensions List : ';
                    var_dump( $this->get( $name ) );
                }
            }
        }
        else if ( !in_array( $value, $this->get( $name ) ) )
        {
            $this->pageDataPersistence->set( $name, array_merge( $this->get( $name ), $value ) );
        }
    }

    /**
     * Search for string in array
     *
     * @param string $needle Text to find
     * @param array $haystack Array of content to search within
     * @param bool $strict Wheather to perform strict matching. Unused
     * @return bool
     */
    public function inArrayRecursive( $needle, $haystack = false, $strict = true )
    {
        $found = false;

        foreach ( $haystack as $key => $item )
        {
            if ( $item == $needle )
            {
                $found = true;
                break;
            }
            else if ( is_array( $item ) )
            {
                $found = $this->inArrayRecursive( $needle, $item, $strict );
                if ( $found === true )
                {
                    break;
                }
            }
        }

        return $found;
    }
}
