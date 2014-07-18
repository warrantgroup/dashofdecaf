<?php

namespace PivotalTracker\Filter;

use PivotalTracker\Filter\FilterInterface;

class LabelFilter implements FilterInterface
{
    public function filter($value)
    {
        return array('label' => $value);
    }
}