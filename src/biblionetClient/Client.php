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
     * το όνομα χρήστη του συνδρομητή στην ιστοσελίδα της βιβλιονετ
     * @var string
     */
    public $username = '';
    /**
     * ο κωδικός του χρήστη στην ιστοσελίδα της βιβλιονετ
     * @var string
     */
    public $password = '';
    /**
     * Ορίζει το αν θα γίνει διόρθωση των δεδομένων μετά την αρχική ανάκτηση
     * Για παράδειγμα, το CoverImage θα γίνει absolute url αντί για relative
     * @var bool
     */
    public $fixData = true;
    /**
     * Ορίζει το αν θα γίνει έλεγχος του ότι υπάρχει το CoverImage. Αυτό ισχύει
     * μόνο αν η fixData είναι true
     * @var bool
     */
    public $checkCover = true;
    /**
     * Το τελευταίο μήνυμα λάθους που έχει επιστρέψει το API
     * @var string
     */
    

    /**
     * Biblionet webservice client
     * @link https://biblionet.diadrasis.net/webservicetest/ Information
     * @param string $username το όνομα χρήστη του συνδρομητή στην ιστοσελίδα της βιβλιονετ
     * @param string $password ο κωδικός του χρήστη στην ιστοσελίδα της βιβλιονετ
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
        try {
            $data = $this->callAPI(
                'get_month_titles',
                array(
                    'month' => $month,
                    'year' => $year,
                    'titles_per_page' => $perPage,
                    'page' => $page
                )
            );
        } catch (\Exception $exc) {
            $this->lastError = $exc->getMessage();
            return null;
        }
        if (!is_array($data) || count($data) == 0) {
            return null;
        }
        if ($data[0] == null) {
            return null;
        }
        $returnData = array();
        foreach ($data[0] as $title) {
            $returnData[] = $this->fixTitlesData($title);
        }
        return $returnData;
    }


    /**
     * Αναζήτηση Θεμάτων Τίτλου
     * @param int $titleId To id του επιθυμητού τίτλου
     * @param bool $loadDetails Αν οριστεί σε true, για κάθε πρόσωπο θα γίνει
     *                          και μια κλήση στη getPerson και θα δημιουργηθεί
     *                          ένα νέο property, το "details", με όλη την
     *                          πληροφορία του προσώπου
     */
    public function getTitleSubjects($titleId, $loadDetails = false)
    {
        try {
            $data = $this->callAPI(
                'get_title_subject',
                array(
                    'title' => $titleId
                )
            );
        } catch (\Exception $exc) {
            $this->lastError = $exc->getMessage();
            return null;
        }
        if (!is_array($data) || count($data) == 0) {
            return null;
        }
        if ($data[0] == null) {
            return null;
        }

        
        $returnData = array();
        foreach ($data[0] as $title) {
            if ($loadDetails) {
                $title->details = $this->getSubject($title->SubjectsID);
            }
            $returnData[] = $title;
        }
        return $returnData;
    }


    /**
     * Αναζήτηση Συνεργατών Τίτλου
     * @param int $titleId To id του επιθυμητού τίτλου
     * @param bool $loadDetails Αν οριστεί σε true, για κάθε πρόσωπο θα γίνει
     *                          και μια κλήση στη getPerson και θα δημιουργηθεί
     *                          ένα νέο property, το "details", με όλη την
     *                          πληροφορία του προσώπου
     */
    public function getContributors($titleId, $loadDetails = false)
    {
        try {
            $data = $this->callAPI(
                'get_contributors',
                array(
                    'title' => $titleId
                )
            );
        } catch (\Exception $exc) {
            $this->lastError = $exc->getMessage();
            return null;
        }
        if (!is_array($data) || count($data) == 0) {
            return null;
        }
        if ($data[0] == null) {
            return null;
        }

        
        $returnData = array();
        foreach ($data[0] as $title) {
            $obj = $this->fixPersonData($title);
            if ($loadDetails) {
                $obj->details = $this->getPerson($obj->ContributorID);
            }
            $returnData[] = $obj;
        }
        return $returnData;
    }

    /**
     * Αναζήτηση Εταιρειών Τίτλου
     * @param int $titleId To id του επιθυμητού τίτλου
     * @param bool $loadDetails Αν οριστεί σε true, για κάθε εταιρεία θα γίνει
     *                          και μια κλήση στη getCompany και θα δημιουργηθεί
     *                          ένα νέο property, το "details", με όλη την
     *                          πληροφορία της εταιρείας
     */
    public function getTitleCompanies($titleId, $loadDetails = false)
    {
        try {
            $data = $this->callAPI(
                'get_title_companies',
                array(
                    'title' => $titleId
                )
            );
        } catch (\Exception $exc) {
            $this->lastError = $exc->getMessage();
            return null;
        }
        if (!is_array($data) || count($data) == 0) {
            return null;
        }
        if ($data[0] == null) {
            return null;
        }

        $returnData = array();
        foreach ($data[0] as $title) {
            if ($loadDetails) {
                $title->details = $this->getCompany($title->CompanyID);
            }
            $returnData[] = $title;
        }
        return $returnData;
    }
    
    /**
     * Αναζήτηση Πληροφοριών Θέματος
     * @param int $subjectid
     * @return object A subject object or null on no result
     */
    public function getSubject($subjectid)
    {
        try {
            $data = $this->callAPI(
                'get_subject', array('subject' => $subjectid)
            );
        } catch (\Exception $exc) {
            $this->lastError = $exc->getMessage();
            return null;
        }
        
        if (!is_array($data) || count($data) == 0) {
            return null;
        }
        if ($data[0] == null) {
            return null;
        }
        return $data[0][0];
    }

    /**
     * Αναζήτηση Πληροφοριών Γλώσσας
     * @param int $languageId
     * @return object A language object or null on no result
     */
    public function getLanguage($languageId)
    {
        try {
            $data = $this->callAPI(
                'get_language', array('language' => $languageId)
            );
        } catch (\Exception $exc) {
            $this->lastError = $exc->getMessage();
            return null;
        }
        
        if (!is_array($data) || count($data) == 0) {
            return null;
        }
        if ($data[0] == null) {
            return null;
        }
        return $data[0][0];
    }

    /**
     * Αναζήτηση Πληροφοριών Εταιρίας
     * @param int $companyid
     * @return object A company object or null on no result
     */
    public function getCompany($companyid)
    {
        try {
            $data = $this->callAPI(
                'get_company', array('company' => $companyid)
            );
        } catch (\Exception $exc) {
            $this->lastError = $exc->getMessage();
            return null;
        }
        
        if (!is_array($data) || count($data) == 0) {
            return null;
        }
        if ($data[0] == null) {
            return null;
        }
        return $data[0][0];
    }

    /**
     * Αναζήτηση αναλυτικών πληροφοριών προσώπου βάσει ID
     * @param int $personid
     * @return object A person object or null on no result
     */
    public function getPerson($personid)
    {
        try {
            $data = $this->callAPI('get_person', array('person' => $personid));
        } catch (\Exception $exc) {
            $this->lastError = $exc->getMessage();
            return null;
        }
        
        if (!is_array($data) || count($data) == 0) {
            return null;
        }
        if ($data[0] == null) {
            return null;
        }
        return $this->fixPersonData($data[0][0]);
    }
    
    /**
     * Τραβάει διαδοχικά τους τίτλους που ανανεώθηκαν σε όλες τις ημερομηνίες
     * από την $date ως σήμερα
     * @return object[] An array of products or null if no results
     */
    protected function getUpdatedTitlesUntilNow($date, $daysLimit = 30)
    {
        $allDates = new \DatePeriod(
            new \DateTime($date),
            new \DateInterval('P1D'),
            new \DateTime(date('Y-m-d'))
        );
        $returnArray = array();
        $cnt = 0;
        foreach ($allDates as $currentDate) {
            $cnt++;
            if ($cnt > $daysLimit) {
                break;
            }
            $tmpTitles = $this->getUpdatedTitles(
                $currentDate->format('Y-m-d'), false
            );
            if (is_array($tmpTitles)) {
                foreach ($tmpTitles as $title) {
                    $returnArray[$title->TitlesID] = $title;
                }
            }
        }
       return array_values($returnArray);
    }

    /**
     * Επιστρέφει όλους τους τίτλους που καταχωρήθηκαν 
     * ή ενημερώθηκαν σε μια συγκεκριμένη ημερομηνία
     * @param string|int $date Ημερομηνία σε μορφή ΕΕΕΕ-ΜΜ-ΗΗ ή Unix Timestamp
     * @param bool $untilNow Αν γίνει true, η method επιστρέφει όλες τις 
     *                       ημερομηνίες από τημ $date ως σήμερα.
     *                       Για λόγους απόδοσης, έχει όριο στις 30 μέρες
     * @return object[] An array of products or null if no results
     */
    public function getUpdatedTitles($date, $untilNow = false)
    {
        if (is_numeric($date)) {
            $date = date('Y-m-d', $date);
        }
        if ($untilNow === true) {
            return $this->getUpdatedTitlesUntilNow($date);
        }
        try {
            $data = $this->callAPI(
                'get_title',
                array(
                    'lastupdate' => $date
                )
            );
        } catch (\Exception $exc) {
            $this->lastError = $exc->getMessage();
            return null;
        }
        if (!is_array($data) || count($data) == 0) {
            return null;
        }
        if ($data[0] == null) {
            return null;
        }
        $returnData = array();
        foreach ($data[0] as $title) {
            $returnData[] = $this->fixTitlesData($title);
        }
        return $returnData;
    }

    /**
     * Αναζήτηση αναλυτικών πληροφοριών τίτλου βάσει ID
     * @param int $titleid
     * @return object A book object or null on no result
     */
    public function getTitle($titleid)
    {
        try {
            $data = $this->callAPI('get_title', array('title' => $titleid));
        } catch (\Exception $exc) {
            $this->lastError = $exc->getMessage();
            return null;
        }
        
        if (!is_array($data) || count($data) == 0) {
            return null;
        }
        if ($data[0] == null) {
            return null;
        }
        return $this->fixTitlesData($data[0][0]);
    }

    /**
     * Αναζήτηση αναλυτικών πληροφοριών τίτλου βάσει ISBN
     * @param string $isbn To isbn του ζητούμενου τίτλου. Οι παύλες αγνούνται
     * @return object A book object or null on no result
     */
    public function getTitleByISBN($isbn)
    {
        echo $isbn . "\n";
        try {
            $data = $this->callAPI(
                'get_title', 
                array(
                    'isbn' => trim($isbn)
                )
            );
        } catch (\Exception $exc) {
            $this->lastError = $exc->getMessage();
            return null;
        }
        if (!is_array($data) || count($data) == 0) {
            return null;
        }
        if ($data[0] == null) {
            return null;
        }
        return $this->fixTitlesData($data[0][0]);
    }

    /**
     * Διορθώνει πληροφορίες του τίτλου
     */
    protected function fixTitlesData($titleObj)
    {
        if (!is_object($titleObj)) {
            return $titleObj;
        }
        if (!$this->fixData) {
            return $titleObj;
        }
        if (isset($titleObj->CoverImage) && $titleObj->CoverImage != '') {
            $titleObj->CoverImage = 'https://biblionet.gr' 
                . $titleObj->CoverImage;
            if ($this->checkCover && !$this->urlExists($titleObj->CoverImage)) {
                $titleObj->CoverImage = null;
            }
        }
        return $titleObj;
    }

    /**
     * Διορθώνει πληροφορίες του προσώπου
     */
    protected function fixPersonData($personObj)
    {
        if (!is_object($personObj)) {
            return $personObj;
        }
        if (!$this->fixData) {
            return $personObj;
        }
        if (isset($personObj->Photo) && $personObj->Photo != '') {
            $personObj->Photo = 'https://biblionet.gr' 
                . $personObj->Photo;
            if ($this->checkCover && !$this->urlExists($personObj->Photo)) {
                $personObj->Photo = null;
            }
        }
        return $personObj;
    }


    /**
     * Do the actual API call
     * @param string $endpoint
     * @param array $data
     * @param string $method
     * @return mixed
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
        curl_setopt(
            $curl, CURLOPT_HTTPHEADER, 
            array(
                'Content-Type: multipart/form-data',
                'Cache-Control: no-cache',
            )
        );
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

    /**
     * Check if an external url exists or returns any kind of error
     * @param string $url
     * @param int $timeout
     * @return boolean
     */
    protected function urlExists($url, $timeout = 2)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_exec($ch);
        if(curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200)
        {
            curl_close($ch);
            return true;
        }
        curl_close($ch);
        return false;
    }
}