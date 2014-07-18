<?php

namespace PivotalTracker\Filter;

use PivotalTracker\Filter\FilterInterface;

class IncludeDoneFilter implements FilterInterface
{

    public function filter($value) {

        return array('includedone' => $value);
    }
}