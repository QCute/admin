<?php

namespace App\Admin\Controllers\Extend;

use App\Admin\Controllers\Controller;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Support\LazyCollection;

class StatisticController extends Controller
{
    public function getBeginTime(Request $request): int
    {
        $begin = date('Y-m-d', time());
        $end = date('Y-m-d', time() + 86400);
        $date = $request->input('date', $begin . ' - ' . $end);
        [$begin, , ] = explode(' ', trim($date));
        return strtotime($begin);
    }

    public function getEndTime(Request $request): int
    {
        $begin = date('Y-m-d', time());
        $end = date('Y-m-d', time() + 86400);
        $date = $request->input('date', $begin . ' - ' . $end);
        [, , $end] = explode(' ', trim($date));
        return strtotime($end);
    }

    public function getCategory(Request $request, string $format = 'Y-m-d', int $step = 86400): Arrayable
    {
        return LazyCollection::make(function() use ($request, $format, $step) {

            $begin = $this->getBeginTime($request);
            $end = $this->getEndTime($request);
    
            for ($start = $begin; $start <= $end; $start += $step) {
                yield date($format, $start);
            }

        });
    }

    public function getValue(Request $request,  int $step = 86400): array|Arrayable
    {
        return LazyCollection::make(function() use ($request, $step) {

            $begin = $this->getBeginTime($request);
            $end = $this->getEndTime($request);
    
            for ($start = $begin; $start <= $end; $start += $step) {
                yield new class() {

                    /**
                     * Dynamically retrieve attributes on the model.
                     *
                     * @param  string  $key
                     * @return mixed
                     */
                    public function __get($key)
                    {
                        return 0;
                    }

                    /**
                     * Dynamically set attributes on the model.
                     *
                     * @param  string  $key
                     * @param  mixed  $value
                     * @return void
                     */
                    public function __set($key, $value)
                    {
                        
                    }

                    /**
                     * Determine if the given attribute exists.
                     *
                     * @param  mixed  $offset
                     * @return bool
                     */
                    public function offsetExists($offset): bool
                    {
                        return true;
                    }

                    /**
                     * Get the value for a given offset.
                     *
                     * @param  mixed  $offset
                     * @return mixed
                     */
                    public function offsetGet($offset): mixed
                    {
                        return 0;
                    }

                    /**
                     * Set the value for a given offset.
                     *
                     * @param  mixed  $offset
                     * @param  mixed  $value
                     * @return void
                     */
                    public function offsetSet($offset, $value): void
                    {
                        
                    }

                    /**
                     * Unset the value for a given offset.
                     *
                     * @param  mixed  $offset
                     * @return void
                     */
                    public function offsetUnset($offset): void
                    {
                        
                    }

                    /**
                     * Determine if an attribute or relation exists on the model.
                     *
                     * @param  string  $key
                     * @return bool
                     */
                    public function __isset($key)
                    {
                        return true;
                    }

                    /**
                     * Unset an attribute on the model.
                     *
                     * @param  string  $key
                     * @return void
                     */
                    public function __unset($key)
                    {
                        
                    }

                };
            }

        });
    }
}
