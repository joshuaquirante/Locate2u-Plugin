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
        $content = "grant_type=client_credentials&scope=locate2u.api";
        $url = self::${self::$environment . '_access_token_url'};

        try {
            $curl = curl_init( $url );
            curl_setopt( $curl, CURLOPT_URL, $url );
            curl_setopt( $curl, CURLOPT_HEADER, false );
            curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );

            $headers = array(
                "Content-Type: application/x-www-form-urlencoded",
                "Authorization: Basic " . base64_encode( "$client_id:$client_secret")
            );

            curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );

            if( $content ){
                curl_setopt( $curl, CURLOPT_POSTFIELDS, $content );
            }

            $curl_response = curl_exec( $curl );
           
            // if( curl_errno( $curl )  ){
            //     \Log::error( 'locate2u api', __FUNCTION__ . ' failed.', false, true );
            //     \Log::error( 'curl', curl_error( $curl ), false, true );
            //     \Log::debug( 'locate2u api', $url, true );
            // }

            curl_close( $curl );

            $response = json_decode( $curl_response );

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
