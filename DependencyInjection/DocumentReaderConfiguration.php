<?php
/**
 * File containing the DocumentReaderConfiguration class part of the BcDocumentReaderBundle package.
 *
 * @copyright Copyright (C) Brookins Consulting. All rights reserved.
 * @license For full copyright and license information view LICENSE and COPYRIGHT.md file distributed with this source code.
 * @version //autogentag//
 */

namespace BrookinsConsulting\BcDocumentReaderBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class DocumentReaderConfiguration implements ConfigurationInterface
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
                ->arrayNode( 'options' )
                    ->info( 'Available documentreader options' )
                    ->isRequired()
                    ->children()
                        ->booleanNode( 'display_debug' )
                            ->defaultFalse()
                            ->isRequired()
                        ->end()
                        ->scalarNode( 'display_debug_level' )
                            ->isRequired()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode( 'theme' )
                    ->info( 'Available theme icon options' )
                    ->isRequired()
                    ->children()
                        ->scalarNode( 'size' )
                            ->defaultValue( 'small' )
                            ->isRequired()
                        ->end()
                        ->scalarNode( 'theme' )
                            ->defaultValue( 'crystal' )
                            ->isRequired()
                        ->end()
                        ->scalarNode( 'admin_theme' )
                            ->defaultValue( 'crystal-admin' )
                            ->isRequired()
                        ->end()
                        ->scalarNode( 'legacy_icons_directory' )
                            ->isRequired()
                        ->end()
                        ->arrayNode( 'file_field_identifiers' )
                            ->isRequired()
                            ->prototype( 'scalar' )->end()
                        ->end()
                        ->arrayNode( 'sizes' )
                            ->isRequired()
                            ->children()
                                ->scalarNode( 'normal' )
                                    ->defaultValue( '32x32' )
                                    ->isRequired()
                                ->end()
                                ->scalarNode( 'small' )
                                    ->defaultValue( '16x16' )
                                    ->isRequired()
                                ->end()
                            ->end()
                        ->end()
                        ->scalarNode( 'default_icon' )
                            ->defaultValue( 'mimetypes/binary.png' )
                            ->isRequired()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode( 'helpers' )
                    ->info( 'Available file link helper applications' )
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->prototype( 'array' )
                    ->children()
                        ->scalarNode( 'name' )
                            ->isRequired()
                        ->end()
                        ->scalarNode( 'viewer_name' )
                            ->isRequired()
                        ->end()
                        ->scalarNode( 'viewer_url' )
                            ->isRequired()
                        ->end()
                        ->scalarNode( 'icon' )
                            ->isRequired()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $TreeBuilder;
    }
}
