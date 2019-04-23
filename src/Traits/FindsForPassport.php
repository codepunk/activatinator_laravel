<?php
/**
 * Created by PhpStorm.
 * User: slaterama
 * Date: 4/23/19
 * Time: 1:58 PM
 */

namespace Codepunk\Activatinator\Traits;
use Codepunk\Activatinator\Support\Facades\Activatinator;
use League\OAuth2\Server\Exception\OAuthServerException;

/**
 * Trait FindsForPassport
 * @package App\Http\Traits
 * @mixin \Illuminate\Database\Query\Builder
 */

trait FindsForPassport
{
    /**
     * @param $usernameOrEmail
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     * @throws OAuthServerException
     */
    public function findForPassport($usernameOrEmail)
    {
        $is_email = filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL) != false;
        $user = $is_email ?
            $this->where('email', $usernameOrEmail)->first() :
            $this->where('username', $usernameOrEmail)->first();

        if ($user != null && !$user->active) {
            throw new OAuthServerException(
                trans(Activatinator::INACTIVE),
                6,
                Activatinator::INACTIVE
            );
        }

        return $user;
    }
}
