<?php

namespace PivotalTracker\FilterClasses;

class FilterFactory
{

    public function loadClass($filterType) {
        switch($filterType) {
            case 'search' :
                return new SearchFilter();
                break;
            case 'storyStatus' :
                return new StoryStatusFilter();
                break;
            case 'storyType' :
                return new StoryTypeFilter();
                break;
            default :
                throw new \InvalidArgumentException('Invalid Map Type');
                break;
        }
    }
}
