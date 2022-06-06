<?php


use Firebase\JWT\JWT;

if (!function_exists('create_jwt')) {
    /**
     *
     * Create JWT with passed parameter in array
     *
     * @param string|array $value Values that need to be set in the JWT
     *
     * @return mixed|array
     */
    function create_jwt($value)
    {
        $time = time();
        $payload = array(
            "iss" => base_url(),
            "aud" => "user",
            "iat" => $time,
            "exp" => $time + 36000,
            "nbf" => $time,
            "data" => $value
        );

        return JWT::encode($payload, JWT_KEY);
    }

}

if (!function_exists('verify_jwt')) {
    /**
     *
     * Verify the JWT token whether the data is tampered or not
     *
     * @param string $token Token that is got from the user
     *
     * @return object
     */

    function verify_jwt(string $token)
    {
        try {
             return JWT::decode($token, JWT_KEY, array('HS256'));//Decodes a JWT string into a PHP object.
        } catch (\Firebase\JWT\SignatureInvalidException $th) {
            echo $th->getMessage();
            echo "Invalid signature";
            session_destroy();
        }
    }
}

