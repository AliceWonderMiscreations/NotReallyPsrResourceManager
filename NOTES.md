NOTES
=====

Resource URL Strictness
-----------------------

In the [FileResource](lib/FileResource.php) class, the `getSrcAttribute()`
function has some restrictions.

The function is intended for cases where the resource is being embedded in the
web page, such as JavaScript and CSS. It is not intended for hyperlink
resources.

To that end, it refuses to create output unless the scheme is either `null`
indicating the resource is on the same server as the web application, or it is
using either `http` or `https` to reference the file. Other protocols are not
allowed, there is not a valid reason for a web application to ever embed a
resource using a different protocol.

Alternative ports are not supported, the default port for the protocol should
be used. This *should* be *mandatory* with `https`, it is my *strong opinion*
that there is never a legitimate reason to run `https` on any port other than
443. With `http` it use to be fairly common to see it using `8080`. This was in
the days when users would run their own daemon in their user account and could
not bind to ports below 1024. This is no longer needed, virtual machines are
inexpensive and are easier to manage now than a personal server daemon using a
high port was in the old days. Third party resources should not be hosted on
servers using non-standard ports.

When `http` is used, the function __REQUIRES__ that the hash property exists
and that the hash uses one of the algorithms supported by the `integrity`
attribute.

This is to ensure that the `integrity` attribute will both exist *and* use one
of the algorithms every browser that supports that attribute supports.

Note that when creating the `integrity` attribute, it does not matter which
algorithm is used, users are free to experiment with different algorithms that
do not have full browser support. However when referencing a third party server
via `http` instead of `https` I feel it is important to require one of the
algorithms defined in the
[Subresource Integrity Recommendation](https://www.w3.org/TR/SRI/) (see Section
3.2).

In short, use `https` if you want to experiment with different hash algorithms
for the `integrity` attribute.