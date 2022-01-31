# BattleNet Example

```php
<?php
require_once __DIR__ . '/vendor/autoload.php';

use AuthManager\Exceptions\APIException;
use AuthManager\OAuthProviders\BattleNet\ProviderEU;
use AuthManager\OAuthManager;
use GuzzleHttp\Exception\GuzzleException;
use AuthManager\OAuthProviders\BattleNet\Userinfo;

$clientID = 'our client id';
$secretKey = 'our secret key';

$client = new ProviderEU(
    $clientID,
    $secretKey,
    ['wow.profile'],
    'https://our.domain'
);

$am = new OAuthManager($client);
$state = 123456;

if (!empty($_GET['code'])) {
    try {
        $token = $am->getToken($_SERVER['REQUEST_URI'], $state);
        $client->setToken($token);
        
        $userinfo = new Userinfo($client);
        print_r($userinfo->get());

    } catch (APIException $e) {
        exit($e->getMessage());

    } catch (GuzzleException $e) {
        exit($e->getMessage());
    }

} else {
    $am->signin($state);
}
```
[back][]

[back]: ../README.md