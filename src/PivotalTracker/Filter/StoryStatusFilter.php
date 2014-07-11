<?php

namespace PivotalTracker\Filter;

use PivotalTracker\Filter\FilterInterface;

class StoryStatusFilter implements FilterInterface
{
    public function create($value)
    {
        $states = array();
        if (in_array('workInProgress', $value)){
            $states[] = 'delivered';
            $states[] = 'rejected';
            $states[] = 'started';
        }

        if (in_array('finished', $value)){
            $states[] = 'accepted';
            $states[] = 'finished';
        }

        if (in_array('scheduled', $value)){
            $states[] = 'planned';
            $states[] = 'unstarted';
        }

        if (in_array('icebox', $value)){
            $states[] = 'unscheduled';
        }

        return array('state' => $states);
    }
}