# Changelog

## 3.0.0

- drop support for <5.6
- update dependencies
- misc refactoring

## 2.2.3

- cart can be restored from an empty state without an exception being thrown
- use default bin location
- update dependencies

## 2.2.2

- modernize project

## 2.2.1

- misc refactoring

## 2.2.0

- add `Cart\Store\CookieStore`
- rename `Cart\SessionStore` to `Cart\Storage\SessionStore`
- rename `Cart\StorageInterface` to `Cart\Storage\Store`
- rename `Cart\ArrayableInterface` to `Cart\Arrayable`
- add scrutinizer config
- fix scrutinizer recommendations
- misc comments / code cleanup

## 2.1.0

- rename `Cart\Store\Native\SessionStore` to `Cart\SessionStore`
- add more error checking to `Cart::restore`. `Cart\CartRestoreException` is thrown if the cart state cannot be restored due to:
    - unserializable data
    - invalid unserialized data (not an array)
    - missing id
    - missing items
    - invalid id (not a string)
    - invalid items (not an array)
- add `Cart\CartItem::getSingleTax`
- `Cart\CartItem::getSinglePrice` and `Cart\CartItem::getTotalPrice` always return price including tax
- add `Cart\CartItem::getSinglePriceExlcudingTax` and `Cart\CartItem::getTotalPriceExcludingTax`
- `Cart\Cart::total` always return total including tax
- add `Cart\Cart::totalExcludingTax`

## 2.0.0

- rewrite

## 1.1.0

- remove `Cart\Proxy`
- remove all static methods from `Cart\Manager`
- add `Cart\Facade\Cart` and `Cart\Facade\Manager` (these replace `Cart\Proxy` and static methods in `Cart\Manager`)
- remove `Cart\Storage\Session`
- refactor storage implementations - No restrictions on name our placement of storage driver
- misc refactoring + cleaning up

## 1.0

First release
