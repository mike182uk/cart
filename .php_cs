<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
  ->in(__DIR__ . '/src')
  ->in(__DIR__ . '/tests')
;

return Symfony\CS\Config\Config::create()
  ->finder($finder)
;
