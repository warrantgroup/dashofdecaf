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
     * Format search query
     *
     * https://www.pivotaltracker.com/help/faq#howcanasearchberefined
     *
     * @param $filters
     * @return array|string
     */
    public function formatSearchQuery($filters) {

        if(!is_array($filters)) {
           return array();
        }

        $queryString = array();

        foreach($filters as $k => $filter) {
            $queryString[] =  $k . ':' . $filter;
        }

        return implode(' ', $queryString);
    }
}