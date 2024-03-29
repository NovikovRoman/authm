<?php

namespace AuthManager;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;

class OpenIDManager implements OpenIDManagerInterface
{
    private string $url;
    private string $returnTo;
    private Client $httpClient;
    private string $invalidateHandle = '';

    public function __construct(OpenIDInterface $provider, string $returnTo)
    {
        $this->url = $provider->getAuthURI();
        $this->returnTo = $returnTo;
        $this->httpClient = new Client(['timeout' => Constants::TIMEOUT]);
    }

    public function signin($redirect = false): string
    {
        $pUrl = parse_url($this->returnTo);
        $params = [
            'openid.ns' => 'https://specs.openid.net/auth/2.0',
            'openid.mode' => 'checkid_setup',
            'openid.return_to' => $this->returnTo,
            'openid.realm' => $pUrl['scheme'] . '://' . $pUrl['host'],
            'openid.ns.sreg' => 'https://openid.net/extensions/sreg/1.1',
            'openid.identity' => 'https://specs.openid.net/auth/2.0/identifier_select',
            'openid.claimed_id' => 'https://specs.openid.net/auth/2.0/identifier_select',
        ];
        $url = $this->url . '?' . http_build_query($params); // '', '&'
        if ($redirect) {
            header('Location: ' . $url);
            return '';
        }

        return $url;
    }

    /**
     * @param string $url
     * @return string
     * @throws GuzzleException
     */
    public function getID(string $url): string
    {
        $requestParameters = $this->explodeUrl($url);
        $params = [
            'openid.assoc_handle' => $requestParameters['openid_assoc_handle'],
            'openid.signed' => $requestParameters['openid_signed'],
            'openid.sig' => $requestParameters['openid_sig'],
            'openid.ns' => $requestParameters['openid_ns'],
            'openid.op_endpoint' => $requestParameters['openid_op_endpoint'],
            'openid.claimed_id' => $requestParameters['openid_claimed_id'],
            'openid.identity' => $requestParameters['openid_identity'],
            'openid.return_to' => $this->returnTo,
            'openid.response_nonce' => $requestParameters['openid_response_nonce'],
            'openid.mode' => 'check_authentication',
        ];
        if (!empty($requestParameters['openid_claimed_id'])) {
            $claimedId = $requestParameters['openid_claimed_id'];
        } else {
            $claimedId = $requestParameters['openid_identity'];
        }

        $response = $this->httpClient
            ->request('POST', $this->discover($claimedId), ['form_params' => $params,]);

        $ar = array_diff(explode("\n", $response->getBody()->getContents()), ['']);

        $respParams = [];
        foreach ($ar as $item) {
            list($name, $value) = explode(':', $item, 2);
            $respParams[$name] = $value;
        }

        if (isset($respParams['is_valid']) && $respParams['is_valid'] == 'true') {
            return $this->getIdFromIdentity($requestParameters['openid_identity']);
        }

        $this->invalidateHandle = empty($respParams['invalidate_handle']) ? '' : $respParams['invalidate_handle'];

        return '';
    }

    public function getInvalidateHandle(): string
    {
        return $this->invalidateHandle;
    }

    private function explodeUrl(string $url)
    {
        parse_str(parse_url($url, PHP_URL_QUERY), $requestParameters);

        $requiredKeys = ['openid_assoc_handle', 'openid_signed', 'openid_sig', 'openid_ns', 'openid_op_endpoint',
            'openid_claimed_id', 'openid_identity', 'openid_response_nonce'];
        foreach ($requiredKeys as $name) {
            if (empty($requestParameters[$name])) {
                $requestParameters[$name] = '';
            }
        }

        return $requestParameters;
    }

    private function getIdFromIdentity($identity)
    {
        preg_match('#/openid/id/(7[0-9]{15,25})#i',
            $identity, $m);
        return empty($m[1]) ? '' : $m[1];
    }

    /**
     * @param string $url
     * @return mixed
     * @throws GuzzleException
     * @throws Exception
     */
    private function discover(string $url)
    {
        $response = $this->httpClient->request('GET', $url);
        $contentType = $response->getHeader('Content-Type');
        if (empty($contentType) || !preg_match('#application/xrds\+xml#', $contentType[0])) {
            throw new BadResponseException('Unexpected Content-Type', null, $response);
        }

        $body = $response->getBody()->getContents();
        if (preg_match('#<URI>(.+?)</URI>#sui', $body, $m)) {
            return $m[1];
        }

        throw new Exception('URI not found. ' . $body);
    }
}