<?php

namespace AWonderPHP\NotReallyPsrResourceManager;

/**
 * An interface for when things go horribly wrong
 *
 * Yes, looks kind of empty, but it should be. Actual exception code should
 * extend an actual exception class and then just implement this so that it
 * easy to catch them as affiliated with the ResourceManager.
 */
interface ResourceManagerException
{
}

?>