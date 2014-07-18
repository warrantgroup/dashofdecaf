<?

/**
 * Pivotal Tracker Story
 */

namespace PivotalTracker;
use \Guzzle\Http\Exception\ClientErrorResponseException;
use PivotalTracker\StoryCollection, PivotalTracker\Filter\FilterFactory;

class Story {

    protected $api;
    protected $filters = array();

    public function __construct($api)
    {
        $this->api = $api;
        $this->url = sprintf('https://www.pivotaltracker.com/services/v5/projects/%s/stories', $api->getProjectId());
    }

    /**
     * Search all stories
     *
     * @return array
     */
    public function search($params = array()) {
        $client = new \Guzzle\Http\Client();

        if(!isset($params['limit'])) {
            $params['limit'] = 20;
        }

        // Always include "done" stories from previous iterations
        $this->addFilter(array('includeDone' => 'true'));

        if(isset($params['filters'])) {
            $this->addFilter($params['filters']);
        }

        $query = array(
            'offset' => (isset($params['offset'])) ? $params['offset'] * $params['limit'] : 0,
            'limit' => $params['limit']
        );

        if(count($this->filters) > 0) {
            $query['filter'] = $this->api->formatFilter($this->filters);
        }

        //var_dump($query['filter']);

        $request = $client->get($this->url, array(
            'X-TrackerToken' => $this->api->getToken()
        ), array(
                'query' => $query
            )
        );

        $response = $request->send();
        return $this->buildList($response->json(), $params);
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
     * Build story list
     *
     * @param $stories
     * @return mixed
     */
    public function buildList($stories, $params = array()) {

        if(isset($params['changelog'])) {
            return $this->groupByStatus($stories, array('bug', 'feature'));
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
     */
    protected function storyItem($story) {
        return array(
            'id' => $story['id'],
            'name' => $story['name'],
            'description' => (isset($story['description']))? $story['description'] : '',
            'current_state' => $story['current_state'],
            'story_type' => $story['story_type'],
            'url' => $story['url']
        );
    }

    /**
     * Group by stories status
     *
     * @params $status array of expected story statuses
     */
    public function groupByStatus($stories, $statuses = array()) {

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
}