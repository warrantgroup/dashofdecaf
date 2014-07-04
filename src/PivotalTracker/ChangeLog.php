<?

/**
 * Pivotal Tracker Changelog
 */

namespace PivotalTracker;

use \Guzzle\Http\Exception\ClientErrorResponseException;

class ChangeLog
{

    protected $api;

    public function __construct($api)
    {
        $this->api = $api;
        $this->url = sprintf('https://www.pivotaltracker.com/services/v5/projects/%s/stories', $api->getProjectId());
    }

    public function build($params)
    {
        $client = new \Guzzle\Http\Client();

        $response = $client->get($this->url, array(
                'X-TrackerToken' => $this->api->getToken(),
                'query' => array(
                    'filter' => $this->buildFilter($params)
                )
            )
        )->send();

        var_dump($response->json());
    }

    /**
     * Build Filters
     *
     * @param $params
     * @return string
     */
    protected function buildFilter($params) {

        $range = $this->convertDateRange($params['range']);

        $filters = array(
            'state' => 'feature,bug',
            'accepted_after' => $range['startDate'],
            'accepted_before' => $range['endDate']
        );

        if(is_array($params['label'])) {
            $filters[] = implode(',', $params['label']);
        }

        return implode(' ', $filters);
    }

    /**
     * Convert Date Ranges
     *
     * @param $type
     * @return array
     */
    protected function convertDateRange($type) {

        $range = array();

        switch($type) {
            case 'week' :
                $range[0] = new \DateTime('Monday this week');
                $range[1] = new \DateTime('Sunday this week');
            break;

            case 'month':
                $range[0]= new \DateTime('first day of this month');
                $range[1] = new \DateTime('last day of this month');
            break;

            case 'year' :
                $range[0] = new \DateTime('first day of this year');
                $range[1] = new \DateTime('last day of this year');
            break;

            default:
            case 'fortnight':
                $range[0] = new \DateTime('Monday last week');
                $range[1] = new \DateTime('Sunday this week');
                break;
        }

        return array(
            'startDate' => $range[0]->format('Y-m-d'),
            'endDate' => $range[1]->format('Y-m-d')
        );
    }

}