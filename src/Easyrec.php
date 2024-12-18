<?php

namespace Antoineaugusti\LaravelEasyrec;

use Antoineaugusti\LaravelEasyrec\Exceptions\EasyrecException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Message;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use InvalidArgumentException;

class Easyrec
{
    private ?string $endpoint = null;

    // private $httpClient;

    private array $queryParams;

    private ?array $response = null;

    private $tenantKey;

    private $profileData;

    private $config;

    public function __construct($config)
    {
        $this->config = $config;
        $this->endpoint = null;
        $this->response = null;

        // Register Guzzle
        // $this->setHttpClient(new HTTPClient(['base_url' => $this->getBaseURL()]));

        // Set API key and tenantID
        $this->queryParams = [
            'apikey' => $config['apiKey'],
            'tenantid' => $config['tenantID'],
        ];
    }

    /*
    public function setHttpClient($object)
    {
        $this->httpClient = $object;
    }
    */

    /**
     * @return string
     */
    public function getBaseURL(): string
    {
        return $this->config['baseURL'] . '/api/' . $this->config['apiVersion'] . '/json/';
    }

    /**
     * @return string
     */
    public function getBaseApiURL(): string
    {
        return $this->config['baseURL'] . '/api/' . $this->config['apiVersion'] . '/';
    }

    /*
    * ACTIONS
    * --------------------
    */

    /**
     * This action should be raised if a user views an item.
     *
     * @param  string  $tenantKey Tenant Key
     * @param  string  $itemid An item ID to identify an item on your website. Eg: "POST42"
     * @param  string  $itemdescription An item description that is displayed when showing recommendations on your website.
     * @param  string  $itemurl An item URL that links to the item page. Please give an absolute path.
     * @param  string  $userid A user ID.
     * @param  string  $itemimageurl An optional item image URL that links to an imagine of the item. Please give an absolute path.
     * @param  string  $actiontime An action time parameter that overwrites the current timestamp of the action. The parameter has the format "dd_MM_yyyy_HH_mm_ss".
     * @param  string  $itemtype An item type that denotes the type of the item (`IMAGE`, `BOOK` etc.). If not supplied, the default value `ITEM` will be used.
     * @param  string  $sessionid A session ID of a user.
     * @return array The decoded JSON response
     */
    public function view(
        $tenantKey,
        $itemid,
        $itemdescription,
        $itemurl,
        $userid = null,
        $itemimageurl = null,
        $actiontime = null,
        $itemtype = null,
        $sessionid = null
    ) {
        if (is_null($sessionid)) {
            $sessionid = Session::getId();
        }

        foreach (['itemid', 'itemdescription', 'itemurl', 'userid', 'itemimageurl', 'actiontime', 'itemtype', 'sessionid'] as $param) {
            $this->setQueryParam($param, $$param);
        }

        // Set the endpoint name and send the request
        $this->setEndpoint('view');

        $this->tenantKey = $tenantKey;

        return $this->sendRequest();
    }

    /**
     * @see view
     */
    public function buy(
        $tenantKey,
        $itemid,
        $itemdescription,
        $itemurl,
        $userid = null,
        $itemimageurl = null,
        $actiontime = null,
        $itemtype = null,
        $sessionid = null
    ) {
        if (is_null($sessionid)) {
            $sessionid = Session::getId();
        }

        foreach (['itemid', 'itemdescription', 'itemurl', 'userid', 'itemimageurl', 'actiontime', 'itemtype', 'sessionid'] as $param) {
            $this->setQueryParam($param, $$param);
        }

        // Set the endpoint name and send the request
        $this->setEndpoint('buy');

        $this->tenantKey = $tenantKey;

        return $this->sendRequest();
    }

