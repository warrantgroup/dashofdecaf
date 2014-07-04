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
                    'filter' => $this->filter($params)
                )
            )
        )->send();

       return $this->buildChangeLog($response->json());
    }

    /**
     * Build Filters
     *
     * @param $params
     * @return string
     */
    protected function filter($params) {

        if(!isset($params['range'])) {
            $params['range'] = 'fortnight';
        }

        if(!isset($params['label'])) {
            $params['label'] = null;
        }

        $range = $this->convertDateRange($params['range']);

        $filters = array(
            'state' => 'feature,bug',
            'accepted' => $range['startDate'] . '..' . $range['endDate']
        );

        if($params['label']) {
            if(is_array($params['label'])) {
                $filters['label'] = implode(',', $params['label']);
            }else{
                $filters['label'] = $params['label'];
            }
        }

        var_dump($this->api->formatSearchQuery($filters));
        return $this->api->formatSearchQuery($filters);
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
            'startDate' => $range[0]->format('m/d/Y'),
            'endDate' => $range[1]->format('m/d/Y')
        );
    }

    /**
     * Build Change Log
     *
     * Filter all stories by type (feature, bug)
     */
    protected function buildChangeLog($stories) {

        $changelog = array(
            'feature' => array(),
            'bug' => array()
        );

        foreach($stories as $story) {
            if(in_array($story['story_type'], array_keys($changelog))) {
                $changelog[$story['story_type']][] = array(
                    'id' => $story['id'],
                    'name' => $story['name']
                );
            }
        }

        return $changelog;
    }

}