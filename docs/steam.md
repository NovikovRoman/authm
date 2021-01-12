# Steam Example

```php
<?php
require_once __DIR__ . '/vendor/autoload.php';

use AuthManager\OpenIDManager;
use AuthManager\OpenIDProviders\Steam\Provider as SteamProvider;
use GuzzleHttp\Exception\GuzzleException;
use AuthManager\OpenIDProviders\Steam\ISteamUser;

$apiKey = 'our api key';

$steam = new SteamProvider();
$am = new OpenIDManager($steam, 'https://our.domain/this/script');

if (!empty($_GET['openid_mode']) && $_GET['openid_mode'] == 'id_res') {

    try {
        $id = $am->getID($_SERVER['REQUEST_URI']);
        if (!$id) {
            exit('Unauthorized');
        }
        print_r($id . "\n");

        $c = new ISteamUser($apiKey);
        print_r($c->getPlayerSummaries($id));

    } catch (GuzzleException $e) {
        exit($e->getMessage());
    }

} else {
    $am->signin(true);
    // optional
    // print_r($am->signin());
}
```