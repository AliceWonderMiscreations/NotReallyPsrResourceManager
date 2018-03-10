<?php
declare(strict_types = 1);

namespace AWonderPHP\NotReallyPsrResourceManager;

/**
 * An interface for serving JavaScript and CSS files through a PHP file wrapper
 */

interface ResourceServer
{
    /**
     * Accepts a FileResource object as an argument and serves the file
     *
     * @param \AWonderPHP\NotReallyPsrResourceManager\FileResource $fileResource The instance of FileResource to be
     *                                                                           served.
     * @param bool                                                 $minify       If the FileResource has the boolean
     *                                                                           property minified set to false *and*
     *                                                                           the checksum property set to null,
     *                                                                           implementors *may* minify on the fly
     *                                                                           if this is set to true.
     *
     * @return bool True on success, False on failure
     */
    public function serveFileResource($fileResource, bool $minify = false);
    
    /**
     * Creates a FileResource object from the arguments and then serves the file using serveFileResourc()
     *
     * @param string      $vendor  The top level vendor of the script, lower case
     * @param string      $product The product name the script is part of, lower case
     * @param string      $name    The basic name of the script (e.g. jquery), lower case
     * @param int|string  $version The version of the script requested. If the argument is
     *                             an integer, it should be recast as a string.
     * @param null|string $variant The variant of the script requested
     * @param bool        $minify  If the resulting FileResource has the boolean property minified set to false *and*
     *                             the checksum property set to null, implementors *may* minify on the fly if this is
     *                             set to true.
     *
     * @return bool True on success, False on Failure
     */
    public function serveJavaScript(
        string $vendor,
        string $product,
        string $name,
        $version,
        $variant = null,
        bool $minify = false
    );
    
    /**
     * Creates a FileResource object from the arguments and then serves the file using serveFileResourc()
     *
     * @param string      $vendor  The top level vendor of the script, lower case
     * @param string      $product The product name the script is part of, lower case
     * @param string      $name    The basic name of the script (e.g. jquery), lower case
     * @param int|string  $version The version of the script requested. If the argument is
     *                             an integer, it should be recast as a string.
     * @param null|string $variant The variant of the script requested
     * @param bool        $minify  If the resulting FileResource has the boolean property minified set to false *and*
     *                             the checksum property set to null, implementors *may* minify on the fly if this is
     *                             set to true.
     *
     * @return bool True on success, False on Failure
     */
    public function serveCSS(
        string $vendor,
        string $product,
        string $name,
        $version,
        $variant = null,
        bool $minify = false
    );
}

?>