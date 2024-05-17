<?php

namespace App\Admin\Traits;

use App\Admin\Builders\Form;
use App\Admin\Models\Model;
use Illuminate\Contracts\Support\Arrayable;

trait FormOutput
{
    public function buildIndex(Form $form, array|Arrayable $columns): Form
    {
        foreach($columns as $item) {

            $item = (object)$item;

            if($item->name == Model::DELETED_AT) {

            } else if($item->name == 'permission') {

            } else if(str_contains($item->name, 'time') && !str_contains($item->name, 'times')) {
                $form
                    ->dateTimeRange($item->name)
                    ->label($item->comment ?? $item->name);
            } else {
                $form
                    ->text($item->name)
                    ->label($item->comment ?? $item->name);
            }
        }

        return $form;
    }

    public function buildShow(Form $form, array|Arrayable $columns, object $data): Form
    {
        foreach($columns as $item) {

            $item = (object)$item;

            if($item->name == Model::DELETED_AT) {

            } else if($item->name == 'permission') {

            } else if(str_contains($item->name, 'time') && !str_contains($item->name, 'times')) {
                $form
                    ->text($item->name)
                    ->label($item->comment ?? $item->name)
                    ->value($data->{$item->name})
                    ->disabled();
            } else if(str_contains($item->name, 'content') || str_contains($item->name, 'desc')) {
                $form
                    ->textArea($item->name)
                    ->label($item->comment ?? $item->name)
                    ->value($data->{$item->name})
                    ->disabled();
            } else {
                $form
                    ->text($item->name)
                    ->label($item->comment ?? $item->name)
                    ->value($data->{$item->name})
                    ->disabled();
            }
        }

        return $form->disabled();
    }

    public function buildCreate(Form $form, array|Arrayable $columns): Form
    {
        foreach($columns as $item) {

            $item = (object)$item;

            if($item->name == 'id') {

            } else if($item->name == Model::CREATED_AT) {

            } else if($item->name == Model::UPDATED_AT) {

            } else if($item->name == Model::DELETED_AT) {

            } else if($item->name == 'permission') {

            } else if(str_contains($item->name, 'time') && !str_contains($item->name, 'times')) {
                $form
                    ->dateTime($item->name)
                    ->label($item->comment ?? $item->name);
            } else if(str_contains($item->name, 'content') || str_contains($item->name, 'desc')) {
                $form
                    ->textArea($item->name)
                    ->label($item->comment ?? $item->name);
            } else {
                $form
                    ->text($item->name)
                    ->label($item->comment ?? $item->name);
            }
        }

        return $form;
    }

    public function buildEdit(Form $form, array|Arrayable $columns, object $data): Form
    {
        foreach($columns as $item) {

            $item = (object)$item;

            if($item->name == 'id') {
                $form
                    ->text($item->name)
                    ->label($item->comment ?? $item->name)
                    ->value($data->{$item->name})
                    ->disabled();
            } else if($item->name == Model::CREATED_AT) {

            } else if($item->name == Model::UPDATED_AT) {

            } else if($item->name == Model::DELETED_AT) {

            } else if($item->name == 'permission') {

            } else if(str_contains($item->name, 'time') && !str_contains($item->name, 'times')) {
                $form
                    ->dateTime($item->name)
                    ->label($item->comment ?? $item->name)
                    ->value($data->{$item->name});
            } else if(str_contains($item->name, 'content') || str_contains($item->name, 'desc')) {
                $form
                    ->textArea($item->name)
                    ->label($item->comment ?? $item->name)
                    ->value($data->{$item->name});
            } else {
                $form
                    ->text($item->name)
                    ->label($item->comment ?? $item->name)
                    ->value($data->{$item->name});
            }
        }

        return $form;
    }
}
