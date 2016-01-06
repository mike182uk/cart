<?php

namespace Cart\Catalog;

class ProductSsl extends Product
{
    protected $unit_quantities = array(1, 2, 3, 5, 10);

    protected $unit = TermLexer::UNIT_YEAR;

    protected $group = Group::SECURITY;
}
