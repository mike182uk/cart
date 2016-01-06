<?php

namespace Cart\Catalog;

class ProductDomain extends Product implements TaxIcannInterface
{
    protected $unit_quantities = array(1, 2, 3, 5, 10);

    protected $unit = TermLexer::UNIT_YEAR;

    public function getIcannFee(Term $term)
    {
        return TaxIcannInterface::ICANN_FEE * $term->period;
    }
}
