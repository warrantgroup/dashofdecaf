<?php

namespace PivotalTracker\FilterClasses;

use PivotalTracker\FilterClasses\FilterInterface;

class SearchFilter implements FilterInterface
{
    public function create($value)
    {
        return array('name' => $value);
    }
}