    /**
     * This action should be raised if a user rates an item.
     *
     * @param  string  $tenantKey Tenant Key
     * @param  string  $itemid An item ID to identify an item on your website. Eg: "POST42"
     * @param  int  $ratingvalue The rating value of the item. Must be an integer in the range from 1 to 10.
     * @param  string  $itemdescription An item description that is displayed when showing recommendations on your website.
     * @param  string  $itemurl An item URL that links to the item page. Please give an absolute path.
     * @param  string  $userid A user ID.
     * @param  string  $itemimageurl An optional item image URL that links to an imagine of the item. Please give an absolute path.
     * @param  string  $actiontime An action time parameter that overwrites the current timestamp of the action. The parameter has the format "dd_MM_yyyy_HH_mm_ss".
     * @param  string  $itemtype An item type that denotes the type of the item (`IMAGE`, `BOOK` etc.). If not supplied, the default value `ITEM` will be used.
     * @param  string  $sessionid A session ID of a user.
     * @return array The decoded JSON response
     */
    public function rate(
        $tenantKey,
        $itemid,
        $ratingvalue,
        $itemdescription,
        $itemurl,
        $userid = null,
        $itemimageurl = null,
        $actiontime = null,
        $itemtype = null,
        $sessionid = null
    ) {
        // Check that the $ratingvalue as got the expected format
        if ($ratingvalue > 10 || ! is_numeric($ratingvalue) || $ratingvalue < 1) {
            throw new InvalidArgumentException('The rating value should be between 1 and 10.', 1);
        }

        if (is_null($sessionid)) {
            $sessionid = Session::getId();
        }

        foreach (['userid', 'ratingvalue', 'sessionid', 'itemid', 'itemdescription', 'itemurl', 'itemimageurl', 'actiontime', 'itemtype'] as $param) {
            $this->setQueryParam($param, $$param);
        }

        // Set the endpoint name and send the request
        $this->setEndpoint('rate');

        $this->tenantKey = $tenantKey;

        return $this->sendRequest();
    }

    /**
     * This action can be used to send generic user actions.
     *
     * @param  string  $tenantKey Tenant Key
     * @param  string  $itemid An item ID to identify an item on your website. Eg: "POST42"
     * @param  string  $itemdescription An item description that is displayed when showing recommendations on your website.
     * @param  string  $itemurl An item URL that links to the item page. Please give an absolute path.
     * @param  string  $actiontype A required action type you want to use to send.
     * @param  string  $actionvalue If your action type uses action values this parameter is required.
     * @param  string  $userid A user ID.
     * @param  string  $itemimageurl An optional item image URL that links to an imagine of the item. Please give an absolute path.
     * @param  string  $actiontime An action time parameter that overwrites the current timestamp of the action. The parameter has the format "dd_MM_yyyy_HH_mm_ss".
     * @param  string  $itemtype An item type that denotes the type of the item (`IMAGE`, `BOOK` etc.). If not supplied, the default value `ITEM` will be used.
     * @param  string  $sessionid A session ID of a user.
     * @return array The decoded JSON response
     */
    public function sendAction(
        $tenantKey,
        $itemid,
        $itemdescription,
        $itemurl,
        $actiontype,
        $actionvalue = null,
        $userid = null,
        $itemimageurl = null,
        $actiontime = null,
        $itemtype = null,
        $sessionid = null
    ) {
        if (is_null($sessionid)) {
            $sessionid = Session::getId();
        }

        foreach (['itemid', 'itemdescription', 'itemurl', 'actiontype', 'actionvalue', 'userid', 'itemimageurl', 'actiontime', 'itemtype', 'sessionid'] as $param) {
            $this->setQueryParam($param, $$param);
        }

        // Set the endpoint name and send the request
        $this->setEndpoint('sendaction');

        $this->tenantKey = $tenantKey;

        return $this->sendRequest();
    }

    /*
    * RECOMMENDATIONS
    * --------------------
    */

