<?php

namespace PivotalTracker\Filter;

use PivotalTracker\Filter\FilterInterface;

class LabelFilter implements FilterInterface
{
    public function filter($value)
    {
        if(is_array($value)) {
            $value = implode(',', $value);
        }
        
        return array('label' => $value);
    }
}