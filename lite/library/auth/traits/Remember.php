<?php

declare(strict_types=1);

namespace lite\library\auth\traits;

use lite\exception\SheepException;
use lite\library\auth\Recaller;
use think\facade\Cookie;
use think\helper\Str;

/**
 * sessionGuard 记住我
 */
trait Remember
{

    /**
     * Indicates if the user was authenticated via a recaller cookie.
     *
     * @var bool
     */
    protected $viaRemember = false;

    /**
     * Indicates if a token user retrieval has been attempted.
     *
     * @var bool
     */
    protected $recallAttempted = false;

    /**
     * Create a new "remember me" token for the user if one doesn't already exist.
     *
     * @param  \think\Model  $user
     * @return void
     */
    protected function ensureRememberTokenIsSet(\think\Model $user)
    {
        if (empty($user->getRememberToken())) {
            $this->cycleRememberToken($user);
        }
    }

    /**
     * Queue the recaller cookie into the cookie jar.
     *
     * @param  \think\Model  $user
     * @return void
     */
    protected function responseCookie(\think\Model $user)
    {
        $sign = sheep_config('basic.site.sign') ? : 'sheep';
        // 默认记住我 15 天
        Cookie::set($this->getRecallerName(), openssl_encrypt($user->getAuthIdentifier() . '|' . $user->getRememberToken() . '|', 'DES-ECB', $sign), (86400 * 15));
    }



    /**
     * Get the decrypted recaller cookie for the request.
     *
     * @return \Illuminate\Auth\Recaller|null
     */
    protected function recaller()
    {
        if ($recaller = Cookie::get($this->getRecallerName())) {
            $sign = sheep_config('basic.site.sign') ?: 'sheep';
            $recaller = openssl_decrypt($recaller, 'DES-ECB', $sign);

            return new Recaller($recaller);
        }
    }


    /**
     * Pull a user from the repository by its "remember me" cookie token.
     *
     * @param  Recaller  $recaller
     * @return mixed
     */
    protected function userFromRecaller($recaller)
    {
        if (!$recaller->valid() || $this->recallAttempted) {
            return;
        }

        // If the user is null, but we decrypt a "recaller" cookie we can attempt to
        // pull the user data on that cookie which serves as a remember cookie on
        // the application. Once we have a user we can return it to the caller.
        $this->recallAttempted = true;

        $this->viaRemember = !is_null($user = $this->retrieveByToken(
            $recaller->id(),
            $recaller->token()
        ));

        return $user;
    }


    /**
     * Refresh the "remember me" token for the user.
     *
     * @param  \think\Model  $user
     * @return void
     */
    protected function cycleRememberToken(\think\Model $user)
    {
        $user->setRememberToken($token = Str::random(60));

        $user->save();
    }


    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed  $identifier
     * @param  string  $token
     * @return \think\Model|null
     */
    public function retrieveByToken($identifier, $token)
    {
        $retrievedModel = $this->model->where(
            $this->model->getAuthIdentifierName(),
            $identifier
        )->find();

        if (!$retrievedModel) {
            return;
        }

        $rememberToken = $retrievedModel->getRememberToken();

        return $rememberToken && hash_equals($rememberToken, $token)
            ? $retrievedModel : null;
    }



    /**
     * Determine if the user was authenticated via "remember me" cookie.
     *
     * @return bool
     */
    public function viaRemember()
    {
        return $this->viaRemember;
    }


    /**
     * Get the name of the cookie used to store the "recaller".
     *
     * @return string
     */
    public function getRecallerName()
    {
        return 'remember_' . $this->guard . '_' . sha1(static::class);
    }
}
