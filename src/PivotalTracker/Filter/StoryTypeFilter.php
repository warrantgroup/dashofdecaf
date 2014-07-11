<?php

namespace PivotalTracker\Filter;

use PivotalTracker\Filter\FilterInterface;

class StoryTypeFilter implements FilterInterface
{
    public function create($value)
    {
        $types = array();
        if (in_array('features', $value)){
            $types[] = 'feature';
        }

        if (in_array('bugs', $value)){
            $types[] = 'bug';
        }

        if (in_array('chores', $value)){
            $types[] = 'chore';
        }

        if (in_array('releases', $value)){
            $types[] = 'release';
        }

        return array('type' => $types);
    }
}