<?php

include_once('HttpHelper.php');

/**
	*	PayPal helper class for REST API requests.
	*	
*/

class PayPalHelper {
	
	private $_http = null;
	private $_apiUrl = null;
	private $_token = null;
	
	/**
		* 	Class constructor.
		*	
	*/
	public function __construct() {
		$this->_http = new HttpHelper;
		$this->_apiUrl = PAYPAL_ENDPOINTS[PAYPAL_ENVIRONMENT];
	}
	
	/**
		* 	Set PayPal default header for the curl instance.
		*
		* 	@return void
	*/
	
	
	/**
		* 	Create the PayPal REST endpoint url.
		*
		*	Use the configurations and combine resources to create the endpoint.
		*
        *	@param string $resource Url to be called using curl
		* 	@return string REST API url depending on environment.
	*/
	private function _createApiUrl($resource) {
		if($resource == 'oauth2/token')
			return $this->_apiUrl . "/v1/" . $resource;
		else
			return $this->_apiUrl . "/" . PAYPAL_REST_VERSION . "/" . $resource;
	}
	
	/**
		* 	Request for PayPal REST oath bearer token.
		*	
		* 	Reset curl helper. 
		*	Set default PayPal headers.
		*	Set curl url.
		*	Set curl credentials.
		*	Set curl body.
		*	Set class token attribute with bearer token.
		*
		* 	@return void
	*/
	private function _getToken() {
		$this->_http->resetHelper();
		$this->_http->setUrl($this->_createApiUrl("oauth2/token"));
		$this->_http->setAuthentication(PAYPAL_CREDENTIALS[PAYPAL_ENVIRONMENT]['client_id'] . ":" . PAYPAL_CREDENTIALS[PAYPAL_ENVIRONMENT]['client_secret']);
		$this->_http->setBody("grant_type=client_credentials");
		$returnData = $this->_http->sendRequest();
		$this->_token = $returnData['access_token'];	
	}
/**
		* 	Request for PayPal REST client token.
		*	Reset curl helper.
    	 *	Set default PayPal headers.
     	* 	Set API call specific headers.
    	 *	Set curl url.
		* 
		*	@param array $postData Url to be called using curl
		* 	@return array PayPal REST client token response
		* 	
	*/

	private function _getClientToken(){
		$this->_http->resetHelper();
		$this->_http->addHeader("Content-Type: application/json");
		$this->_http->addHeader("Authorization: Bearer " . $this->_token);
		$this->_http->setMethod('POST');
		// $this->_http->setUrl($this->_createApiUrl("identity/generate-tokenuijhghlj"));
		$this->_http->setUrl($this->_apiUrl . "/v1/" . "identity/generate-token");
		return $this->_http->sendRequest();
		//return $returnData;	
	}

	// public function test(){
		
	// 	return $this->_apiUrl . "/v1/" . "identity/generate-token";
	// }
	
	/**
		* 	Actual call to curl helper to create an order using PayPal REST APIs.
		*	
		* 	Reset curl helper.
		*	Set default PayPal headers.
		* 	Set API call specific headers.
		*	Set curl url.
		*	Set curl body.
		*
        *	@param array $postData Url to be called using curl
		* 	@return array PayPal REST create response
	*/
	private function _createOrder($postData) {
		$this->_http->resetHelper();
		$this->_http->addHeader("Content-Type: application/json");
		$this->_http->addHeader("Authorization: Bearer " . $this->_token);
		$this->_http->setUrl($this->_createApiUrl("checkout/orders"));
		$this->_http->setBody($postData);
		return $this->_http->sendRequest(); 
	}

    /**
     * 	Actual call to curl helper to get a payment using PayPal REST APIs.
     *
     * 	Reset curl helper.
     *	Set default PayPal headers.
     * 	Set API call specific headers.
     *	Set curl url.
     *
     * 	@param array $postData Url to be called using curl
     * 	@return array PayPal REST execute response
     */
    private function _getOrderDetails() {
        $this->_http->resetHelper();
        $this->_http->addHeader("Content-Type: application/json");
        $this->_http->addHeader("Authorization: Bearer " . $this->_token);
        $this->_http->setUrl($this->_createApiUrl("checkout/orders/" . $_SESSION['order_id']));
        return $this->_http->sendRequest();
	}
	
