<?php

namespace Cart\Catalog;

interface TaxIcannInterface
{
    const ICANN_FEE = 0.18;

    public function getIcannFee(Term $term);
}
