<?php

namespace Quintenmbusiness\LaravelTestCore\Helpers;

use Illuminate\Database\Eloquent\Model;
use \ReflectionClass;
use \ReflectionMethod;
use \Reflection;
use Throwable;

class ModelHelper
{
    protected Model $model;

    protected \ReflectionClass $reflection;

    public function __construct(Model $model) {
        $this->model = $model;
        $this->reflection = new \ReflectionClass($model);
    }

    protected array $relationBlacklist = [
        'newFactory',
        'factory',
    ];

    /**
     * Detect likely relationships on the model.
     *
     * @return array
     */
    public function getRelationships(): array
    {
        $reflection = $this->reflection;
        $relationships = [];

        return collect($reflection->getMethods())->filter(function ($method) use ($reflection) {
            // Check if the method belongs to the current class and has the desired return type
            return $method->class === $reflection->getName() &&
                $method->getReturnType() &&
                str_contains((string) $method->getReturnType(), "Illuminate\Database\Eloquent\Relations\\");
        })->map(function ($method) {
            $returnType = class_basename((string) $method->getReturnType());

            return [
                'name' => $method->name,
                'returnType' => $returnType,
            ];
        })->toArray();
    }

    function inspectModel(Model $model)
    {
        $reflection = $this->reflection;

        // Basic model information
        $info = [
            'Class' => $reflection->getName(),
            'Table' => $model->getTable(),
            'Primary Key' => $model->getKeyName(),
            'Attributes' => $model->getAttributes(),
            'Fillable' => $model->getFillable(),
            'Guarded' => $model->getGuarded(),
            'Casts' => $model->getCasts(),
            'Hidden' => $model->getHidden(),
            'Visible' => $model->getVisible(),
            'Dates' => $model->getDates(),
            'Appends' => $model->getAppends(),
            'Connection' => $model->getConnectionName(),
            'Relations' => collect($model->getRelations())->keys()->toArray(),
        ];

        // Available methods
        $methods = collect($reflection->getMethods())->filter(function ($method) use ($reflection) {
            return $method->class === $reflection->getName();
        })->toArray();

        dd($methods);

        $info['Methods'] = $methods;

        // Relationship methods detection
        $relationships = [];
        foreach ($methods as $method) {
            try {
                $result = $model->{$method}();
                if ($result instanceof Illuminate\Database\Eloquent\Relations\Relation) {
                    $relationships[$method] = get_class($result);
                }
            } catch (Throwable $e) {
                // Ignore methods that throw exceptions
            }
        }

        $info['Detected Relationships'] = $relationships;
    }
}
