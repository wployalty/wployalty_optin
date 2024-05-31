<?php

namespace Wlopt\App\Controller;

use Wlr\App\Helpers\Input;
use Wlr\App\Helpers\Woocommerce;

class Base
{
    public static $input,$woocommerce;

    public function __construct()
    {
        self::$input = empty(self::$input) ? new Input() : self::$input;
        self::$woocommerce = empty(self::$woocommerce) ? Woocommerce::getInstance() : self::$woocommerce;
    }

}