	/**
		* 	Actual call to curl helper to execute a payment using PayPal REST APIs.
		*	
		* 	Reset curl helper.
		*	Set default PayPal headers.
		* 	Set API call specific headers.
		*	Set curl url.
		*	Set curl body.
		*
        *	@param array $postData Url to be called using curl
		* 	@return array PayPal REST execute response
	*/
	private function _patchOrder($postData) {
		$this->_http->resetHelper();
		$this->_http->addHeader("Content-Type: application/json");
		$this->_http->addHeader("Authorization: Bearer " . $this->_token);
		$this->_http->setUrl($this->_createApiUrl("checkout/orders/" . $_SESSION['order_id']));
		$this->_http->setPatchBody($postData);
		return $this->_http->sendRequest(); 
	}
	
	/**
		* 	Actual call to curl helper to capture an order using PayPal REST APIs.
		*	
		* 	Reset curl helper.
		*	Set default PayPal headers.
		* 	Set API call specific headers.
		*	Set curl url.
		*	Set curl body.
		*
        *	@param array $postData Url to be called using curl
		* 	@return array PayPal REST capture response
	*/
	private function _captureOrder($data) {
		$this->_http->resetHelper();
		$this->_http->addHeader("Content-Type: application/json");
		$this->_http->addHeader("Authorization: Bearer " . $this->_token);
		$this->_http->setUrl($this->_createApiUrl("checkout/orders/" . $data . "/capture"));
		$postData='{}';
		$this->_http->setBody($postData);
		//unset($_SESSION['order_id']);
		return $this->_http->sendRequest(); 
	}

	private function _verification($data){
		$this->_http->resetHelper();
		$this->_http->addHeader("Content-Type: application/json");
		$this->_http->addHeader("Authorization: Bearer " . $this->_token);
		$this->_http->setMethod('GET');
		$this->_http->setUrl($this->_createApiUrl("checkout/orders/" . $data . "?fields=payment_source"));
		$postData='{}';
		$this->_http->setBody($postData);
		//unset($_SESSION['order_id']);
		return $this->_http->sendRequest();

	}
	
	/**
		* 	Call private order create class function to forward curl request to helper.
		*	
		* 	Check for bearer token.
		*	Call internal REST create order function.
		*
        *	@param array $postData Url to be called using curl
		* 	@return array Formatted API response
	*/
	
	public function orderCreate($postData) {
		if($this->_token === null) {
			$this->_getToken();
		}
		$returnData = $this->_createOrder($postData);
		$_SESSION['order_id'] = $returnData['id'];
		return array(
			"ack" => true,
			"data" => array(
				"id" => $returnData['id']
			)
		);
	}


	public function getClientToken(){
		if($this->_token === null) {
			$this->_getToken();
		}
		$returnData = $this->_getClientToken();
		return $returnData['client_token'];

	}

    /**
     * 	Call private payment get class function to forward curl request to helper.
     *
     * 	Check for bearer token.
     *	Call internal REST get order details function.
     *
     *  @param array $postData Url to be called using curl
     * 	@return array Formatted API response
     */
    public function orderGet() {
        if($this->_token === null) {
            $this->_getToken();
        }
        $returnData = $this->_getOrderDetails();
        return array(
            "ack" => true,
            "data" => $returnData
        );
	}
	
	/**
		* 	Call private patch order class function to forward curl request to helper.
		*	
		* 	Check for bearer token.
		*	Call internal REST patch order function.
		*
        *   @param array $postData Url to be called using curl
		* 	@return array Formatted API response
	*/
	public function orderPatch($postData) {
		if($this->_token === null) {
			$this->_getToken();
		}
		$returnData = $this->_patchOrder($postData);
		return array(
			"ack" => true,
			"data" => $returnData
		);
	}
	
	/**
		* 	Call private capture order class function to forward curl request to helper.
		*	
		* 	Check for bearer token.
		*	Call internal REST capture order function.
		*
        *   @param array $postData Url to be called using curl
		* 	@return array Formatted API response
	*/
	public function orderCapture($data) {
		if($this->_token === null) {
			$this->_getToken();
		}
		$returnData = $this->_captureOrder($data);
		//var_dump($returnData);
		return $returnData;
	}

	public function verification($data){
		if($this->_token === null) {
			$this->_getToken();
		}
		$orderid=$data;
		$returnData = $this->_verification($data);
		// $detail=$returnData["payment_source"]["card"]["authentication_result"];
		if($returnData){
			$captureData=$this->orderCapture($orderid);
			// $detail= var_dump($captureData);
			//$detail=gettype($captureData);
		}else{
			$captureData=" verifcation return undefined";
		}
		// return $detail ;
		// var_dump($returnData);
		return array(
			"verification" => $returnData,
			"capture" => $captureData

		);
	}
	
}