<?php
/**
 * @author      Wployalty (Ilaiyaraja)
 * @license     http://www.gnu.org/licenses/gpl-2.0.html
 * @link        https://www.wployalty.net
 * */

namespace Wlopt\App\Controller;

use Wlr\App\Helpers\Input;
use Wlr\App\Helpers\Woocommerce;

defined( 'ABSPATH' ) or die();

class Base {
	public static $input, $woocommerce;

	public function __construct() {
		self::$input       = empty( self::$input ) ? new Input() : self::$input;
		self::$woocommerce = empty( self::$woocommerce ) ? Woocommerce::getInstance() : self::$woocommerce;
	}

}