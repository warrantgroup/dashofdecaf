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

        $request = $client->get($this->url, array(
            'X-TrackerToken' => $this->api->getToken()
        ), array(
                'query' => $query
            )
        );

        $response = $request->send();
        return $this->buildList($response->json());
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
    public function buildList($stories) {

        $collection = new StoryCollection();

        foreach($stories as $story) {
            $collection->add(array(
                'id' => $story['id'],
                'name' => $story['name'],
                'description' => (isset($story['description']))? $story['description'] : '',
                'current_state' => $story['current_state'],
                'story_type' => $story['story_type'],
                'url' => $story['url']
            ));
        }

        return $collection;
    }
}