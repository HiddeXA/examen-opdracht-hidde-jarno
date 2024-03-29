<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\DishTypeRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class DishTypeCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class DishTypeCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\DishType::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/dish-type');
        CRUD::setEntityNameStrings('dish type', 'dish types');

        // order the columns by name
        $this->crud->orderBy('name', 'asc');

        // Verberg de knop Voorbeeld
        CRUD::denyAccess([
            'show',
        ]);
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('id');
        CRUD::column('created_at');
        CRUD::column('updated_at');
        CRUD::column('name')->label('Naam');
        CRUD::column('code')->label('Code');
        CRUD::addColumn([
            'name' => 'category',
            'label' => 'Valt onder',
            'entity' => 'foodcategory',
            'model' => 'App\Models\FoodCategory',
            'attribute' => 'name',
            'type' => 'select',
        ]);


        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {

        CRUD::setValidation(DishTypeRequest::class);

        CRUD::field('name')->label('Naam');
        CRUD::field('code')->label('Code');
        CRUD::addField([
            'name' => 'category',
            'label' => 'Valt onder',
            'attribute' => 'name',
            'entity' => 'foodcategory',
            'model' => 'App\Models\FoodCategory',
            'type' => 'select2',
        ]);

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        // de code van de dish_type mag niet worden aangepast.

        CRUD::field('name')->label('Naam');
        CRUD::addField([
            'name' => 'category',
            'label' => 'Valt onder',
            'attribute' => 'name',
            'entity' => 'foodcategory',
            'model' => 'App\Models\FoodCategory',
            'type' => 'select2',
        ]);
    }
}
