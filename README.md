#### Create a new Cart Item

```
$item = new CartItem;

$item->name = 'Macbook Pro';
$item->sku = 'MBP8GB';
$item->price = 1200;
$item->tax = 200;
$item->options = array(
	'ram' => '8 GB',
	'ssd' => '256 GB'
);
```

`Cart\CartItem` implements `ArrayAccess` so properties can be assigned to the cart item as if accessing an array:

```
$item = new CartItem;

$item['name'] = 'Macbook Pro';
$item['sku'] = 'MBP8GB';
$item['price'] = 1200;
$item['tax'] = 200;
$item['options'] = array(
	'ram' => '8 GB',
	'ssd' => '256 GB'
);
```

An array of data can also be passed to the cart item constructor to set the cart item properties:

```
$item = new CartItem(array(
	'name' => 'Macbook Pro';
	'sku' => 'MBP8GB';
	'price' => 1200;
	'tax' => 200;
	'options' => array(
		'ram' => '8 GB',
		'ssd' => '256 GB'
	)
));
```

#### Cart Item ID

Each cart item will have a unique ID. This ID is generated using the properties set on the cart item. You can get the cart item ID using the method `getId` or by accessing the property `id`.

```
$id = $item->getId();

// or

$id = $item->id;

// or 

$id = $item['id'];
```
Changing a property on the cart item will change its unique id.