    /**
     * General method used to hit a recommendation endpoint of the API
     *
     * @param  string  $endpoint The name of the API endpoint
     * @param  string  $itemid A required item ID to identify an item on your website. (e.g. "ID001")
     * @param  mixed  $userid If this parameter is provided items viewed by this user are suppressed.
     * @param  int  $numberOfResults An optional parameter to determine the number of results returned. Should be between 1 and 15.
     * @param  string  $itemtype An optional item type that denotes the type of the item (e.g. IMAGE, VIDEO, BOOK, etc.). If not supplied the default value ITEM will be used.
     * @param  string  $requesteditemtype An optional item type that denotes the type of the item (e.g. IMAGE, VIDEO, BOOK, etc.). If not supplied the default value ITEM will be used.
     * @param  bool  $withProfile If this parameter is set to true the result contains an additional element 'profileData' with the item profile.
     * @return array The decoded JSON response
     *
     * @throws \InvalidArgumentException if the number of results is not a number or is negative
     */
    private function abstractRecommendationEndpoint($endpoint, $itemid, $userid = null, $numberOfResults = 10, $itemtype = null, $requesteditemtype = null, $withProfile = false)
    {
        // Check that $numberOfResults has got the expected format
        if (! is_numeric($numberOfResults) || $numberOfResults < 0) {
            throw new InvalidArgumentException('The number of results should be at least 1.', 1);
        }

        // Can't currently retrieve more than 15 results
        $numberOfResults = min($numberOfResults, 15);

        foreach (['itemid', 'userid', 'numberOfResults', 'itemtype', 'requesteditemtype', 'withProfile'] as $param) {
            $this->setQueryParam($param, $$param);
        }

        // Set the endpoint name and send the request
        $this->setEndpoint($endpoint);

        return $this->sendRequest();
    }

    /**
     * @see abstractRecommendationEndpoint
     */
    public function usersAlsoViewed(
        $tenantKey,
        $itemid,
        $userid = null,
        $numberOfResults = 10,
        $itemtype = null,
        $requesteditemtype = null,
        $withProfile = false
    ) {
        $this->tenantKey = $tenantKey;

        return $this->abstractRecommendationEndpoint('otherusersalsoviewed', $itemid, $userid, $numberOfResults, $itemtype, $requesteditemtype, $withProfile);
    }

    /**
     * @see abstractRecommendationEndpoint
     */
    public function usersAlsoBought(
        $tenantKey,
        $itemid,
        $userid = null,
        $numberOfResults = 10,
        $itemtype = null,
        $requesteditemtype = null,
        $withProfile = false
    ) {
        $this->tenantKey = $tenantKey;

        return $this->abstractRecommendationEndpoint('otherusersalsobought', $itemid, $userid, $numberOfResults, $itemtype, $requesteditemtype, $withProfile);
    }

    /**
     * @see abstractRecommendationEndpoint
     */
    public function ratedGoodByOther(
        $tenantKey,
        $itemid,
        $userid = null,
        $numberOfResults = 10,
        $itemtype = null,
        $requesteditemtype = null,
        $withProfile = false
    ) {
        $this->tenantKey = $tenantKey;

        return $this->abstractRecommendationEndpoint('itemsratedgoodbyotherusers', $itemid, $userid, $numberOfResults, $itemtype, $requesteditemtype, $withProfile);
    }

    /**
     * Returns recommendation for a given user ID
     *
     * @param  string  $itemid A required item ID to identify an item on your website. (e.g. "ID001")
     * @param  mixed  $userid A required anonymised id of a user. (e.g. "24EH1723322222A3")
     * @param  int  $numberOfResults An optional parameter to determine the number of results returned. Should be between 1 and 15.
     * @param  string  $requesteditemtype An optional type of an item (e.g. IMAGE, VIDEO, BOOK, etc.) to filter the returned items.If not supplied items of all item types are returned.
     * @param  string  $actiontype Allows to define which actions of a user are considered when creating the personalized recommendation. Valid values are: VIEW, RATE, BUY.
     * @param  bool  $withProfile If this parameter is set to true the result contains an additional element 'profileData' with the item profile.
     * @return array The decoded JSON response
     */
    public function recommendationsForUser(
        $tenantKey,
        $itemid,
        $userid,
        $numberOfResults = 10,
        $requesteditemtype = null,
        $actiontype = 'VIEW',
        $withProfile = false
    ) {
        $this->tenantKey = $tenantKey;

        // Check that $numberOfResults has got the expected format
        if (! is_numeric($numberOfResults) || $numberOfResults < 0) {
            throw new InvalidArgumentException('The number of results should be at least 1.', 1);
        }

        // Can't currently retrieve more than 15 results
        $numberOfResults = min($numberOfResults, 15);

        foreach (['itemid', 'userid', 'numberOfResults', 'requesteditemtype', 'actiontype', 'withProfile'] as $param) {
            $this->setQueryParam($param, $$param);
        }

        // Set the endpoint name and send the request
        $this->setEndpoint('recommendationsforuser');

        return $this->sendRequest();
    }

