<?

/**
 * Pivotal Tracker Story
 */

namespace PivotalTracker;
use \Guzzle\Http\Exception\ClientErrorResponseException;

class Story {

    protected $api;

    public function __construct($api)
    {
        $this->api = $api;
        $this->url = sprintf('https://www.pivotaltracker.com/services/v5/projects/%s/stories', $api->getProjectId());
    }

    public function search() {
        $client = new \Guzzle\Http\Client();

        $response = $client->get($this->url, array(
            'X-TrackerToken' => $this->api->getToken(),
        ))->send();

        return $response->json();
    }
}