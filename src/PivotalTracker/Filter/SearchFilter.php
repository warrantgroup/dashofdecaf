<?php

namespace PivotalTracker\Filter;

use PivotalTracker\Filter\FilterInterface;

class SearchFilter implements FilterInterface
{
    public function create($value)
    {
        return array('name' => $value);
    }
}