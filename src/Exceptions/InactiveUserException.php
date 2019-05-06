<?php
/**
 * Created by PhpStorm.
 * User: slaterama
 * Date: 5/6/19
 * Time: 1:44 PM
 */

namespace Codepunk\Activatinator\Exceptions;

use League\OAuth2\Server\Exception\OAuthServerException;
use Throwable;

class InactiveUserException extends OAuthServerException
{

    /**
     * @var null|string
     */
    private $email;

    /**
     * Throw a new exception.
     *
     * @param string $message Error message
     * @param int $code Error code
     * @param string $errorType Error type
     * @param int $httpStatusCode HTTP status code to send (default = 400)
     * @param null|string $email The email of the user
     * @param null|string $hint A helper hint
     * @param null|string $redirectUri A HTTP URI to redirect the user back to
     * @param Throwable $previous Previous exception
     */
    public function __construct(
        $message,
        $code,
        $errorType,
        $httpStatusCode = 400,
        $email = null,
        $hint = null,
        $redirectUri = null,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $errorType, $httpStatusCode, $hint, $redirectUri, $previous);
        $this->email = $email;
    }

    /**
     * Returns the current payload.
     *
     * @return array
     */
    public function getPayload()
    {
        $payload = parent::getPayload();
        if ($this->email != null) {
            $payload['email'] = $this->email;
        }
        return $payload;
    }

}
