<?php

namespace PivotalTracker\Filter;

use PivotalTracker\Filter\FilterInterface;

class SearchFilter implements FilterInterface
{
    public function filter($value)
    {
        return array('name' => $value);
    }
}