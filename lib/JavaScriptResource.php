<?php
declare(strict_types = 1);

namespace AWonderPHP\NotReallyPsrResourceManager;

/**
 * Interface for JavaScript Objects based upon the JS_JSON.md
 * file in the top level directory
 */
interface JavaScriptResource
{
    
    /**
     * Must output a valid URL or a valid local path to use in a script src attribute
     *
     * @return string
     */
    public function getSrcAttribute();
    
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
     * Must generate a JavaScript integrity string if this->checksum is not null
     *
     * @return null|string The contents of the integrity attribute
     */
    public function getIntegrityAttribute();
    
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