<?php

namespace PivotalTracker\FilterClasses;

use PivotalTracker\FilterClasses\FilterInterface;

class StoryStatusFilter implements FilterInterface
{
    public function create($value, $filterString)
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

        return $filterString . 'filter=state:' . implode(',', $states);
    }
}