<?

/**
 * Pivotal Tracker Story
 */

namespace PivotalTracker;
use \Guzzle\Http\Exception\ClientErrorResponseException;
use PivotalTracker\StoryCollection;

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

        $request = $client->get($this->url, array(
            'X-TrackerToken' => $this->api->getToken()
        ), array(
                'query' => array(
                    'offset' => (isset($params['offset'])) ? $params['offset'] * $params['limit'] : 0,
                    'limit' => $params['limit']
                )
            )
        );

        if(isset($params['filter'])) {
           $this->filters = $params['filter'];
        }

        if(count($this->filters) > 0) {
            $query = $request->getQuery();
            $query->set('filter', $this->api->formatFilter($this->filters));
        }

        $response = $request->send();
        return $this->buildList($response->json());
    }

    /**
     * Add search filter
     *
     * @param $name
     * @param $filter
     */
    public function addFilter($name, $filter) {
        $this->filters[$name] = $filter;
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