#!/usr/bin/php
<?php


function showHelp()
{
    ?>
Biblionet CLI

Usage:
php cli.php [command]

    help: Display this help
    getTitle [TitlesID]: Get information about a specific title
    getTitleByISBN [isbn]: Get information about a specific title
    getMonthTitles [month] [year] [page] [perpage]: Get all titles published for a specific month
    getUpdatedTitles [date]: Επιστρέφει όλους τους τίτλους που καταχωρήθηκαν ή ενημερώθηκαν από συγκεκριμένη ημερομηνία και μετά
    getUpdatedPersons [date]: Επιστρέφει όλα τα πρόσωπα που καταχωρήθηκαν ή ενημερώθηκαν από συγκεκριμένη ημερομηνία και μετά
    getContributors [titleId]: Αναζήτηση Συνεργατών Τίτλου
    getTitleCompanies [titleId]: Αναζήτηση Εταιρειών Τίτλου
    getTitleSubjects [titleId]: Αναζήτηση Θεμάτων Τίτλου
    getPerson [personid]: Αναζήτηση Πληροφοριών Προσώπου
    getCompany [companyid]: Αναζήτηση Πληροφοριών Εταιρείας
    getSubject [subjectid]: Αναζήτηση Πληροφοριών Θέματος
    getLanguage [languageid]: Αναζήτηση Πληροφοριών Γλώσσας

<?php
    exit(0);
}

$params = $argv;
if ($argc < 2) {
    showHelp();
}

if (!isset($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = 'localhost';
    $_SERVER['SERVER_PORT'] = 80;
    $_SERVER['SERVER_NAME'] = 'localhost';
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
    $_SERVER['HTTP_USER_AGENT'] = 'CLI';
    $_SERVER['REQUEST_URI'] = '/';
}



require_once 'src/biblionetClient/Client.php';
switch ($params[1]) {
    case "--help":
    case "-help":
    case "help":
    case "-h":
    case "?":
        showHelp();
        break;
    case "getTitle";
        if (!isset($params[2])) {
            echo "Please specify a TitlesID\n";
            break;
        }
        $client = new \mrpc\biblionetClient\Client();
        var_dump($client->getTitle($params[2]));
        break;
    case "getPerson":
        if (!isset($params[2])) {
            echo "Please specify a PersonID\n";
            break;
        }
        $client = new \mrpc\biblionetClient\Client();
        var_dump($client->getPerson($params[2]));
        break;
    case "getCompany":
        if (!isset($params[2])) {
            echo "Please specify a CompanyID\n";
            break;
        }
        $client = new \mrpc\biblionetClient\Client();
        var_dump($client->getCompany($params[2]));
        break;
    case "getSubject":
        if (!isset($params[2])) {
            echo "Please specify a SubjectID\n";
            break;
        }
        $client = new \mrpc\biblionetClient\Client();
        var_dump($client->getSubject($params[2]));
        break;
    case "getLanguage":
        if (!isset($params[2])) {
            echo "Please specify a Language ID\n";
            break;
        }
        $client = new \mrpc\biblionetClient\Client();
        var_dump($client->getLanguage($params[2]));
        break;
    case "getUpdatedTitles";
        if (!isset($params[2])) {
            echo "Please specify a date\n";
            break;
        }
        $client = new \mrpc\biblionetClient\Client();
        var_dump($client->getUpdatedTitles($params[2], true));
        break;
    case "getUpdatedPersons";
        if (!isset($params[2])) {
            echo "Please specify a date\n";
            break;
        }
        $client = new \mrpc\biblionetClient\Client();
        var_dump($client->getUpdatedPersons($params[2], true));
        break;
    case "getTitleByISBN";
        if (!isset($params[2])) {
            echo "Please specify an ISBN\n";
            break;
        }
        $client = new \mrpc\biblionetClient\Client();
        var_dump($client->getTitleByISBN($params[2]));
        break;
    case "getTitleCompanies":
        if (!isset($params[2])) {
            echo "Please specify an title id\n";
            break;
        }
        $client = new \mrpc\biblionetClient\Client();
        var_dump($client->getTitleCompanies($params[2], true));
        break;
    case "getContributors":
        if (!isset($params[2])) {
            echo "Please specify an title id\n";
            break;
        }
        $client = new \mrpc\biblionetClient\Client();
        var_dump($client->getContributors($params[2], true));
        break;
    case "getTitleSubjects":
            if (!isset($params[2])) {
                echo "Please specify an title id\n";
                break;
            }
            $client = new \mrpc\biblionetClient\Client();
            var_dump($client->getTitleSubjects($params[2], true));
            break;
    case "getMonthTitles";
        if (!isset($params[3])) {
            echo "Please specify month and year\n";
            break;
        }
        if (!isset($params[4])) {
            $params[4] = 1;
        }
        if (!isset($params[5])) {
            $params[5] = 50;
        }
        $client = new \mrpc\biblionetClient\Client();
        var_dump(
            $client->getMonthTitles(
                $params[2], $params[3], $params[4], $params[5]
            )
        );
        break;
    default:
        echo "Invalid Command. "
        . "Please use:\nphp cli.php --help\nfor help.\n";
        break;
}


exit(0);