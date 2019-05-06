<?php
/**
 * Created by PhpStorm.
 * User: slaterama
 * Date: 4/23/19
 * Time: 1:58 PM
 */

namespace Codepunk\Activatinator\Traits;

use Codepunk\Activatinator\Exceptions\InactiveUserException;
use Codepunk\Activatinator\Support\Facades\Activatinator;
use Illuminate\Support\Facades\Hash;

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

    // Rename FindsForPassport to AuthenticatesForPassport??
    /**
     * @param $password
     * @return bool
     * @throws InactiveUserException
     */
    public function validateForPassportPasswordGrant($password)
    {
        $check = Hash::check($password, $this->getAuthPassword());
        if (!$check) {
            return $check;
        } else if ($this->active) {
            return true;
        } else {
            throw new InactiveUserException(
                trans(Activatinator::INACTIVE),
                9,
                Activatinator::INACTIVE,
                401,
                $this->email
            );
        }
    }
}
