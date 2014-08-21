<?

/**
 * Pivotal Tracker Story
 */

namespace PivotalTracker;
use \Guzzle\Http\Exception\ClientErrorResponseException;
use PivotalTracker\Filter\FilterInterface;
use PivotalTracker\StoryCollection, PivotalTracker\Filter\FilterFactory;

class Story {

    protected $api;
    protected $filters = array();
    protected $limit = 30;

    public function __construct($api)
    {
        $this->api = $api;
        $this->url = sprintf('https://www.pivotaltracker.com/services/v5/projects/%s/search', $api->getProjectId());
    }

    /**
     * Limit number of stories
     *
     * @return int
     */
    public function getLimit() {
        return $this->limit;
    }

    /**
     * Search all stories
     *
     * @return array
     */
    public function search($params = array()) {

        $client = new \Guzzle\Http\Client();

        // Always include "done" stories from previous iterations
        if (isset($params['includeDone'])){
            $this->addFilter(array('includeDone' => $params['includeDone']));
        } else {
            $this->addFilter(array('includeDone' => 'true'));
        }

        if(isset($params['filters'])) {
            $this->addFilter($params['filters']);
        }

        if(count($this->filters) > 0) {
            $query['query'] = $this->api->formatFilter($this->filters);
        }

        $request = $client->get($this->url, array(
            'X-TrackerToken' => $this->api->getToken()
        ), array(
                'query' => $query
            )
        );

        $response = $request->send()->json();
        return $this->build($response['stories']['stories'], $params);
    }

    /**
     * Add search filter
     *
     * @param array $filters
     * @return this
     */
    public function addFilter($filters) {

        $filterFactory = new FilterFactory();

        if(count($filters) >= 0) {
            foreach ($filters as $type => $value) {
                if(!empty($value)) {
                    $class = $filterFactory->create($type);
                    $this->filters = array_merge($class->filter($value), $this->filters);
                }
            }
        }

        return $this;
    }

    /**
     * Build Story Collection
     *
     * @param $stories
     * @param array $params
     * @return StoryCollection
     */
    protected function build($stories, $params = array()) {

        if(isset($params['changelog'])) {
            return $this->groupByStatus($stories, array('bug', 'feature'));
        }

        if(isset($params['release'])) {
            $stories = $this->sortByDeadline($stories);
        }

        $collection = new \PivotalTracker\StoryCollection;

        foreach($stories as $story) {
            $collection->add($this->storyItem($story));
        }

        return $collection;
    }

    /**
     * Story item
     *
     * Filter story values returned from API
     *
     * @param $story
     * @return array
     */
    protected function storyItem($story) {
        return array(
            'id' => $story['id'],
            'name' => $story['name'],
            'description' => (isset($story['description']))? $story['description'] : '',
            'current_state' => $story['current_state'],
            'story_type' => $story['story_type'],
            'deadline' => (isset($story['deadline'])) ? $story['deadline'] : '',
            'url' => $story['url']
        );
    }

    /**
     * Group by stories status
     *
     * @params $status array of expected story statuses
     */
    protected function groupByStatus($stories, $statuses = array()) {

        $group = array();

        foreach ($stories as $story) {
            if (in_array($story['story_type'], $statuses)) {
                $group[$story['story_type']][] = $this->storyItem($story);
            }
        }

        $collection = new \PivotalTracker\StoryCollection;

        foreach($statuses as $status) {
            if(isset($group[$status])) {
                $collection->set($status, $group[$status]);
            }
        }

        return $collection;
    }

    /**
     * Sort by Deadline
     *
     * @param $stories
     */
    protected function sortByDeadline($stories) {
        $sortedStories = array();
        $nulls = array();

        foreach($stories as $k=>$v) {
            $lowerStories[$k] = strtolower($v['deadline']);
        }
        asort($lowerStories);
        foreach($lowerStories as $key=>$val) {
            if (empty($val)){
                $nulls[] = $key;
            } else {
                $sortedStories[] = $stories[$key];
            }
        }
        foreach ($nulls as $recordKey){
            $sortedStories[] = $stories[$recordKey];
        }
        return $sortedStories;
    }
}