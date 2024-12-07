<?php

namespace Quintenmbusiness\LaravelTestCore\Traits\Model;

use Illuminate\Database\Eloquent\Model;

trait ModelTestHelper
{
    /**
     * Get the model class being tested.
     *
     * @return string
     */
    abstract protected function getModelClass(): string;

    /**
     * Get an instance of the model.
     *
     * @return Model
     */
    protected function getModelInstance(): Model
    {
        return new ($this->getModelClass());
    }

    /**
     * Assert that the model's factory creates valid instances.
     *
     * @return void
     */
    public function assertFactoryCreatesModel(): void
    {
        $modelClass = $this->getModelClass();
        $model = $modelClass::factory()->create();

        $this->assertInstanceOf(
            Model::class,
            $model,
            "Failed asserting that the model factory for [{$modelClass}] creates a valid instance."
        );

        $this->assertDatabaseHas(
            $model->getTable(),
            ['id' => $model->id],
            "Failed asserting that the model [{$modelClass}] with ID [{$model->id}] exists in the database."
        );
    }

    /**
     * Assert that the model supports soft deletes.
     *
     * @return void
     */
    public function assertModelSoftDeletes(): void
    {
        $modelClass = $this->getModelClass();
        $model = $modelClass::factory()->create();

        $model->delete();

        $this->assertSoftDeleted(
            $model,
            "Failed asserting that the model [{$modelClass}] with ID [{$model->id}] is soft deleted."
        );
    }
}