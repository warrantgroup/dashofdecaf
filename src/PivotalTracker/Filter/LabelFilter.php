<?php

namespace PivotalTracker\Filter;

use PivotalTracker\Filter\FilterInterface;

class LabelFilter implements FilterInterface
{
    public function filter($value)
    {
        if ($value == 'all'){
            return null;
        }
        return array('label' => $value);
    }
}