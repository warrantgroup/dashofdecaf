<?

/**
 * Pivotal Tracker Changelog
 */

namespace PivotalTracker;

use \Guzzle\Http\Exception\ClientErrorResponseException;

class ChangeLog
{

    const PROJECT = 433999;
    const TOKEN = 'd26b8fbb80bb4aab5f771fb7833712c1';

    public function __construct()
    {
        $this->url = sprintf('https://www.pivotaltracker.com/services/v5/projects/%s/stories', self::PROJECT);
    }

    public function build()
    {

        $client = new \Guzzle\Http\Client();

        $response = $client->get($this->url, array(
            'X-TrackerToken' => self::TOKEN
        ))->send();

        var_dump($response->json());
    }
    
}