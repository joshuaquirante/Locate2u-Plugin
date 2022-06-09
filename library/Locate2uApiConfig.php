<?php

class Locate2uApiConnection 
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
        }else{
            header("HTTP/1.1 401 Unauthorized");
            exit;
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
    public static function generateAccessToken( $client_id, $client_secret ) 
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

                if( $response && isset( $response->error ) ){
                    return self::errorHandler( $response->error );
                }
            }

        } catch ( Exception $e ) {

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

	
}
