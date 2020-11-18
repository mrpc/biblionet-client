# Biblionet Client class
Client library for the web service of biblionet: https://biblionet.gr/webservice/

## How to install:
You can install this class using composer, just run:
```
#composer require mrpc/biblionet-client
```
in the root of your project

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
