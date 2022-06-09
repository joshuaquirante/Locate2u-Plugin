<?php

class Locate2uApi 
{
    const API_V1 = "v1/";

    /** TESTING */
    protected static $test_api_url = "https://api-test.locate2u.com/api/";
    protected static $test_access_token_url = "https://id-test.locate2u.com/connect/token";

    /** PRODUCTION */
    protected static $prod_api_url = "https://api.locate2u.com/api/";
    protected static $prod_access_token_url = "https://id.locate2u.com/connect/token";

    protected static $token;

    protected static $environment; // test | prod

	public function __construct( $client_id, $client_secret, $enableTestMode = true )
    {
        $environment = 'test'; // Testing Environment by default

        if( ! $enableTestMode ){
            $environment = 'prod'; // Production Environment
        }

        self::$environment = $environment; // Set Environment

        $token = $this->getToken( $client_id, $client_secret );

        if( $token ){
            $this->setToken( $token );
        }
	}

    /**
     * Get Access Token
     */
    private function getToken( $client_id, $client_secret )
    {
        $response = self::generateAccessToken( $client_id, $client_secret );

        if( $response && ( isset( $response->success ) && ! $response->success ) ){
            // throw new \Exception( strtoupper( $response->message ) );
            return false;
        }

        return $response;
    }

    /**
     * Set Access Token
     */
    private function setToken( $token )
    {
        self::$token = "Bearer " . $token;
    }

    /**
     * Generate Access Token - OAuth 2
     * 
     * @param string $client_id
     * @param string $client_secret
     * 
     * @return string Access Token 
     */
    private static function generateAccessToken( $client_id, $client_secret ) 
    {
        
        $url = self::${self::$environment . '_access_token_url'};

        try {
            $args = array(
                'body' => 'grant_type=client_credentials&scope=locate2u.api',
                'headers' => array(
                  'Content-Type' => 'application/x-www-form-urlencoded',
                  'Authorization' => 'Basic ' . base64_encode( $client_id . ':' . $client_secret ),
                )
            );  

            $curl_response = wp_remote_post($url, $args);            

            $response = json_decode( wp_remote_retrieve_body( $curl_response ) );

            if( $response && ( isset( $response->access_token ) && $response->access_token ) ){
                return $response->access_token;
                
            }else{
                // \Log::error( 'api', ( __FUNCTION__ . ' failed.' . "\n" . 'No Access Token. Response: ' . json_encode( $response ) ), false, true );
                // \Log::debug( 'api', $url, true );

                if( $response && isset( $response->error ) ){
                    return self::errorHandler( $response->error );
                }
            }

        } catch ( Exception $e ) {
            // \Log::error( 'locate2u api', ( __FUNCTION__ . ' failed.' . "\n" . $e->getMessage() ), false, true );
            // \Log::debug( 'locate2u api', $url, true );

            return self::errorHandler( $e->getMessage() );
        }
    }

    /**
	 * @param string $path Controller | Controller/Action
	 * @param array $params
     * 
	 * @return object $response
	 */
	private static function connect( $path, $params, $update_data = false ) 
    {
        $url = self::${self::$environment . '_api_url' } . self::API_V1 . $path;

        try {

            $args = array(
                'method' => $update_data ? 'PUT' : NULL,
                'headers' => array(
                  'Content-Type' => 'application/json',
                  'Authorization' => self::$token,
                ),
                'body' => $params ? json_encode( $params ) : NULL,
            );
    

            $curl_response = wp_remote_post($url, $args);            

            $response = json_decode( wp_remote_retrieve_body( $curl_response ) );         


            if( $response ){
                
                if( isset( $response[0]->errorCode ) || isset( $response[0]->errors ) ){
                    // \Log::error( 'locate2u api',  json_encode( $response ), false, true );
					
                    return $response[0];
				}else{
					return $response;
				}
            }else{
                // \Log::error( 'locate2u api', ( __FUNCTION__ . ' failed.' . "\n" . 'No Response. ' . $response ), false, true );
                // \Log::debug( 'locate2u api', $url, true );

                return self::errorHandler( "No response." );
            }
        } catch ( Exception $e ) {
            // \Log::error( 'locate2u api', ( __FUNCTION__ . ' failed.' . "\n" . $e->getMessage() ), false, true );
            // \Log::debug( 'locate2u api', $url, true );

            return self::errorHandler( $e->getMessage() );
        }
	}

    /**
     * @return object Error
     */
    private static function errorHandler( $message )
    {
        return (object) array(
            "success" => false,
            "message" => $message
        );
    }

	/**
     * Create Record
     * 
	 * @param string $controller Controller name
     * @param array $params Array of data
     * 
	 * @return json
	 */
	private static function create( $controller, $params ){
        /** Additional Code/Validation here */

		return self::connect( $controller, $params );
	}

    /**
     * Get Data
     * 
	 * @param string $controller Controller name
     * @param array $params Array of data
     * 
	 * @return json
	 */
    private static function retrieve( $controller ){
        /** Additional Code/Validation here */

		return self::connect( $controller );
	}

    /**
     * Update Data
     * 
	 * @param string $controller Controller name
     * @param array $params Array of data
     * 
	 * @return json
	 */
    private function update( $controller ){
        /** Additional Code/Validation here */

		return self::connect( $controller );
	}

    /**
     * Delete Data
     * 
	 * @param string $controller Controller name
     * @param array $params Array of data
     * 
	 * @return json
	 */
    private function delete( $controller ){
        /** Additional Code/Validation here */

		return self::connect( $controller );
	}


    /**
     * Create Stop
     * 
     * @param array $params Array of data
     * 
     * @return json
	 */
    public static function createStop( $data )
    {
        return self::create( 'stops', $data );
    }

    /**
     * Import Stop
     * 
     * @param array $params Array of data
     * 
     * @return json
	 */
    public static function importStop( $data )
    {
        return self::create( 'stops/import', $data );
    }

    /**
     * Create Shipment
     * 
     * @param array $params Array of data
     * 
     * @return json
	 */
    public static function createShipment( $data )
    {
        return self::create( 'shipments', $data );
    }

    /**
     * Import Shipment
     * 
     * @param array $params Array of data
     * 
     * @return json
	 */
    public static function importShipment( $data )
    {
        return self::create( 'shipments/import', $data );
    }
}