    /**
     * @see abstractRecommendationEndpoint
     */
    public function relatedItems(
        $tenantKey,
        $itemid,
        $userid = null,
        $numberOfResults = 10,
        $itemtype = null,
        $requesteditemtype = null,
        $withProfile = false
    ) {
        $this->tenantKey = $tenantKey;

        return $this->abstractRecommendationEndpoint('relateditems', $itemid, $userid, $numberOfResults, $itemtype, $requesteditemtype, $withProfile);
    }

    /**
     * Returns the last actions performed by a user
     *
     * @param  mixed  $userid A required anonymised id of a user. (e.g. "24EH1723322222A3")
     * @param  int  $numberOfResults An optional parameter to determine the number of results returned. Should be between 1 and 15.
     * @param  string  $requesteditemtype An optional type of an item (e.g. IMAGE, VIDEO, BOOK, etc.) to filter the returned items.If not supplied items of all item types are returned.
     * @param  string  $actiontype Allows to define which actions of a user are considered when creating the personalized recommendation. Valid values are: VIEW, RATE, BUY.
     * @return array The decoded JSON response
     */
    public function actionHistoryForUser(
        $tenantKey,
        $userid,
        $numberOfResults = 10,
        $requesteditemtype = null,
        $actiontype = null,
        $withProfile = 'true'
    ) {
        $this->tenantKey = $tenantKey;

        // Check that $numberOfResults has got the expected format
        if (! is_numeric($numberOfResults) || $numberOfResults < 0) {
            throw new InvalidArgumentException('The number of results should be at least 1.', 1);
        }

        // Can't currently retrieve more than 15 results
        $numberOfResults = min($numberOfResults, 15);

        foreach (['userid', 'numberOfResults', 'requesteditemtype', 'actiontype', 'withProfile'] as $param) {
            $this->setQueryParam($param, $$param);
        }

        // Set the endpoint name and send the request
        $this->setEndpoint('actionhistoryforuser');

        return $this->sendRequest();
    }

    /*
    * RANKINGS
    * --------------------
    */

    /**
     * Call a community endpoint of the API
     *
     * @param  string  $endpoint The name of the API endpoint
     * @param  int  $numberOfResults An optional parameter to determine the number of results returned. Must be between 1 and 50.
     * @param  string  $timeRange An optional parameter to determine the time range. This parameter may be set to one of the following values: DAY, WEEK, MONTH, ALL.
     * @param  string  $requesteditemtype An optional item type that denotes the type of the item (e.g. IMAGE, VIDEO, BOOK, etc.). If not supplied the default value ITEM will be used.
     * @param  bool  $withProfile If this parameter is set to true the result contains an additional element 'profileData' with the item profile.
     * @return array The JSON decoded response
     *
     * @throws \InvalidArgumentException If timeRange is not in the supported values: DAY, WEEK, MONTH, ALL
     * @throws \InvalidArgumentException If the numberOfResults is negative or is not a number
     */
    private function abstractCommunityEndpoint($endpoint, $numberOfResults = 30, $timeRange = 'ALL', $requesteditemtype = null, $withProfile = false)
    {
        // Check that $numberOfResults has got the expected format
        if (! is_numeric($numberOfResults) || $numberOfResults < 0) {
            throw new InvalidArgumentException('The number of results should be at least 1.', 1);
        }

        // Can't currently retrieve more than 50 results
        $numberOfResults = min($numberOfResults, 50);

        // Check that $timeRange has got the expected format
        if (! in_array($timeRange, ['DAY', 'WEEK', 'MONTH', 'ALL'])) {
            throw new InvalidArgumentException('Invalid value for timeRange. Allowed values are DAY, WEEK, MONTH, ALL.', 1);
        }

        foreach (['numberOfResults', 'timeRange', 'requesteditemtype', 'withProfile'] as $param) {
            $this->setQueryParam($param, $$param);
        }

        // Set the endpoint name and send the request
        $this->setEndpoint($endpoint);

        return $this->sendRequest();
    }

