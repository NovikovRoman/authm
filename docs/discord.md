# Discord Example

```php
<?php
require_once __DIR__ . '/vendor/autoload.php';

use AuthManager\Exceptions\APIException;
use AuthManager\OAuthManager;
use AuthManager\OAuthProviders\Discord\User;
use AuthManager\OAuthProviders\Discord\Provider;
use GuzzleHttp\Exception\GuzzleException;

$clientID = 'our client id';
$secretKey = 'our secret key';

$client = new Provider(
    $clientID,
    $secretKey,
    ['identify', 'email'],
    'https://our.domain'
);

$am = new OAuthManager($client);
$state = 123456;

if (!empty($_GET['code'])) {
    try {
        $token = $am->getToken($_SERVER['REQUEST_URI'], $state);
        $client->setToken($token);

        $u = new User($client);
        print_r($u->me());

    } catch (APIException $e) {
        exit($e->getMessage());

    } catch (GuzzleException $e) {
        exit($e->getMessage());
    }

} else {
    $am->signin($state, true, ['prompt' => true]);
}
```
[back][]

[back]: ../README.md