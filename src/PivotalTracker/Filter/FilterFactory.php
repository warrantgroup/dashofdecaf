<?php

namespace PivotalTracker\Filter;

class FilterFactory
{
    public function create($filterType) {
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
            case 'acceptedDateRange' :
                $filter = new DateRangeFilter();
                return $filter->setType('accepted');
            break;
            case 'createdDateRange' :
                $filter = new DateRangeFilter();
                return $filter->setType('created');
                break;
            case 'label' :
                return new LabelFilter();
                break;
            case 'includeDone' :
                return new IncludeDoneFilter();
                break;
            default :
                throw new \InvalidArgumentException('Invalid filter type');
                break;
        }
    }
}
