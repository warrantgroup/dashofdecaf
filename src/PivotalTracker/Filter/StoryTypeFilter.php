<?php

namespace PivotalTracker\Filter;

use PivotalTracker\Filter\FilterInterface;

class StoryTypeFilter implements FilterInterface
{
    public function filter($value)
    {
        return array('type' => array_filter($value, array($this, 'filterType')));
    }

    /**
     * Filter types to expected values
     *
     * @param $value
     * @return bool
     */
    protected function filterType($value)
    {
        return in_array($value, array('feature', 'bug', 'chore', 'release'));
    }
}