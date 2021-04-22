<?php
require_once 'Clock.php';

class ManualClock implements Clock
{
    private $now;

    /**
     * ManualClock constructor.
     * @param $now
     */
    public function __construct($now)
    {
        $this->now = $now;
    }

    public function now()
    {
        return $this->now;
    }

    public function advance(DateInterval $anInterval)
    {
        $this->now = clone $this->now;
        $this->now->add($anInterval);
    }
}