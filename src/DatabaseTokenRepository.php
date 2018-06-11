<?php

namespace Codepunk\Activatinator;

use Carbon\Carbon;
use Codepunk\Activatinator\Contracts\TokenRepositoryInterface;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Codepunk\Activatinator\Contracts\Activable as ActivableContract;
use Illuminate\Support\Str;

class DatabaseTokenRepository implements TokenRepositoryInterface
{
    const USER_ID = 'user_id';

    const TOKEN = 'token';

    /**
     * The database connection instance.
     *
     * @var \Illuminate\Database\ConnectionInterface
     */
    protected $connection;

    /**
     * The Hasher implementation.
     *
     * @var \Illuminate\Contracts\Hashing\Hasher
     */
    protected $hasher;

    /**
     * The token database table.
     *
     * @var string
     */
    protected $table;

    /**
     * The hashing key.
     *
     * @var string
     */
    protected $hashKey;

    /**
     * The number of seconds a token should last.
     *
     * @var int
     */
    protected $expires;

    /**
     * Create a new token repository instance.
     *
     * @param  \Illuminate\Database\ConnectionInterface  $connection
     * @param  \Illuminate\Contracts\Hashing\Hasher  $hasher
     * @param  string  $table
     * @param  string  $hashKey
     * @param  int  $expires
     * @return void
     */
    public function __construct(ConnectionInterface $connection, HasherContract $hasher,
                                $table, $hashKey, $expires = 60)
    {
        $this->table = $table;
        $this->hasher = $hasher;
        $this->hashKey = $hashKey;
        $this->expires = $expires * 60;
        $this->connection = $connection;
    }

    /**
     * Create a new token record.
     *
     * @param  \Codepunk\Activatinator\Contracts\Activable  $user
     * @return string
     */
    public function create(ActivableContract $user)
    {
        $id = $user->getIdForActivation();

        $this->deleteExisting($user);

        // We will create a new, random token for the user so that we can e-mail them
        // a safe link to the password reset form. Then we will insert a record in
        // the database so that we can verify the token within the actual reset.
        $token = $this->createNewToken();

        $this->getTable()->insert($this->getPayload($id, $token));

        return $token;
    }

    /**
     * Delete all existing reset tokens from the database.
     *
     * @param  \Codepunk\Activatinator\Contracts\Activable  $user
     * @return int
     */
    protected function deleteExisting(ActivableContract $user)
    {
        return $this->getTable()->where(static::USER_ID, $user->getIdForActivation())->delete();
    }

    /**
     * Build the record payload for the table.
     *
     * @param  int  $id
     * @param  string  $token
     * @return array
     */
    protected function getPayload($id, $token)
    {
        return [static::USER_ID => $id, static::TOKEN => $this->hasher->make($token), 'created_at' => new Carbon];
    }

    /**
     * Determine if a token record exists and is valid.
     *
     * @param  \Codepunk\Activatinator\Contracts\Activable  $user
     * @param  string  $token
     * @return bool
     */
    public function exists(ActivableContract $user, $token)
    {
        $record = (array)$this->getTable()->where(
            static::USER_ID, $user->getIdForActivation()
        )->first();

        return $record &&
            ! $this->tokenExpired($record['created_at']) &&
            $this->hasher->check($token, $record[static::TOKEN]);
    }

    /**
     * Determine if the token has expired.
     *
     * @param  string  $createdAt
     * @return bool
     */
    protected function tokenExpired($createdAt)
    {
        return Carbon::parse($createdAt)->addSeconds($this->expires)->isPast();
    }

    /**
     * Delete a token record by user.
     *
     * @param  \Codepunk\Activatinator\Contracts\Activable  $user
     * @return void
     */
    public function delete(ActivableContract $user)
    {
        $this->deleteExisting($user);
    }

    /**
     * Delete expired tokens.
     *
     * @return void
     */
    public function deleteExpired()
    {
        $expiredAt = Carbon::now()->subSeconds($this->expires);

        $this->getTable()->where('created_at', '<', $expiredAt)->delete();
    }

    /**
     * Retrieve a user id by their unique activation token. Note that this will also return
     * the user id for expired tokens.
     *
     * @param  string  $token
     * @return string|null
     */
    public function retrieveUserIdByToken($token) {
        $record = (array) $this->getTable()->where(
            static::TOKEN, $token
        )->first();

        return ($record ? $record[static::USER_ID] : null);
    }

    /**
     * Create a new token for the user.
     *
     * @return string
     */
    public function createNewToken()
    {
        return hash_hmac('sha256', Str::random(40), $this->hashKey);
    }

    /**
     * Get the database connection instance.
     *
     * @return \Illuminate\Database\ConnectionInterface
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Begin a new database query against the table.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getTable()
    {
        return $this->connection->table($this->table);
    }

    /**
     * Get the hasher instance.
     *
     * @return \Illuminate\Contracts\Hashing\Hasher
     */
    public function getHasher()
    {
        return $this->hasher;
    }
}
