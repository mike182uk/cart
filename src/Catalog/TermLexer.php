<?php

namespace Cart\Catalog;

class TermLexer
{
    const UNIT_DAY = 'D';
    const UNIT_WEEK = 'W';
    const UNIT_MONTH = 'M';
    const UNIT_YEAR = 'Y';

    protected $unit;

    protected $qty;

    protected $_multiplier = array(
        self::UNIT_DAY => 1,
        self::UNIT_WEEK => 7,
        self::UNIT_MONTH => 30,
        self::UNIT_YEAR => 360,
    );

    public function __construct($code)
    {
        preg_match('/(\d+)([A-Z])/', strtoupper($code), $match);
        if (!isset($match[1]) || !isset($match[2])) {
            throw new \Exception("Invalid period code");
        }

        $qty = $match[1];
        $unit = $match[2];

        $units = $this->getUnits();
        $qty = (int)$qty;
        if (!array_key_exists($unit, $units)) {
            throw new \Exception("Period Error. Unit is not defined");
        }

        if ($qty < $units[$unit][0] || $qty > $units[$unit][1]) {
            $msg = "Invalid period quantity $qty for unit $unit. Allowed range is from {$units[$unit][0]} to {$units[$unit][1]}";
            throw new \Exception($msg);
        }

        $this->unit = $unit;
        $this->qty = $qty;
    }

    private function getUnits()
    {
        return array(
            self::UNIT_DAY => array(1, 720),
            self::UNIT_WEEK => array(1, 52),
            self::UNIT_MONTH => array(1, 240),
            self::UNIT_YEAR => array(1, 10),
        );
    }

    public function getUnit()
    {
        return $this->unit;
    }

    public function getQty()
    {
        return $this->qty;
    }

    public function getCode()
    {
        return $this->qty . $this->unit;
    }

    public function getTitle()
    {
        switch ($this->unit) {
            case self::UNIT_DAY:
                $shift = $this->qty . ' day(s)';
                break;
            case self::UNIT_WEEK:
                $shift = $this->qty . ' week(s)';
                break;
            case self::UNIT_MONTH:
                $shift = $this->qty . ' month(s)';
                break;
            case self::UNIT_YEAR:
                $shift = $this->qty . ' year(s)';
                break;
            default:
                $shift = 'n/a';
        }

        return $shift;
    }

    public function getDays()
    {
        return $this->_multiplier[$this->unit];
    }

    public function getExpirationTime($now = null)
    {
        if (null === $now) {
            $now = time();
        }

        switch ($this->unit) {
            case self::UNIT_DAY:
                $shift = 'days';
                break;
            case self::UNIT_WEEK:
                $shift = 'weeks';
                break;
            case self::UNIT_MONTH:
                $shift = 'months';
                break;
            case self::UNIT_YEAR:
                $shift = 'years';
                break;
            default:
                throw new \Exception('Unit not defined');
        }
        return strtotime("+$this->qty $shift", $now);
    }

    public function __toString()
    {
        return $this->getTitle();
    }
}
