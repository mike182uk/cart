<?php

namespace Cart\Catalog;

class ProductVps extends Product
{
    protected $unit_quantities = array(1, 3, 6, 12, 24, 36, 48, 60);

    protected $unit = TermLexer::UNIT_MONTH;

    protected $group = Group::HOSTING;
}
