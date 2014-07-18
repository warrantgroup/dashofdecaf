<?php

namespace PivotalTracker\Filter;

class FilterFactory
{
    public function create($filterType) {
        switch($filterType) {
            case 'search' :
                return new SearchFilter();
                break;
            case 'storyState' :
                return new StoryStatusFilter();
                break;
            case 'storyType' :
                return new StoryTypeFilter();
                break;
            case 'label' :
                return new LabelFilter();
                break;
            default :
                throw new \InvalidArgumentException('Invalid Map Type');
                break;
        }
    }
}
