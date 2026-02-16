<?php
namespace Core;
/**
 * Class Response
 *
 * Defines common HTTP response status codes as class constants.
 *
 * Constants:
 * - NOT_FOUND (404): Resource not found.
 * - FORBIDDEN (403): Access to the resource is forbidden.
 * - UNAUTHORIZED (401): Authentication is required and has failed or has not yet been provided.
 * - BAD_REQUEST (400): The request could not be understood or was missing required parameters.
 */

class Response{
const NOT_FOUND =404;
const FORBIDDEN = 403;
const UNAUTHORIZED = 401;
const BAD_REQUEST = 400;
}

