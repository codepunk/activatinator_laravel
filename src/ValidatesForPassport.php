<?php
/**
 * Created by PhpStorm.
 * User: slaterama
 * Date: 4/23/19
 * Time: 1:58 PM
 */

namespace Codepunk\Activatinator;

use Codepunk\Activatinator\Support\Facades\Activatinator;
use Illuminate\Support\Facades\Hash;
use League\OAuth2\Server\Exception\OAuthServerException;

/**
 * Trait ValidatesForPassport
 * @package App\Http\Traits
 * @mixin \Illuminate\Database\Query\Builder
 */
trait ValidatesForPassport
{
    /**
     * @param $usernameOrEmail
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     */
    public function findForPassport($usernameOrEmail)
    {
        $is_email = filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL) != false;
        $column = $is_email ? 'email' : 'username';
        return $this->where($column, $usernameOrEmail)->first();
    }

    /**
     * @param $password
     * @return bool
     * @throws OAuthServerException
     */
    public function validateForPassportPasswordGrant($password)
    {
        $check = Hash::check($password, $this->getAuthPassword());
        if (!$check) {
            return $check;
        } else if ($this->active) {
            return true;
        } else {
            throw new OAuthServerException(
                trans(Activatinator::INACTIVE_USER),
                9,
                Activatinator::INACTIVE_USER,
                401,
                $this->email
            );
        }
    }
}
