<?php
declare(strict_types = 1);

/**
 * An exception interface for JS/CSS resource objects
 *
 * @package AWonderPHP/NotReallyPsrResourceManager
 * @author  Alice Wonder <paypal@domblogger.net>
 * @license https://opensource.org/licenses/MIT MIT
 * @link    https://github.com/AliceWonderMiscreations/NotReallyPsrResourceManager
 */
/*
 +----------------------------------------------------+
 |                                                    |
 | Copyright (C) 2018 Alice Wonder Miscreations       |
 |  May be used under the terms of the MIT license    |
 |                                                    |
 +----------------------------------------------------+
 | Purpose: Catchable Exception Interface             |
 +----------------------------------------------------+
*/

namespace AWonderPHP\NotReallyPsrResourceManager;

/**
 * An exception interface for JS/CSS resource objects
 *
 * Yes, looks kind of empty, but it should be. Actual exception code should
 * extend an actual exception class and then just implement this so that it
 * easy to catch them as affiliated with the ResourceManager.
 */
interface ResourceManagerException
{
}

?>