<?php

namespace mrpc\biblionetClient;

class Client
{
    /**
     * API URL
     * @var string
     */
    protected $apiUrl
        = 'https://biblionet.diadrasis.net/wp-json/biblionetwebservice/';
    /**
     * Client username
     * @var string
     */
    public $username = '';
    /**
     * Client password
     * @var string
     */
    public $password = '';

    /**
     * Biblionet webservice client
     * @link https://biblionet.diadrasis.net/webservicetest/ Information
     * @param string $username User name
     * @param string $password Password
     */
    public function __construct($username = 'testuser', $password = 'testpsw')
    {
        $this->username = $username;
        $this->password = $password;
    }


    /**
     * Get all titles published for a specific month
     * @param int $month
     * @param int $year
     * @param int $page
     * @param int $perPage
     * @return object[] An array of products or null if no results
     */
    public function getMonthTitles($month, $year, $page = 1, $perPage = 50)
    {
        $data = $this->callAPI(
            'get_month_titles',
            array(
                'month' => $month,
                'year' => $year,
                'titles_per_page' => $perPage,
                'page' => $page
            )
        );
        if (!is_array($data) || count($data) == 0) {
            return null;
        }
        if ($data[0] == null) {
            return null;
        }
        return $data[0];
    }

    /**
     * Get details about a title
     * @param int $titleid
     * @return object A book object or null on no result
     */
    public function getTitle($titleid)
    {
        $data = $this->callAPI('get_title', array('title' => $titleid));
        if (!is_array($data) || count($data) == 0) {
            return null;
        }
        if ($data[0] == null) {
            return null;
        }
        return $data[0][0];
    }


    /**
     * Do the actual API call
     * @param string $endpoint
     * @param array $data
     * @param string $method
     * @return type
     */
    protected function callAPI($endpoint, $data = array())
    {
        $curl = curl_init();

        $url = $this->apiUrl . $endpoint;
        $data['username'] = $this->username;
        $data['password'] = $this->password;

        curl_setopt($curl, CURLOPT_POST, 1);
        if ($data) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }

        // OPTIONS:
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        // EXECUTE:
        $result = curl_exec($curl);
        if (!$result) {
            return false;
        }
        curl_close($curl);

        if (is_object(json_decode($result))
            || is_array(json_decode($result))) {
            return json_decode($result);
        }
        throw new \Exception(
            json_decode($result)
        );

    }
}