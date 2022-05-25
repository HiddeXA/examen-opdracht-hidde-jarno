<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class CustomerCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CustomerCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Customer::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/customer');
        CRUD::setEntityNameStrings('klant', 'klanten');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('name')->label('Naam');
        CRUD::column('email');
        CRUD::column('phone')->label('Telefoon');

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
        //adding fields and adding wrappers so its all shown nicely in the form
        CRUD::setValidation(CustomerRequest::class);
        $this->crud->addField([
            'name' => 'name',
            'label' => 'Naam',
            'type' => 'text',
            'wrapper'     => ['class' => 'col-md-12'],
        ]);
        $this->crud->addField([
            'name' => 'email',
            'label' => 'Email',
            'type' => 'email',
            'wrapper'     => ['class' => 'col-md-6'],
        ]);
        $this->crud->addField([
            'name' => 'phone',
            'label' => 'Telefoon',
            'type' => 'number',
            'wrapper'     => ['class' => 'col-md-6'],
        ]);
        $this->crud->addField([
            'name' => 'street',
            'label' => 'Straat',
            'type' => 'text',
            'wrapper'     => ['class' => 'col-md-6'],
        ]);
        $this->crud->addField([
            'name' => 'house_number',
            'label' => 'Huisnummer',
            'type' => 'number',
            'wrapper'     => ['class' => 'col-md-3'],
        ]);
        $this->crud->addField([
            'name' => 'house_number_addon',
            'label' => 'Toevoeging',
            'type' => 'text',
            'wrapper'     => ['class' => 'col-md-3'],
        ]);
        $this->crud->addField([
            'name' => 'country',
            'label' => 'Land',
            'type' => 'text',
            'wrapper'     => ['class' => 'col-md-6'],
        ]);
        $this->crud->addField([
            'name' => 'zip_code',
            'label' => 'Postcode',
            'type' => 'text',
            'wrapper'     => ['class' => 'col-md-6'],
        ]);
        $this->crud->addField([
            'name' => 'city',
            'label' => 'Stad',
            'type' => 'text',
            'wrapper'     => ['class' => 'col-md-6'],
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
    protected function setupShowOperation()
    {
        CRUD::column('name')->label('Naam');
        CRUD::column('email')->label('Email');
        CRUD::column('phone')->label('Telefoon');
        CRUD::column('street')->label('Straat');
        CRUD::column('house_number')->label('Huisnummer');
        CRUD::column('house_number_addon')->label('Toevoeging');
        CRUD::column('country')->label('Land');
        CRUD::column('zip_code')->label('Postcode');
        CRUD::column('city')->label('Stad');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
