<?php

namespace Quintenmbusiness\LaravelTestCore\Traits\Model;

use Illuminate\Database\Eloquent\Model;

trait ModelTestHelper
{
    /**
     * The model class to be tested.
     *
     * @var string
     */
    protected string $modelClass;

    /**
     * Get an instance of the model.
     *
     * @return Model
     */
    protected function getModelInstance(): Model
    {
        return new $this->modelClass();
    }

    /**
     * Assert that the model's factory creates valid instances.
     *
     * @return void
     */
    public function assertFactoryCreatesModel(): void
    {
        $model = $this->modelClass::factory()->create();

        $this->assertInstanceOf(
            Model::class,
            $model,
            "Failed asserting that the model factory for [{$this->modelClass}] creates a valid instance."
        );

        $this->assertDatabaseHas(
            $model->getTable(),
            ['id' => $model->id],
            "Failed asserting that the model [{$this->modelClass}] with ID [{$model->id}] exists in the database."
        );
    }

    /**
     * Assert that the model supports soft deletes.
     *
     * @return void
     */
    public function assertModelSoftDeletes(): void
    {
        $model = $this->modelClass::factory()->create();

        $model->delete();

        $this->assertSoftDeleted(
            $model,
            "Failed asserting that the model [{$this->modelClass}] with ID [{$model->id}] is soft deleted."
        );
    }

    /**
     * Assert that the model has the specified fillable attributes.
     *
     * @param array $expectedFillable
     * @return void
     */
    public function assertModelFillableAttributes(array $expectedFillable): void
    {
        $model = $this->getModelInstance();
        $this->assertEquals(
            $expectedFillable,
            $model->getFillable(),
            "Failed asserting that the model [{$this->modelClass}] has the expected fillable attributes. " .
            "Expected: " . json_encode($expectedFillable) . " but found: " . json_encode($model->getFillable())
        );
    }
}