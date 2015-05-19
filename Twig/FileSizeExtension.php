<?php
/**
 * File containing the FileSizeExtension class part of the BcDocumentReaderBundle package.
 * This file is based on the class by the same class name within ezpublish-kernel @ tag : v6.0.0-alpha1. Re-implemmented uniquely to support older versions of eZ Publish 5.x
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @copyright Copyright (C) Brookins Consulting. All rights reserved.
 * @license For full copyright and license information view LICENSE and COPYRIGHT.md file distributed with this source code.
 * @version //autogentag//
 */

namespace BrookinsConsulting\BcDocumentReaderBundle\Twig;

use Locale;
use NumberFormatter;
use Twig_Extension;
use Twig_SimpleFilter;
use Symfony\Component\Translation\TranslatorInterface;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\MVC\Symfony\Locale\LocaleConverterInterface;

/**
 * Class FileSizeExtension
 *
 * @package eZ\Publish\Core\MVC\Symfony\Templating\Twig\Extension
 */
class FileSizeExtension extends Twig_Extension
{
    /**
     * @param TranslatorInterface $translator
     */
    protected $translator;

    /**
     * @param array $suffixes
     */
    protected $suffixes;

    /**
     * @param ConfigResolverInterface $configResolver
     */
    protected $configResolver;

    /**
     * @param  LocaleConverterInterface $localeConverter
     */
    protected $localeConverter;

    /**
     * @param TranslatorInterface $translator
     * @param ConfigResolverInterface $configResolver
     * @param LocaleConverterInterface $localeConverter
     * @param array $suffixes
     */
    public function __construct( TranslatorInterface $translator, array $suffixes, ConfigResolverInterface $configResolver, LocaleConverterInterface $localeConverter )
    {
        $this->translator = $translator;
        $this->suffixes = $suffixes;
        $this->configResolver = $configResolver;
        $this->localeConverter = $localeConverter;
    }

    private function getLocale()
    {
        foreach ( $this->configResolver->getParameter( 'languages' ) as $locale )
        {
            $convertedLocale = $this->localeConverter->convertToPOSIX( $locale );
            if ( $convertedLocale !== null )
            {
                return $convertedLocale;
            }
        }
        return Locale::getDefault();
    }

    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return array An array of filters
     */
    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter( 'bc_file_size', array( $this, 'sizeFilter' ) ),
        );
    }

    /**
     * Returns the binary file size, $precision will determine the decimal number precision,
     * and the Locale will alter the format of the result by choosing between coma or point pattern.
     *
     * @param integer $number
     * @param integer $precision
     *
     * @return string
     */
    public function sizeFilter( $number, $precision )
    {
        $mod = 1000;
        $index = count( $this->suffixes );
        if ( $number < pow( $mod, $index ) )
        {
            for ( $i = 0; $number >= $mod; $i++ )
            {
                $number /= $mod;
            }
        }
        else
        {
            $number /= pow( $mod, ( $index - 1 ) );
            $i = ( $index - 1 );
        }
        $formatter = new NumberFormatter( $this->getLocale(), NumberFormatter::PATTERN_DECIMAL );
        $formatter->setPattern( $formatter->getPattern() . " " . $this->translator->trans( $this->suffixes[$i] ) );
        $return = $formatter->format( round( $number, $precision ) );
        return $return;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'brookinsconsulting_fileSize';
    }
}
