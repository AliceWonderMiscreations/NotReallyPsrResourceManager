JavaScript and CSS ResourceManager
==================================

This is a set of interfaces that PHP libraries can use to provide sane
management of third party JavaScript and CSS resources uses by the web
applications.

The current namespace `\AWonderPHP\NotReallyPsrResourceManager` sucks and will
change. It was never intended as permanent. I was hoping a standards body would
want to take this idea and create some standard interfaces with fancy lingo
that includes [RFC 2119](http://tools.ietf.org/html/rfc2119).

While not strictly required, it is highly recommended that classes that
implement these interfaces extend the abstract classes within the
[`\AwonderPHP\FileResource`](https://github.com/AliceWonderMiscreations/FileResource)
namespace.

That allows the resulting objects to be served by any class that understands
how to serve a `FileResource` object.

It is also intended that the JavaScriptResource and CssResource objects be
generated from JSON configuration files as described in this document.

1. [`FileResource Methods`](#fileresource-methods)
2. [`JavaScriptResource Interface Methods`](#javascriptresource-interface-methods)
3. [`CssResource Interface Methods`](#cssresource-interface-methods)
4. [`ResourceManager Interface`](#resourcemanager-interface)
5. [`JSON Configuration File`](#json-configuration-file)
6. [`JavaScript JSON`](#javascript-json)
7. [`CSS JSON`](#css-json)
8. [`File System and Config File Naming`](#file-system-and-config-file-naming)
9. [`Wrapper Script`](#wrapper-script)


FileResource Methods
--------------------

These methods are specified in both the `JavaScriptResource` and `CssResource`
interfaces and are identical to the methods of the same name in the abstract
`\AwonderPHP\FileResource\FileResource` class:

* `public function getMimeType()`  
  Returns the `$mime` property.

* `public function getChecksum()`  
  Returns the `$checksum` property.

* `public function getCrossOrigin()`  
  Returns the `$crossorigin` property.

* `public function getFilePath()`  
  Returns the `$filepath` property.

* `public function validateFile()`  
  If the `$checksum` property is set *and* the `$filepath` property is set
  *and* the file exists, returns `true` if the file matches the checksum and
  `false` if it does not.

* `public function getSrcAttribute($prefix = null)`  
  Builds the contents of the `src` or `href` attribute needed to embed the
  resource in a web page. Note that this will return `null` if the `$urlscheme`
  property is `http` and the file does not have a `$checksum` property that
  uses an algorithm in the `$validIntegrityAlgo` property. The optional
  parameter `$prefix` is a file system path to put in front of the `$urlpath`
  property, useful for web applications using a wrapper to serve the file.

* `public function getIntegrityAttribute()`  
  Builds the contents of an `integrity` attribute, if the `$checksum` property
  uses a suitable algorithm.

* `public function getTimestamp()`  
  If the `$lastmode` property is not null, returns a UNIX time stamp (seconds
  from UNIX epoch).


JavaScriptResource Interface Methods
------------------------------------

These methods are in the JavaScriptResource interface but are *not* defined in
the previously mentioned `FileResource` abstract class.

* `getTypeAttribute()`  
  What goes into the `type` attribute of a `<script>` node. Usually the MIME
  type but not always.

* `getAsyncAttribute()`  
  Whether or not the Boolean `async` attribute should be present.

* `getDeferAttribute();`  
  Whether or not the Boolean `defer` attribute should be present.

* `getNoModuleAttribute()`  
  Whether or not the Boolean `nomodule` attribute should be present.

* `generateScriptDomNode($dom, $nonce = null)`  
  Creates a `\DOMNode` `<script>` node, with an optional nonce.

* `generateScriptString(bool $xml = false, $nonce = null)`  
  Creates a string, either HTML or XHTML, for the script node with an optional
  nonce. If the `$xml` parameter is `true`, a string that is XML compliant
  should be returned (every attribute is a `key="value"` pair, Boolean
  attributes are usually just `key="key"` but it does not really matter. Script
  node is self-closing). When the `$xml` parameters is `false`, the default, an
  HTML compliant string is generated (boolean attributes *may* just be `key`
  and are not required to have an `="value"` and a self-closing script tags are
  not recognized as closed, so a closing `</script>` is required).

Both the `generateScriptDomNode()` and `generateScriptString()` methods should
call the `getSrcAttribute()` method, so the constructor of an implementing
class should have a property for the `$prefix` that is `null` by default but
can be set by the constructor.


CssResource Interface Methods
-----------------------------

* `getTypeAttribute()`  
  What goes into the `type` attribute of a CSS `<link>` node. This should
  ALWAYS return `text/css`

* `getMediaAttribute()`  
  Returns what goes into the `media` attribute of a CSS `<link>` node. This is
  rarely used, but it is very powerful and should be used more often IMHO as it
  can reduce the bandwidth the client needs to have to successfully use a web
  application.

* `getHreflangAttribute()`  
  Returns what goes into a `hreflang` attribute. When not null, it __MUST__ be
  a [BCP47](https://tools.ietf.org/html/bcp47) string.

* `getReferrerPolicyAttribute()`  
  Returns what goes into a `referrerpolicy` attribute of a CSS `<link>` node. I
  suspect it will not be used much, but it is there.

* `getRelAttribute()`  
  The contents of the `rel` attribute, should return `stylesheet`.

ResourceManager Interface
-------------------------

This is the interface that defines how web applications will interact get the
`JavaScriptResource` and `CssResource` objects that they need.

It describes two public methods:

* `getJavaScript(string $vendor, string $product, string $name, $version, $variant = null);`

* `getCSS(string $vendor, string $product, string $name, $version, $variant = null);`

The web application calls those methods which will either return `null` if the
implementing class can not create the object, or it returns eith a
`JavaScriptResource` or `CssResource` implementing object, depending upon which
of the methods were called.

### The Parameters:

* `$vendor` String:  
  Lower case vendor string for who packaged the JavaScript or CSS, similar to
  the first level namespace in PSR-4 PHP classes but lower case. In Composer
  terms, it would be the top level directory within the Composer created
  `vendor` directory of your web application.

* `$product` String:  
  Lower case product string for the package the JavaScript or CSS is part of,
  similar to the second level namespace in PSR-4 PHP classes but lower case. In
  Composer terms, it would be the directory within the previously mentioned
  directory.

* `$name` String:  
  The name of the JavaScript or CSS sans version and other info, e.g. `jquery`
  or `jquery-ui` or `normalize`.

* `$version` String or Integer:  
  The version needed, e.g. `3.3.1` to specifically ask for jQuery 3.3.1 or
  `3.3` to specifically ask for latest in `3.3` branch or simply `3` to ask
  for the latest in 3 branch.

* `$variant` String or Null:  
  The variant of the JavaScript needed, e.g. `min` for minified or `slim` or
  `slim.min`.

With that information, the `ResourceManager` implementing class would be able
to find the JSON file describing the object and load it into an object.

The constructor for an implementing class should define the `Base` directory
directory resources are installed into so the methods can find the needed
configuration files and also the `$prefix` to use with the `getSrcAttribute()`
method.

### Example Usage:

    $base = "/whatever/path";
    $RM = new \namespace\whatever\ImplementingClass($base);
    $jsObj = $RM->getJavaScript('flossjs', 'jquery', 'jquery', 3, "min");

Then from `$jsObj` the web application can create the `<script>` node needed.
  

JSON Configuration File
-----------------------

The ResourceManager will hunt for a JSON configuration file that matches the
resource the web application requests and then create the object to return from
the JSON file. That needs a standard JSON file format.

### Common JSON Elements to JavaScript and CSS

* `name` String, required:  
  The name of the script or css, sans version and variant, e.g. `jquery`.
* `homepage` String, not strictly required:  
  The URL homepage of the project
* `version` String, required:  
  The version of the script being described.
* `license` Array, required:  
  An array containing one or more applicable license for the script. Each
  element of the array should contain license name and a URL for the
  license.

Those fields are not utilized by the classes but are metadata that is useful to
a system administrator. Other fields common to both JS and CSS:

* `mime` String, required:  
  The MIME type that should be used when serving the file.
* `checksum` String, recommended:  
  The `algo:checksum` described in the `FileResource` abstract class.
* `filepath` String, optional:  
  If the file is present on the server, the file system path to the file.
* `lastmod` String, recommended:  
  A string that can be parsed by `strtotime()` to create a UNIX time stamp
  indicating when the file was last modified. For many projects, this is
  specified in a comment header of the file itself, and in those cases, that
  string should be used.
* `srcurl` String, required:  
  What should go in the `src` or `href` attribute. Must be parseable by the
  `parse_url` function and internationalized domains should be in punycode.
* `minified` Boolean, optional:  
  Use of this field may be used by some implementations but is not required. It
  defines whether the JS/CSS in the file is minified.

### JavaScript JSON

A sample of what a JavaScript JSON might look like:

    {
        "name": "jquery",
        "homepage": "https://jquery.com/",
        "version": "3.3.1",
        "license": [
            {
                "name": "MIT",
                "url": "https://jquery.org/license/"
            }
        ],
        "mime": "application/javascript",
        "checksum": "sha256:160a426ff2894252cd7cebbdd6d6b7da8fcd319c65b70468f10b6690c45d02ef",
        "filepath": "flossjs/jquery/js/jquery-3.3.1.min.js",
        "lastmod": "2018-01-20T17:24Z",
        "minified": true,
        "srcurl": "/js/jquery-3.3.1.min.js"
    }

JavaScript Specific Fields:

* `async` Boolean, optional:  
  Only needed if it is desired to have that attribute, then set to `true`.
* `defer` Boolean, optional:  
  Only needed if it is desired to have that attribute, then set to `true`.
* `nomodule` Boolean, optional:  
  Only needed if it is desired to have that attribute, then set to `true`.
* ??`type`?? Needs Exploration:  
  In most cases, the `type` attribute is set to the MIME type, but it may be
  necessary to set it to `modular` for ES6 modular feature, I still need to
  learn about that.

### CSS JSON

A sample of what a CSS JSON might look like needs to be written.

CSS Specific Fields:

* `media` Array, optional:  
  An array containing the media tyes the CSS file applies to when it does not
  apply to everyone (browsers assume `all` when not specified)
* `hreflang` String, optional:  
  The BCP47 language string that applies to the CSS file.
* `referrerpolicy` String, optional:  
  When present, must be one of `no-referrer`, `no-referrer-when-downgrade`,
  `origin`, `origin-when-cross-origin`, `unsafe-url` -- browsers assume
  `no-referrer-when-downgrade` which is almost always the best policy for
  CSS style sheets. It is my opinion that `unsafe-url` should *never* be
  used as it can leak information. It only has meaning when the remote server
  does not use TLS but your web application uses TLS and that scenario *should*
  be blocked by browsers anyway.


File System and Config File Naming
----------------------------------

All scripts should be installed with a hierarchy of
`$base/VendorName/ProductName` where `VendorName` and `ProductName` are lower
case, as is Composer convention for PHP libraries.

In the case of Composer install of JS/CSS libraries, the Composer `vendor`
directory would be the `$base` directory.

Within the `ProductName` directory, an `etc` directory __MUST__ exist that has
the JSON configuration files, and it is *RECOMMENDED* that the actual
JavaScript files reside in a `js` directory and CSS files reside in a `css`
directory.

An example of what this would look like is at
[AliceWonderMiscreations/CommonJS](https://github.com/AliceWonderMiscreations/CommonJS)

Default configuration files would be named using:

    ScriptName-Version-Variant.json.dist

Where ScriptName is the name of the script (e.g. `jquery`), Version is the
version of the script (e.g. `3.3.1`), and if present, Variant would be the
script variant (e.g. `min` or `slim` or `slim.min`).

The default configuration should have the `srcurl` point to a local URL, e.g.

    "srcurl": "/js/jquery-3.3.1.min.js",

When a system administrator wants to customize what is in the configuration,
they simply copy the file so that it no longer ends in `.dist` and then they
can modify it (e.g. to set `srcurl` to a CDN).

The ResourceManager __MUST__ give priority to the configuration file without
the `.dist` if it is found.

With the `major.minor.point` versioning scheme, a configuration file should
exist for both `major.minor` and `major` that are identical to the default
configuration file for the most recent `major.minor.point` that match.


Wrapper Script
--------------

Web Applications that implement this __MUST__ be able to handle requests to the
default `/js/` and `/css/` locations.

This can be accomplished by a wrapper script. An abstract class that extends
the [FileWrapper](https://github.com/AliceWonderMiscreations/FileWrapper) class
to work for this has been written, that abstract class is part of the
[`AWonderPHP\FileResource`](https://github.com/AliceWonderMiscreations/FileResource)
namespace.

An interface exists in this namespace that can be used to define a class that
extends the class that serves `FileResource` objects described above.

-----------------------------------
__EOF__