    /**
     * @see abstractCommunityEndpoint
     */
    public function mostViewedItems($tenantKey, $numberOfResults = 30, $timeRange = 'ALL', $requesteditemtype = null, $withProfile = false)
    {
        $this->tenantKey = $tenantKey;

        return $this->abstractCommunityEndpoint('mostvieweditems', $numberOfResults, $timeRange, $requesteditemtype, $withProfile);
    }

    /**
     * @see abstractCommunityEndpoint
     */
    public function mostBoughtItems($tenantKey, $numberOfResults = 30, $timeRange = 'ALL', $requesteditemtype = null, $withProfile = false)
    {
        $this->tenantKey = $tenantKey;

        return $this->abstractCommunityEndpoint('mostboughtitems', $numberOfResults, $timeRange, $requesteditemtype, $withProfile);
    }

    /**
     * @see abstractCommunityEndpoint
     */
    public function mostRatedItems($tenantKey, $numberOfResults = 30, $timeRange = 'ALL', $requesteditemtype = null, $withProfile = false)
    {
        $this->tenantKey = $tenantKey;

        return $this->abstractCommunityEndpoint('mostrateditems', $numberOfResults, $timeRange, $requesteditemtype, $withProfile);
    }

    /**
     * @see abstractCommunityEndpoint
     */
    public function bestRatedItems($tenantKey, $numberOfResults = 30, $timeRange = 'ALL', $requesteditemtype = null, $withProfile = false)
    {
        $this->tenantKey = $tenantKey;

        return $this->abstractCommunityEndpoint('bestrateditems', $numberOfResults, $timeRange, $requesteditemtype, $withProfile);
    }

    /**
     * @see abstractCommunityEndpoint
     */
    public function worstRatedItems($tenantKey, $numberOfResults = 30, $timeRange = 'ALL', $requesteditemtype = null, $withProfile = false)
    {
        $this->tenantKey = $tenantKey;

        return $this->abstractCommunityEndpoint('worstrateditems', $numberOfResults, $timeRange, $requesteditemtype, $withProfile);
    }

    /**
     * Returns the query parameters for the GET request
     *
     * @return array The key value parameters
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }

    /**
     * Set the endpoint name of the API
     *
     * @param  string  $endpoint The endpoint name
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
    }

    /**
     * Returns the endpoint name
     *
     * @return string The endpoint name
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * Returns true if an endpoint list items
     *
     * @return bool
     */
    public function doesEndpointListItems()
    {
        return in_array($this->getEndpoint(), [
            'otherusersalsoviewed',
            'otherusersalsobought',
            'itemsratedgoodbyotherusers',
            'recommendationsforuser',
            'mostvieweditems',
            'mostboughtitems',
            'mostrateditems',
            'bestrateditems',
            'worstrateditems',
            'actionhistoryforuser',
            'relateditems',
        ]);
    }

    /**
     * Set the response given by the API
     *
     * @param  array  $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     * Determine if the response given by the API has got an error
     *
     * @return bool
     */
    public function responseHasError()
    {
        return ! is_null($this->response) && array_key_exists('error', $this->response);
    }

    /**
     * Retrieve only the first response if we had an error in the response
     *
     * @return array An array with key '@code' and '@message' describing the first error
     */
    public function retrieveFirstErrorFromResponse()
    {
        if (! $this->responseHasError()) {
            throw new InvalidArgumentException('Response hasn\'t got an error');
        }

        $errors = $this->response['error'];

        // Multiple errors?
        $error = array_key_exists(0, $errors) ? $errors[0] : $errors;

        return $error;
    }

