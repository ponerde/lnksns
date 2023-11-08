<?php

declare(strict_types=1);

namespace lite\library\ratelimiter;

use lite\library\ratelimiter\traits\InteractsWithTime;
use lite\facade\Cache;

class RateLimiter
{
    use InteractsWithTime;

    /**
     * 调用者实例
     */
    protected $callInstance;

    /**
     * 緩存
     */
    protected $cache;


    public function __construct($callInstance = null)
    {
        $this->callInstance = $callInstance;

        $this->cache = cache()->store('file');
    }

    /**
     * 确定给定 key 是否被“访问”过多次.
     *
     * @param  string  $key
     * @param  int  $maxAttempts
     * @param  float|int  $decaySeconds
     * @return bool
     */
    public function tooManyAttempts($key, $maxAttempts, $decaySeconds = 3600)
    {
        if ($this->attempts($key) >= $maxAttempts) {
            if ($this->cache->has($key . ':timer')) {
                return true;
            }

            $this->resetAttempts($key);
        }

        return false;
    }

    /**
     * 在给定的衰减时间内增加给定键的计数器.
     *
     * @param  string  $key
     * @param  float|int  $decaySeconds
     * @return int
     */
    public function hit($key, $decaySeconds = 3600)
    {
        $this->cache->set(
            $key . ':timer',
            $this->availableAt($decaySeconds),
            $decaySeconds
        );

        if (!$this->cache->has($key)) {
            $this->cache->set($key, 0, $decaySeconds);
        }

        $hits = (int) $this->cache->inc($key);
        
        return $hits;
    }

    /**
     * 获取 key 的尝试次数.
     *
     * @param  string  $key
     * @return mixed
     */
    public function attempts($key)
    {
        return $this->cache->get($key, 0);
    }

    /**
     * 重置 key 的尝试次数.
     *
     * @param  string  $key
     * @return mixed
     */
    public function resetAttempts($key)
    {
        return $this->cache->delete($key);
    }

    /**
     * 获取 key 的剩余重试次数.
     *
     * @param  string  $key
     * @param  int  $maxAttempts
     * @return int
     */
    public function retriesLeft($key, $maxAttempts)
    {
        $attempts = $this->attempts($key);

        return $maxAttempts - $attempts;
    }

    /**
     * 清除 key 的尝试次数和锁定计时器.
     *
     * @param  string  $key
     * @return void
     */
    public function clear($key)
    {
        $this->resetAttempts($key);

        $this->cache->delete($key . ':timer');
    }

    /**
     * 获取被锁定剩余时间.
     *
     * @param  string  $key
     * @return int
     */
    public function availableIn($key)
    {
        return $this->cache->get($key . ':timer') - $this->currentTime();
    }
}
