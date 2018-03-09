<?php

namespace AWonderPHP\NotReallyPsrResourceManager;

/**
 * Interface for ResourceManager
 *
 * Right now JS only but obviously will manage CSS as well in future
 */
interface ResourceManager
{

    /**
     * Attempts to find the JSON configuration file for the specified JavaScript and when
     * found, returns a JavaScriptResource object
     *
     * @param string      $vendor  The top level vendor of the script, lower case
     * @param string      $product The product name the script is part of, lower case
     * @param string      $name    The basic name of the script (e.g. jquery), lower case
     * @param int|string  $version The version of the script requested. If the argument is
     *                             an integer, it should be recast as a string.
     * @param null|string $variant The variant of the script requested
     *
     * @return null|\AWonderPHP\NotReallyPsrResourceManager\FileResource
     */
    public function getJavaScript(string $vendor, string $product, string $name, $version, $variant = null);
}

?>