# Google Example

```php
<?php
require_once __DIR__ . '/vendor/autoload.php';

use AuthManager\Exceptions\APIException;
use AuthManager\OAuthManager;
use GuzzleHttp\Exception\GuzzleException;
use AuthManager\OAuthProviders\Google\Provider;
use AuthManager\OAuthProviders\Google\People;

$clientID = 'our client id';
$secretKey = 'our secret key';

$client = new Provider(
    $clientID,
    $secretKey,
    ['https://www.googleapis.com/auth/userinfo.email', 'https://www.googleapis.com/auth/userinfo.profile'],
    'https://our.domain'
);

$am = new OAuthManager($client);
$state = 123456;

if (!empty($_GET['code'])) {
    try {
        $token = $am->getToken($_SERVER['REQUEST_URI'], $state);
        $client->setToken($token);

        $people = new People($client);
        print_r($people->me(['emailAddresses', 'photos']));

    } catch (GuzzleException $e) {
        exit($e->getMessage());

    } catch (APIException $e) {
        exit($e->getMessage());

    }

} else {
    $am->signin($state, true, ['access_type' => 'offline']);
}
```