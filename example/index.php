<?php
/**
 * Import namespaces and alias cart proxy to cart so can do sexy calls like Cart::items()
 */
use \Cart\Manager;
use \Cart\Proxy as Cart;

/**
 * Bootstrap the cart...
 */
include 'bootstrap.php';

//-------------------------------------------------------------------------------------------------------------
?>
<!doctype html>
<html>
    <head>
        <title>Shopping Cart Demo</title>
        <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
        <link rel="stylesheet" href="assets/css/example.css" />
    </head>
    <body>
        <div class="navbar navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container">
                    <a class="brand" href="index.php">Shopping Cart Demo</a>
                        <div class="nav-collapse">
                            <ul class="nav">
                                <li class="dropdown">
                                    <a href="" class="dropdown-toggle" data-toggle="dropdown">Global Cart Actions <b class="caret"></b></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="?action=clear">Clear Cart</a></li>
                                        <li><a href="?action=clear_all_carts">Clear All Carts</a></li>
                                    </ul>
                                </li>
                                <li class="dropdown">
                                    <a href="" class="dropdown-toggle" data-toggle="dropdown">Switch Cart <b class="caret"></b></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="?action=switch_cart&cart=Cart-01">Cart 1</a></li>
                                        <li><a href="?action=switch_cart&cart=Cart-02">Cart 2</a></li>
                                    </ul>
                                </li>
                            </ul>
                            <p class="navbar-text pull-right">You are currently viewing cart: <?php echo Manager::context(); ?> - <?php echo Cart::itemCount(); ?> Item(s)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container" id="content">

            <?php if (isset($_SESSION['action_msg'])) : //show action message ?>
            <p class="alert alert-success"><strong>Action Complete!</strong> <?php echo $_SESSION['action_msg'] ?></p>
            <?php
            unset($_SESSION['action_msg']); //only show message once
            endif;
            ?>

            <h2>Shopping Cart</h2>
            <div id="cart">
                <?php if (Cart::itemCount() > 0) : ?>
                <table class="table" id="cart-table">
                    <thead>
                        <th scope="col">Product</th>
                        <th scope="col" class="options">Options</th>
                        <th scope="col" class="quantity">Quantity</th>
                        <th scope="col" class="price">Price (ex. Tax)</th>
                        <th scope="col" class="tax">Tax</th>
                        <th scope="col" class="total">Total</th>
                        <th scope="col" class="remove"></th>
                    </thead>
                    <tbody>
                        <?php foreach (Cart::items() as $item) : ?>
                        <tr>
                            <td><?php echo $item->get_name(); ?></td>
                            <td class="options">
                                <?php
                                if ($item->get_options()) {
                                    foreach ($item->get_options() as $opt => $val) {
                                        echo "<strong>" . ucwords($opt) . ":</strong> " . ucwords($val) . "<br />";
                                    }
                                }
                                else {
                                    echo " - ";
                                }
                                ?>
                            </td>
                            <td class="quantity">
                                <form action="?action=update_quantity&item=<?php echo $item->uid(); ?>" method="post">
                                    <input type="text" name="quantity" value="<?php echo $item->get_quantity(); ?>" class="span1" />
                                    <input type="submit" value="Update" class="btn" />
                                </form>
                            </td>
                            <td class="price">&pound;<?php echo $item->singlePrice(true); ?></td>
                            <td class="tax">&pound;<?php echo $item->singleTax(); ?></td>
                            <td class="total"><strong>&pound;<?php echo $item->totalPrice(); ?></strong></td>
                            <td>
                                <a href="?action=remove&item=<?php  echo $item->uid(); ?>" class="btn">x</a>
                            </td>
                        </tr>
                        <?php if ($item->hasMeta('has_engraving')) : ?>
                        <tr>
                            <td colspan="7" class="engraving-text-row">
                                <?php if ($item->getMeta('has_engraving')) : ?>
                                <label>Engraving Text For <strong><?php echo $item->get_name(); ?></strong></label>
                                <form action="?action=update_engraving&item=<?php echo $item->uid() ?>" method="post">
                                    <input type="text" name="engraving_text" value="<?php echo $item->getMeta('engraving_text') ?>" class="span8">
                                    <input type="submit" value="Update Engraving Text" class="btn" />
                                    <br />
                                    <a href="?action=remove_engraving&item=<?php echo $item->uid() ?>">Remove Engraving</a>
                                </form>
                                <?php else : ?>
                                    <a href="?action=add_engraving&item=<?php echo $item->uid() ?>" class="btn">Add Engraving For <strong><?php echo $item->get_name(); ?></strong></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4"></td>
                            <td><strong>Total Weight:</strong></td>
                            <?php
                            //if over 1000g round up to nearest KG
                            $weight = Cart::cumulative_weight();
                            if ($weight  < 1000) {
                                $weight_str = $weight . 'g';
                            }
                            else {
                                $weight_str = round($weight / 1000) . 'kg';
                            }
                            ?>
                            <td><?php echo $weight_str; ?></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="4"></td>
                            <td><strong>Tax:</strong></td>
                            <td>&pound;<?php echo Cart::totalTax(); ?></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="4"></td>
                            <td><strong>Total:</strong></td>
                            <td>&pound;<?php echo Cart::total(); ?></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
                <div>
                    <form action="?action=update_merchant_notes" method="post">
                        <label>Leave any notes for the merchant to read here. <a href="?action=clear_notes">Clear Notes</a> </label>
                        <textarea class="span6" rows="5" name="merchant_notes"><?php echo Cart::getMeta('merchant_notes') ?></textarea>
                        <input type="submit" class="btn" value="Update Notes" />

                    </form>
                </div>
                <?php else : ?>
                <p class="alert alert-warning">Your shopping cart is currently empty. <strong>Add some products below!</strong></p>
                <?php endif; ?>
            </div>

            <h2>Products</h2>
            <div id="products" class="row">
                <div class="product span4">
                    <form action="?action=add" method="post">
                        <img src="assets/img/ipad.jpg" />
                        <h3>Apple Ipad 2 WiFi <br /><span class="price">&pound;420.00</span></h3>
                        <input type="hidden" name="product[name]" value="Apple Ipad 2 WiFi" />
                        <input type="hidden" name="product[SKU]" value="B0052J441U" />
                        <input type="hidden" name="product[id]" value="1" />
                        <input type="hidden" name="product[price]" value="350.00" />
                        <input type="hidden" name="product[tax]" value="70.00" />
                        <input type="hidden" name="product[weight]" value="603" />
                        <input type="hidden" name="product[meta][has_engraving]" value="0" />

                        <div>
                            <div>
                                <label>Colour</label>
                                <select name="product[options][colour]">
                                    <option value="white">White</option>
                                    <option value="black">Black</option>
                                </select>
                            </div>
                            <div>
                                <label>Quantity</label>
                                <select name="product[quantity]">
                                    <?php for ($i = 1; $i<=10; $i++) : ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div>
                                <input type="submit" value="Add To Cart" class="btn"  />
                            </div>
                        </div>

                    </form>
                </div>
                <div class="product span4">
                    <form action="?action=add" method="post">
                        <img src="assets/img/macbookpro.jpg" />
                        <h3>Apple Macbook Pro 13 inch Laptop <br /><span class="price">&pound;989.00</span></h3>
                        <input type="hidden" name="product[name]" value="Apple Macbook Pro 13 inch Laptop" />
                        <input type="hidden" name="product[SKU]" value="B004P8JCY8" />
                        <input type="hidden" name="product[id]" value="2" />
                        <input type="hidden" name="product[price]" value="824.17" />
                        <input type="hidden" name="product[tax]" value="164.83" />
                        <input type="hidden" name="product[weight]" value="3900" />

                        <div>
                            <div>
                                <label>Quantity</label>
                                <select name="product[quantity]">
                                    <?php for ($i = 1; $i<=10; $i++) : ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div>
                                <input type="submit" value="Add To Cart" class="btn"  />
                            </div>
                        </div>

                    </form>
                </div>
                <div class="product span4">
                    <form action="?action=add" method="post">
                        <img src="assets/img/gelamacbookskin.jpg" />
                        <h3>GelaSkins Laptop & MacBook Pro / Air Cover Skin <br /><span class="price">&pound;14.99</span></h3>
                        <input type="hidden" name="product[name]" value="GelaSkins Laptop & MacBook Pro / Air Cover Skin" />
                        <input type="hidden" name="product[SKU]" value="B006L9KGB2" />
                        <input type="hidden" name="product[id]" value="3" />
                        <input type="hidden" name="product[price]" value="12.49" />
                        <input type="hidden" name="product[tax]" value="2.50" />
                        <input type="hidden" name="product[weight]" value="82" />

                        <div>
                            <div>
                                <label>Pattern</label>
                                <select name="product[options][pattern]">
                                    <option value="floral">Floral</option>
                                    <option value="ocean">Ocean</option>
                                    <option value="jungle">Jungle</option>
                                    <option value="space">Space</option>
                                </select>
                            </div>
                            <div>
                                <label>Size</label>
                                <select name="product[options][size]">
                                    <option value="11 inch">11"</option>
                                    <option value="13 inch">13"</option>
                                    <option value="15 inch">15"</option>
                                    <option value="17 inch">17"</option>
                                </select>
                            </div>
                            <div>
                                <label>Quantity</label>
                                <select name="product[quantity]">
                                    <?php for ($i = 1; $i<=10; $i++) : ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div>
                                <input type="submit" value="Add To Cart" class="btn"  />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script src="assets/js/jquery-1.7.1.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
    </body>
</html>
