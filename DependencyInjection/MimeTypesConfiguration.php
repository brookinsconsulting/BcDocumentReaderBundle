<?php
/**
 * File containing the MimeTypesConfiguration class part of the BcDocumentReaderBundle package.
 *
 * @copyright Copyright (C) Brookins Consulting. All rights reserved.
 * @license For full copyright and license information view LICENSE and COPYRIGHT.md file distributed with this source code.
 * @version //autogentag//
 */

namespace BrookinsConsulting\BcDocumentReaderBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class MimeTypesConfiguration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $TreeBuilder = new TreeBuilder();

        $RootNode = $TreeBuilder->root( 'parameters' );

        $RootNode
            ->children()
                ->arrayNode( 'helper_mime_map' )
                    ->info( 'Available Helper mime type mime map' )
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->prototype( 'array' )
                        ->children()
                            ->scalarNode( 'type' )
                                ->isRequired()
                            ->end()
                            ->scalarNode( 'mimeType' )
                                ->isRequired()
                            ->end()
                            ->arrayNode( 'extensions' )
                                ->prototype( 'scalar' )
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $TreeBuilder;
    }
}
