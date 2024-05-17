<?php

namespace App\Admin\Traits;

use Illuminate\Database\Eloquent\Builder;

trait FormInput
{
    public function withInput(array $input = []): Builder
    {
        return collect($input)->reduce(function(Builder $builder, string $value, string $name) {
            $type = $this->fields[$name] ?? null;

            switch($type) {
                case 'Like': {
                    return $builder->where($name, 'LIKE', $value);
                };
                case 'Date': {
                    $value = str_contains($value, '-') ? strtotime($value) : $value;

                    return $builder->where($name, '=', $value);
                };
                case 'DateRange': {
                    [$begin, , $end] = explode(' ', trim($value));
        
                    $begin = str_contains($begin, '-') ? strtotime($begin) : $begin;
                    $end = str_contains($end, '-') ? strtotime($end) : $end;

                    return $builder->whereBetween($name, [$begin, $end]);
                };
                case 'DateTime': {
                    $value = str_contains($value, '-') || str_contains($value, ':') ? strtotime($value) : $value;

                    return $builder->where($name, '=', $value);
                };
                case 'DateTimeRange': {
                    $block = explode(' ', trim($value));
                    if(count($block) == 3) {
                        [$begin, , $end] = $block;
                    } else {
                        [$beginDate, $beginTime, , $endDate, $endTime] = explode(' ', $value);
                        $begin = strtotime($beginDate . ' ' . $beginTime);
                        $end = strtotime($endDate . ' ' . $endTime);
                    }

                    return $builder->whereBetween($name, [$begin, $end]);
                };
                case 'Time': {
                    $value = str_contains($value, ':') ? strtotime($value) : $value;

                    return $builder->where($name, '=', $value);
                };
                case 'TimeRange': {
                    [$begin, , $end] = explode(' ', trim($value));

                    $begin = str_contains($begin, ':') ? strtotime($begin) : $begin;
                    $end = str_contains($end, ':') ? strtotime($end) : $end;

                    return $builder->whereBetween($name, [$begin, $end]);
                };
                default: {
                    return $builder->where($name, '=', $value);
                };
            }

        }, $this->newQuery());
    }
}
