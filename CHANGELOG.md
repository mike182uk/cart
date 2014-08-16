#Changelog

##2.2.1

Miscellaneous refactorings

##2.2.0

- added `Cart\Store\CookieStore`
- renamed `Cart\SessionStore` to `Cart\Storage\SessionStore`
- renamed `Cart\StorageInterface` to `Cart\Storage\Store`
- renamed `Cart\ArrayableInterface` to `Cart\Arrayable`
- added scrutinizer config
- fixed scrutinizer recommendations
- misc comments / code cleanup

##2.1.0

- Renamed `Cart\Store\Native\SessionStore` to `Cart\SessionStore`
- Added more error checking to `Cart::restore`. `Cart\CartRestoreException` is thrown if the cart state cannot be restored due to:
    - unserializable data
    - invalid unserialized data (not an array)
    - missing id
    - missing items
    - invalid id (not a string)
    - invalid items (not an array)
- added `Cart\CartItem::getSingleTax`
- made `Cart\CartItem::getSinglePrice` and `Cart\CartItem::getTotalPrice` always return price including tax
- added `Cart\CartItem::getSinglePriceExlcudingTax` and `Cart\CartItem::getTotalPriceExcludingTax`
- made `Cart\Cart::total` always return total including tax
- added `Cart\Cart::totalExcludingTax`

##2.0.0

- Rewrite from the ground up

##1.1.0

- Removed `Cart\Proxy`
- Remove all static methods from `Cart\Manager`
- Added `Cart\Facade\Cart` and `Cart\Facade\Manager` (these replace `Cart\Proxy` and static methods in `Cart\Manager`)
- Removed `Cart\Storage\Session`
- Refactored how storage implementations work - No restrictions on name our placement of storage driver
- Misc refactoring + cleaning up

##1.0

First release
