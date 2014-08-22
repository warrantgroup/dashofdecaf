<?php

namespace PivotalTracker\Filter;

use PivotalTracker\Filter\FilterInterface;

class LabelFilter implements FilterInterface
{
    public function filter($value)
    {
        if(is_array($value)) {
            $labels = array();

            foreach($value as $v) {
                $labels[] = 'label:'.$v;
            }


            return array('(' . implode(' OR ', $labels) . ')');
        }

        return array('label' => $value);
    }
}