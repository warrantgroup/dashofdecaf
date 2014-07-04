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
}