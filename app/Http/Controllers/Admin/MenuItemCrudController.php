<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\DishTypeRequest;
use App\Http\Requests\MenuItemRequest;
use Illuminate\Support\Facades\Request;
use App\Models\DishType;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Models\MenuItem;

/**
 * Class MenuItemCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class MenuItemCrudController extends CrudController
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
        CRUD::setModel(\App\Models\MenuItem::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/menu-item');
        CRUD::setEntityNameStrings('menu item', 'menu items');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        // Verberg de knop 'Voorbeeld'
        CRUD::denyAccess([
            'show'
        ]);

        CRUD::column('id');
        CRUD::column('created_at');
        CRUD::column('updated_at');
        CRUD::column('code')->label('Code');
        CRUD::column('name')->label('Omschrijving');
        CRUD::column('price')->label('Prijs')->prefix('€ ');
        CRUD::addColumn([
            'name' => 'category',
            'label' => 'Valt onder',
            'entity' => 'dishtype',
            'model' => 'App\Models\DishType',
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
        CRUD::setValidation(MenuItemRequest::class);

        CRUD::field('code')->label('Code');
        CRUD::field('name')->label('Omschrijving');
        CRUD::field('price')->label('Prijs')->prefix('€');
        CRUD::addField([
            'name' => 'category',
            'label' => 'Valt onder',
            'entity' => 'dishtype',
            'model' => 'App\Models\DishType',
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
        $this->setupCreateOperation();
    }
}
