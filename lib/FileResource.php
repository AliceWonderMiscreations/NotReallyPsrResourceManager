<?php
declare(strict_types = 1);

namespace AWonderPHP\NotReallyPsrResourceManager;

/**
 * Abstract class for resources
 */
abstract class FileResource
{
    /**
     * The MIME type of the resource
     *
     * @var null|string
     */
    protected $mime = null;

    /**
     * Algorithm : checksum
     *
     * The checksum is either hex or base64 encoded. Example:
     * sha256:708c26ff77c1fa15ac9409a5cbe946fe50ce203a73c9b300960f2adb79e48c04
     *
     * @var null|string
     */
    protected $checksum = null;
    
    /**
     * Hashing algorithms that are currently supported by browsers for use with
     * the integrity attribute
     */
    protected $validIntegrityAlgo = array('sha256', 'sha384', 'sha512');

    /**
     * Filesystem location, only applicable when a local resource
     *
     * @var null|string
     */
    protected $filepath = null;

    /**
     * Modification date of file - may not necessarily match the modification date of the actual
     * file as seen by the filesystem. ISO 8601 in 'Y-m-d\TH:i:sO' - aka date('c')
     *
     * @var null|string
     */
    protected $lastmod = null;

    // subset from parse_url

    /**
     * The protocol scheme
     *
     * @var null|string
     */
    protected $urlscheme = null;

    /**
     * The host name
     *
     * @var null|string
     */
    protected $urlhost = null;

    /**
     * The url path
     *
     * @var null|string
     */
    protected $urlpath = null;

    /**
     * The query string
     *
     * @var null|string
     */
    protected $urlquery = null;
  
    /**
     * Return the mime type
     *
     * @return null|string
     */
    public function showMime()
    {
        return $this->mime;
    }

    /**
     * Return the checksum
     *
     * @return null|string
     */
    public function showChecksum()
    {
        return $this->checksum;
    }
    
    /**
     * Validates the file matches the checksum
     *
     * @return null|boolean Returns null if the file can not be found or any
     *                      other reason that prevents an actual verification
     *                      from being performed. If verification can be
     *                      performed, returns True if verified, False if it
     *                      does not verify.
     */
    public function validateFile()
    {
        if ((is_null($this->filepath)) || (is_null($this->checksum))) {
            return null;
        }
        if (! file_exists($this->filepath)) {
            return null;
        }
        list($algo, $hash) = explode(':', $this->checksum, 2);
        if (! in_array($algo, hash_algos())) {
            return null;
        }
        if (ctype_xdigit($hash)) {
            $raw = hex2bin($hash);
        } else {
            $raw = base64_decode($hash);
        }
        $filehash = hash_file($algo, $this->filepath, true);
        if ($raw === $filehash) {
            return true;
        }
        return false;
    }

    /**
     * Returns the URI to the resource. For http the checksum MUST exist so
     * that an integrity attribute will exist.
     *
     * @return null|string
     */
    public function getSrcAttribute()
    {
        $return = '';
        if ((! is_null($this->urlscheme)) && (! is_null($this->urlhost))) {
            if (! in_array($this->urlscheme, array('http', 'https'))) {
                return null;
            }
            if ($this->urlscheme === 'http') {
                if (is_null($this->checksum)) {
                    return null;
                }
                list($algo, $checksum) = explode(':', $this->checksum);
                if (! in_array($algo, $this->validIntegrityAlgo)) {
                    return null;
                }
            }
            $return = $this->urlscheme . '://' . $this->urlhost;
        }
        if (! is_null($this->urlpath)) {
            $return .= $this->urlpath;
        }
        if (! is_null($this->urlquery)) {
            $return .= '?' . $this->urlquery;
        }
        if (strlen($return) === 0) {
            return null;
        }
        return $return;
    }

    /**
     * Returns the UNIX timestamp from the lastmod property
     *
     * @return null|int
     */
    public function getTimestamp()
    {
        if (is_null($this->lastmod)) {
            return null;
        }
        if ($ts = strtotime($this->lastmod)) {
            return $ts;
        }
        return null;
    }
}

?>