<?php

namespace PivotalTracker\Filter;

use PivotalTracker\Filter\FilterInterface;

class DateRangeFilter implements FilterInterface
{
    protected $type;
    protected $validTypes = array('created', 'accepted', 'updated');

    public function filter($value)
    {

        if(empty($this->type)) {
            throw new \InvalidArgumentException(sprintf('A date range type must be specified. Expected values [%s]', implode(', ', $this->validTypes)));
        }

        if(is_string($value)) {
            switch ($value) {
                case 'week':
                    $range[0] = new \DateTime('Monday this week');
                    $range[1] = new \DateTime('Sunday this week');
                    break;

                case 'month':
                    $range[0] = new \DateTime('first day of this month');
                    $range[1] = new \DateTime('last day of this month');
                    break;

                case 'year':
                    $range[0] = new \DateTime('first day of January this year');
                    $range[1] = new \DateTime('last day of December this year');
                    break;

                case 'fortnight':
                    $range[0] = new \DateTime('Monday last week');
                    $range[1] = new \DateTime('Sunday this week');
                    break;
            }
        }

        return array($this->type => $range[0]->format('m/d/Y') . '..' . $range[1]->format('m/d/Y'));
    }

    /**
     * Set date range type
     *
     * @param $type
     * @throws \InvalidArgumentException
     */
    public function setType($type) {

        if(!in_array($type, $this->validTypes)) {
            throw new \InvalidArgumentException(sprintf('Date range type invalid. Expected values [%s]', implode(', ', $this->validTypes)));
        }

        $this->type = $type;

        return $this;
    }
}