    /**
     * Send a request to an API endpoint
     *
     * @return array The decoded JSON array
     */
    private function sendRequest($apiOnly = false)
    {
        $endpoint = $this->getEndpoint();
        if (is_null($endpoint)) {
            throw new InvalidArgumentException('Endpoint name was not set.', 1);
        }
        /*
        // Prepare the request
        $request = $this->httpClient->createRequest('GET', $endpoint, ['query' => $this->queryParams]);

        // Send the request
        $response = $this->httpClient->send($request);

        // Parse JSON and returns an array
        $this->setResponse($result = $response->json());
        */

        // Use tenantId from request
        $this->queryParams['tenantid'] = $this->tenantKey;

        if ($apiOnly) {
            $client = new Client([
                'base_uri' => $this->getBaseApiURL(),
            ]);
        } else {
            $client = new Client([
                'base_uri' => $this->getBaseURL(),
            ]);
        }

        try {
            $response = $client->request('GET', $endpoint, [
                'query' => $this->queryParams,
                // 'future' => true
            ]);
            //$result = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
            $result = json_decode($response->getBody(), true);

            // Parse JSON and returns an array
            $this->setResponse($result);

            // Check if we had an error
            if ($this->responseHasError()) {
                $error = $this->retrieveFirstErrorFromResponse();

                throw new EasyrecException($error['@message'], $error['@code']);
            }

            // Add a key to the array with a list of all items' ID
            // Check that we have got the expected array
            // Prevent from iterating over an empty array
            if ($this->doesEndpointListItems() &&
                (! is_null($result) && array_key_exists('recommendeditems', $result)) &&
                (is_array($result['recommendeditems']) && ! empty($result['recommendeditems']))) {
                $ids = [];
                foreach ($result['recommendeditems'] as $items) {
                    foreach ($items as $item) {
                        $ids[] = (int) $item['id'];
                    }
                }
                $result['listids'] = $ids;
            }
        } catch (RequestException $e) {
            $msg = Message::toString($e->getRequest()) . "\n";
            if ($e->hasResponse()) {
                $msg .= Message::toString($e->getResponse()) . "\n";
            }
            Log::error('Error connecting EASYREC', [
                'message' => $msg,
                'params' => $this->queryParams,
            ]);
            $result = '';
        }

        // Reset API key and tenantID. Which ensures other params are removed.
        $this->queryParams = [
            'apikey' => $this->config['apiKey'],
            'tenantid' => $this->tenantKey,
        ];

        return $result;
    }

    /**
     * Send a request to an API endpoint
     *
     * @return array The decoded JSON array
     */
    private function sendPostRequest()
    {
        $endpoint = $this->getEndpoint();
        if (is_null($endpoint)) {
            throw new InvalidArgumentException('Endpoint name was not set.', 1);
        }

        // Use tenantId from request.
        $this->queryParams['tenantid'] = $this->tenantKey;
        $this->queryParams['profile'] = $this->profileData;

        $client = new Client([
            'base_uri' => $this->getBaseURL(),
        ]);

        try {
            $response = $client->request('POST', $endpoint, [
                'json' => $this->queryParams,
                // 'future' => true
            ]);
            $result = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            // Parse JSON and returns an array.
            $this->setResponse($result);

            // Check if we had an error.
            if ($this->responseHasError()) {
                $error = $this->retrieveFirstErrorFromResponse();

                throw new EasyrecException($error['@message'], $error['@code']);
            }

            // Add a key to the array with a list of all items' ID.
            // Check that we have got the expected array
            // Prevent from iterating over an empty array
            if ($this->doesEndpointListItems() &&
                (! is_null($result) && array_key_exists('recommendeditems', $result)) &&
                (is_array($result['recommendeditems']) && ! empty($result['recommendeditems']))) {
                $ids = [];
                foreach ($result['recommendeditems'] as $items) {
                    foreach ($items as $item) {
                        $ids[] = (int) $item['id'];
                    }
                }
                $result['listids'] = $ids;
            }
        } catch (RequestException $e) {
            $msg = Message::toString($e->getRequest()) . "\n";
            if ($e->hasResponse()) {
                $msg .= Message::toString($e->getResponse()) . "\n";
            }
            Log::error('Error connecting EASYREC', [
                'message' => $msg,
                'params' => $this->queryParams,
            ]);
            $result = '';
        }

        // Reset API key and tenantID. Which ensures other params are removed.
        $this->queryParams = [
            'apikey' => $this->config['apiKey'],
            'tenantid' => $this->tenantKey,
        ];

        return $result;
    }

