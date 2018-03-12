<?php
declare(strict_types = 1);

namespace AWonderPHP\NotReallyPsrResourceManager;

/**
 * Interface for JavaScript resource objects.
 *
 * It is intended for classes that implement this interface to extend the
 * \AWonderPHP\FileResource\FileResource abstract class
 */
interface JavaScriptResource
{
/* These methods will be met by extending \AWonderPHP\FileResource\FileResource */
    
    /**
     * Return the mime type
     *
     * @return null|string
     */
    public function getMimeType();
    
    /**
     * Return the checksum
     *
     * @return null|string
     */
    public function getChecksum();
    
    /**
     * Returns null or the value to use with a crossorigin attribute
     *
     * @return null|string
     */
    public function getCrossOrigin();
    
    /**
     * Returns the filepath to the resource or null if the property is not defined
     *
     * @return null|string
     */
    public function getFilePath();
    
    /**
     * Validates the file matches the checksum
     *
     * @return null|boolean Returns null if the file can not be found or any
     *                      other reason that prevents an actual verification
     *                      from being performed. If verification can be
     *                      performed, returns True if verified, False if it
     *                      does not verify.
     */
    public function validateFile();
    
    /**
     * Returns the URI to the resource. For http the checksum MUST exist so
     * that an integrity attribute will exist.
     *
     * @param null|string $prefix A path to put at the beginning of the object urlpath property
     *
     * @return null|string
     */
    public function getSrcAttribute($prefix = null);
    
    /**
     * Returns null or the value to use with a script node integrity attribute
     *
     * @return null|string
     */
    public function getIntegrityAttribute();
    
    /**
     * Returns the UNIX timestamp from the lastmod property
     *
     * @return null|int
     */
    public function getTimestamp();
    
/* These methods are unique from \AWonderPHP\FileResource\FileResource */
    
    /**
     * Should return application/javascript or module
     *
     * @return string
     */
    public function getTypeAttribute();
    
    /**
     * Returns whether or not the script is async
     *
     * @return bool
     */
    public function getAsyncAttribute();
    
    /**
     * Returns whether or not to defer execution
     *
     * @return bool
     */
    public function getDeferAttribute();
    
    /**
     * Returns whether or not to use nomodule
     *
     * @return bool
     */
    public function getNoModuleAttribute();
    
    //?? text

    /**
     * Generates a DOMDocument node
     *
     * @param \DOMDocument $dom   The DOMDocument class instance
     * @param null|string  $nonce A nonce to use with Content Security Policy
     *
     * @return \DOMNode
     */
    public function generateScriptDomNode($dom, $nonce = null);
    
    /**
     * Generates an (X)HTML string
     *
     * @param boolean     $xml   Whether or not to generate self-closing XML style string, should
     *                           default to false
     * @param null|string $nonce A nonce to use with Content Security Policy
     *
     * @return string
     */
    public function generateScriptString(bool $xml = false, $nonce = null);
}

?>