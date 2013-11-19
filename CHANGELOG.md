#Changelog

##2.1.0

- Renamed ``Cart\Store\Native\SessionStore`` to ``Cart\SessionStore``

##2.0.0

- Rewrite from the ground up

##1.1.0

- Removed ``Cart\Proxy``
- Remove all static methods from ``Cart\Manager``
- Added ``Cart\Facade\Cart`` and ``Cart\Facade\Manager`` (these replace ``Cart\Proxy`` and static methods in ``Cart\Manager``)
- Removed ``Cart\Storage\Session``
- Refactored how storage implementations work - No restrictions on name our placement of storage driver
- Misc refactoring + cleaning up

##1.0

First release