    /**
     * Set a GET parameter
     *
     * @param  string  $key The name of the parameter to set
     * @param  mixed  $value The value
     */
    private function setQueryParam($key, $value): void
    {
        // Do not set value if it was null because it was optional.
        if (! is_null($value)) {
            $this->queryParams[$key] = $value;
        }
    }

    /**
     * @param $tenantKey
     * @param $itemid
     * @param $itemdescription
     * @param $itemurl
     * @param  null  $profileData
     * @param  null  $itemimageurl
     * @param  null  $itemtype
     * @return array
     */
    public function storeWithProfile(
        $tenantKey,
        $itemid,
        $itemdescription,
        $itemurl,
        $profileData = null,
        $itemimageurl = null,
        $itemtype = null
    ) {
        foreach (['itemid', 'itemdescription', 'itemurl', 'itemimageurl', 'itemtype'] as $param) {
            $this->setQueryParam($param, $$param);
        }

        // Set the endpoint name and send the request.
        $this->setEndpoint('profile/storeitemwithprofile');

        $this->tenantKey = $tenantKey;

        $this->setProfileData($profileData);

        // Fix user id
        if (isset($this->queryParams['userid'])) {
            unset($this->queryParams['userid']);
        }

        // Fix session id
        if (isset($this->queryParams['sessionid'])) {
            unset($this->queryParams['sessionid']);
        }

        return $this->sendPostRequest();
    }

    /**
     * @param $profileData
     */
    private function setProfileData($profileData)
    {
        $this->profileData = json_encode($profileData);
    }

    /**
     * @param $tenantKey
     * @param $itemid
     * @param  null  $itemtype
     * @return mixed|string
     *
     * @throws EasyrecException
     */
    public function deleteItem($tenantKey, $itemid, $itemtype = null)
    {
        $this->tenantKey = $tenantKey;

        foreach (['itemid', 'itemtype'] as $param) {
            $this->setQueryParam($param, $$param);
        }

        // Set the endpoint name and send the request.
        $this->setEndpoint('profile/delete');

        return $this->sendDeleteRequest();
    }

    /**
     * @return mixed|string
     *
     * @throws EasyrecException
     */
    private function sendDeleteRequest(): mixed
    {
        $endpoint = $this->getEndpoint();
        if (is_null($endpoint)) {
            throw new InvalidArgumentException('Endpoint name was not set.', 1);
        }

        // Use tenantId from request
        $this->queryParams['tenantid'] = $this->tenantKey;

        $client = new Client([
            'base_uri' => $this->getBaseURL(),
        ]);

        try {
            $response = $client->request('DELETE', $endpoint, [
                'query' => $this->queryParams,
            ]);
            $result = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            // Parse JSON and returns an array.
            $this->setResponse($result);

            // Check if we had an error.
            if ($this->responseHasError()) {
                $error = $this->retrieveFirstErrorFromResponse();

                throw new EasyrecException($error['@message'], $error['@code']);
            }
        } catch (RequestException $e) {
            $msg = Message::toString($e->getRequest()) . "\n";
            if ($e->hasResponse()) {
                $msg .= Message::toString($e->getResponse()) . "\n";
            }
            Log::error('Error connecting EASYREC: ' . $msg);
            $result = '';
        }

        // Reset API key and tenantID. Which ensures other params are removed.
        $this->queryParams = [
            'apikey' => $this->config['apiKey'],
            'tenantid' => $this->tenantKey,
        ];

        return $result;
    }

    /**
     * @param $tenantKey
     * @param $itemid
     * @return mixed|string
     */
    public function setItemStatus($tenantKey, $itemid, $active)
    {
        $this->tenantKey = $tenantKey;

        foreach (['itemid', 'active'] as $param) {
            $this->setQueryParam($param, $$param);
        }

        // Set the endpoint name and send the request.
        $this->setEndpoint('setitemactive');

        return $this->sendRequest(true);
    }
}
