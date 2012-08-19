<?php
/*
 * Bootstrap the cart
 *
 * Set a few configuration options, include the necessary files and import / alias the cart namespace
 */

//error reporting - so we can see if we f*ck up
ini_set('display_errors',1);
error_reporting(E_ALL);

//we will use sessions to preserve cart context, and to show action messages
@session_start();

//include required classes
include '../src/Cart/Storage/Interface.php';
include '../src/Cart/Storage/Session.php';
include '../src/Cart/Manager.php';
include '../src/Cart/Proxy.php';
include '../src/Cart/Item.php';
include '../src/Cart/Cart.php';

//import namespaces  / set aliases
use \Cart\Manager;
use \Cart\Proxy as Cart;

//-------------------------------------------------------------------------------------------------------------

/**
 * Initialize the cart manager
 *
 * The first thing to do is load in the config file. This will supply config options for the cart instances
 * in the cart manager. It also initialize any preset carts (including retrieving state)
 */
$config = include 'config/default.php';

Manager::init($config);

//set context from session if applicable
if (isset($_SESSION['cart_context'])) {
    Manager::context($_SESSION['cart_context']);
}
//-------------------------------------------------------------------------------------------------------------

/**
 * Cart Actions
 *
 * When the page is posted back to itself, we check to see if there is a query string parameter called action.
 * If so then its most likely to perform an action on the cart. Once the action has been performed a friendly
 * message variable will be set and we will be redirected back to the page (to get rid of the ugly query string
 * parameter)
 */
if (isset($_GET['action'])) {

    switch ($_GET['action']) {
        case 'add':
            $item_data = $_POST['product'];
            Cart::add($item_data);
            $msg = $item_data['quantity'] . ' x ' . $item_data['name'] . (($item_data['quantity'] > 1) ? ' have' : ' has') . ' been added to the cart.';
        break;
        case 'remove':
            $msg = Cart::item($_GET['item'])->get_name() . ' has been removed from the cart';
            Cart::remove($_GET['item']);
        break;
        case 'update_quantity':
            $new_quantity = $_POST['quantity'];
            $item = $_GET['item'];
            $msg = Cart::item($_GET['item'])->get_name() . ' quantity has been updated.';
            Cart::update($item,'quantity',$new_quantity); //or Cart::item($item)->set_quantity($new_quantity);
        break;
        case 'update_engraving':
            $new_engraving_text = $_POST['engraving_text'];
            $item = $_GET['item'];
            Cart::item($item)->set_meta('engraving_text', $new_engraving_text);
            $msg = Cart::item($_GET['item'])->get_name() . ' engraving text has been updated.';
        break;
        case 'remove_engraving':
            $item = $_GET['item'];
            Cart::item($item)->set_meta('has_engraving', false);
            $msg = Cart::item($_GET['item'])->get_name() . ' engraving has been removed.';
        break;
        case 'add_engraving':
            $item = $_GET['item'];
            Cart::item($item)->set_meta('has_engraving', true);
            $msg = Cart::item($_GET['item'])->get_name() . ' engraving has been added. Use the text box to update.';
        break;
        case 'clear':
            $msg = Manager::context() . ' has been cleared.';
            Manager::destroy_instance();
        break;
        case 'switch_cart':
            $_SESSION['cart_context'] = $_GET['cart'];
            $msg = 'Switched to ' . $_GET['cart'] . '.';
        break;
        case 'clear_all_carts':
            Manager::destroy_all_instances();
            $msg = 'All carts have been cleared.';
        break;
        case 'update_merchant_notes':
            $notes = $_POST['merchant_notes'];
            Cart::set_meta('merchant_notes', $notes);
            $msg = 'Note to merchant has been updated';
        break;
        case 'clear_notes':
            Cart::remove_meta('merchant_notes');
            $msg = 'Note to merchant has been cleared';
        break;
    }

    $_SESSION['action_msg'] = $msg;
    header('location: index.php');
    exit;
}