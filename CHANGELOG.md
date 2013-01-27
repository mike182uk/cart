#Changelog

##1.1.0

1. Removed ``Cart\Proxy``
2. Remove all static methods from ``Cart\Manager``
3. Added ``Cart\Facade\Cart`` and ``Cart\Facade\Manager`` (these replace ``Cart\Proxy`` and ``Cart\Manager``)
4. Removed ``Cart\Storage\Session``
5. Refactored how storage implementations work - No restrictions on name our placement of storage driver
6. Misc refactoring + cleaning up

##1.0

First realease
