<?php namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ScopeInterface;

class DescOrdersScope implements ScopeInterface
{

    /**
     * All of the extensions to be added to the builder.
     *
     * @var array
     */
    protected $extensions = ['Unsorted'];

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     * @param  \Illuminate\Database\Eloquent\Model   $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder->orderBy('created_at', 'desc');
    }

    /**
     * Remove the scope from the given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     * @param  \Illuminate\Database\Eloquent\Model   $model
     *
     * @return void
     */
    public function remove(Builder $builder, Model $model)
    {
        $column = $model->getCreatedAtColumn();
        $query = $builder->getQuery();

        $query->orders = collect($query->orders)->reject(function ($order) use ($column) {
            return $order['column'] == $column && 'desc' == $order['direction'];
        })->values()->all();
    }

    /**
     * Extend the query builder with the needed functions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    public function extend(Builder $builder)
    {
        foreach ($this->extensions as $extension) {
            $this->{"add{$extension}"}($builder);
        }
    }

    /**
     * Add the unsorted extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    protected function addUnsorted(Builder $builder)
    {
        $builder->macro('unsorted', function (Builder $builder) {
            $this->remove($builder, $builder->getModel());

            return $builder;
        });
    }
}
