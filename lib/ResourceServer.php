<?php
declare(strict_types = 1);

/**
 * Interface for serving JavaScript/CSS Resource objects
 *
 * @package AWonderPHP/NotReallyPsrResourceManager
 * @author  Alice Wonder <paypal@domblogger.net>
 * @license https://opensource.org/licenses/MIT MIT
 * @link    https://github.com/AliceWonderMiscreations/NotReallyPsrResourceManager
 */
/*
 +-------------------------------------------------+
 |                                                 |
 | Copyright (C) 2018 Alice Wonder Miscreations    |
 |  May be used under the terms of the MIT license |
 |                                                 |
 +-------------------------------------------------+
 | Purpose: Interface for serving Resource objects |
 +-------------------------------------------------+
*/

namespace AWonderPHP\NotReallyPsrResourceManager;

/**
 * An interface for serving JavaScript and CSS files through a PHP file wrapper
 *
 * It is intended for classes that implement this interface to extend the
 * \AWonderPHP\FileResource\ResourceServer abstract class
 */
interface ResourceServer
{
    /**
     * Creates a FileResource object from the arguments and then serves the file using serveFileResourc()
     * if the implementing class extends \AWonderPHP\FileResource\ResourceServer
     *
     * @param string      $vendor  The top level vendor of the script, lower case
     * @param string      $product The product name the script is part of, lower case
     * @param string      $name    The basic name of the script (e.g. jquery), lower case
     * @param int|string  $version The version of the script requested. If the argument is
     *                             an integer, it should be recast as a string.
     * @param null|string $variant The variant of the script requested
     *
     * @return bool True on success, False on Failure
     */
    public function serveJavaScript(
        string $vendor,
        string $product,
        string $name,
        $version,
        $variant = null
    );
    
    /**
     * Creates a FileResource object from the arguments and then serves the file using serveFileResourc()
     * if the implementing class extends \AWonderPHP\FileResource\ResourceServer
     *
     * @param string      $vendor  The top level vendor of the script, lower case
     * @param string      $product The product name the script is part of, lower case
     * @param string      $name    The basic name of the script (e.g. jquery), lower case
     * @param int|string  $version The version of the script requested. If the argument is
     *                             an integer, it should be recast as a string.
     * @param null|string $variant The variant of the script requested
     *
     * @return bool True on success, False on Failure
     */
    public function serveCSS(
        string $vendor,
        string $product,
        string $name,
        $version,
        $variant = null
    );
}

?>