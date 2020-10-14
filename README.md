# Biblionet Client class
Client library for the web service of biblionet: https://biblionet.diadrasis.net/webservicetest/

## Example use:
```php
$client = new \mrpc\biblionetClient\Client();
$book = $client->getTitle(250081);
```

## CLI usage:
There is a CLI file to do test queries to the api using shell. To test it, run:
```
#php cli.php help
```
