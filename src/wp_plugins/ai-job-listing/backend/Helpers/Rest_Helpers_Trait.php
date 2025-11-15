<?php
namespace Axilweb\AiJobListing\Helpers;
use WP_Error;
use WP_REST_Response;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

trait Rest_Helpers_Trait
{

  /**
   * Creates a standardized error response for REST API endpoints.
   *
   * This function accepts dynamic arguments for error codes, messages, and data,
   * similar to how WP_Error's add() method works. It ensures the error is returned
   * in a proper REST response format.
   *
   * @return WP_REST_Response REST response object encapsulating the WP_Error.
   */
  public static function rest_error()
  {
    $error = new WP_Error();
    $error->add(...func_get_args());
    return rest_ensure_response($error);
  }
  /**
   * Creates a standardized REST API response.
   *
   * This function generates a proper REST API response using the `WP_REST_Response` class. 
   * It allows passing dynamic data, status codes, and additional headers to ensure that 
   * the response is returned in a proper format.
   *
   * The response will be wrapped in `rest_ensure_response()` to ensure that the response 
   * is correctly formatted for the REST API, regardless of the data type.
   *
   * @since 1.0.0
   *
   * @param mixed  $data    The data to be included in the response (default is null).
   * @param int    $status  The HTTP status code for the response (default is 200).
   * @param array  $headers Optional headers to include in the response (default is an empty array).
   *
   * @return WP_REST_Response The formatted REST API response object.
   */
  public static function rest_response($data = null, $status = 200, $headers = [])
  {
    return rest_ensure_response(
      new WP_REST_Response($data, $status, $headers)
    );
  }
}
