<?

namespace PivotalTracker;

class Api {

    protected $token;
    protected $projectId;

    public function __construct($params) {
        $this->token = $params['apiKey'];
        $this->projectId = $params['projectId'];
    }

    public function getToken() {
        return $this->token;
    }

    public function getProjectId() {
        return $this->projectId;
    }

    /**
     * Format Filter
     *
     * https://www.pivotaltracker.com/help/faq#howcanasearchberefined
     *
     * @param $filters
     * @return array|string
     */
    public function formatFilter($filters) {

        if(!is_array($filters) || empty($filters)) {
           return '';
        }

        $searchString = array();

        foreach($filters as $k => $filter) {
            $searchString[] = is_array($filter) ? $k . ':' . implode(',', $filter) : $k . ':' . $filter;
        }

        return implode(' ', $searchString);
    }
}