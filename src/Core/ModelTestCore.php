<?php

namespace Quintenmbusiness\LaravelTestCore\Core;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Model;
use Quintenmbusiness\LaravelTestCore\Helpers\ModelHelper;
use Tests\TestCase;
use ReflectionClass;
use Throwable;

abstract class ModelTestCore extends TestCase
{
    use RefreshDatabase;

    protected ModelHelper $helper;

    /**
     * The model being tested.
     *
     * @var string
     */
    protected string $modelClass;

    /**
     * Reflection class instance for the model.
     *
     * @var ReflectionClass
     */
    protected ReflectionClass $reflection;

    /**
     * Set up the test environment.
     *
     * @return void
     * @throws \ReflectionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        if (!isset($this->modelClass)) {
            $this->fail('The $modelClass property must be set in the test class.');
        }

        $this->reflection = new ReflectionClass($this->modelClass);
        $this->helper = new ModelHelper(new $this->modelClass());
    }

    /**
     * Get an instance of the model.
     *
     * @return Model
     */
    protected function getModelInstance(): Model
    {
        return new $modelClass();
    }

    /**
     * Assert that the model's factory creates valid instances.
     *
     * @return void
     */
    public function assertFactoryCreatesModel(): void
    {
        $modelClass = $this->modelClass;

        $currentCount = $modelClass::count();

        $model = $modelClass::factory()->create();

        $this->assertInstanceOf(
            Model::class,
            $model,
            "Failed asserting that the model factory for [{$modelClass}] creates a valid instance."
        );

        $newCount = $modelClass::count();

        $this->assertEquals(
            $currentCount + 1,
            $newCount,
            "Failed asserting that the database count for [{$modelClass}] increased by 1."
        );
    }

    /**
     * Test all relationships for the model to ensure they are correctly set up.
     *
     * @return void
     */
    public function relationsCorrectlySetup(): void
    {
        $relations = $this->helper->getRelationships();

        if (empty($relations)) {
            $this->markTestSkipped('No relationships defined for this model.');
        }
        $modelClass = $this->modelClass;

        $model = $this->modelClass::factory()->create();

        foreach ($relations as $relation) {
            try {
                $relationship = $model->{$relation['name']};

                $this->assertTrue(
                    true,
                    "The relationship [{$relation['name']}] of type [{$relation['returnType']}] is correctly set up."
                );
            } catch (Throwable $e) {
                $this->fail(
                    "Failed asserting that the relationship [{$relation['name']}] of type [{$relation['returnType']}] works. " .
                    "Error: {$e->getMessage()}"
                );
            }
        }
    }
}
