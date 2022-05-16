<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ReservationRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ReservationCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ReservationCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Reservation::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/reservation');
        CRUD::setEntityNameStrings('reservation', 'reservations');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('table')->label('Tafel');
        CRUD::column('date_time_reservation')->label('Datum en tijd van de reservering');
        CRUD::column('customer_id')->label('Klant');
        CRUD::column('amount')->label('Aantal volwassenen');
        CRUD::column('amount_k')->label('Aantal kinderen');
        CRUD::column('status')->label('Status');
        CRUD::column('allergies')->label('Allergieën');
        CRUD::column('notes')->label('Opmerkingen');
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ReservationRequest::class);

        CRUD::field('table')->label('Tafel');
        CRUD::field('date_time_reservation')->label('Datum en tijd van de reservering')->type('datetime_picker');
        CRUD::field('amount')->label('Aantal volwassenen');
        CRUD::field('amount_k')->label('Aantal kinderen');
        CRUD::field('allergies')->label('Allergieën');
        CRUD::field('notes')->label('Opmerkingen');

        $this->crud->addField([
            // select_from_array
            'name'    => 'status',
            'label'   => 'Status',
            'type'    => 'select_from_array',
            'options' => ['reservation' => 'gereserveerd', 'no reservation' => 'niet gereserveerd'],
        ]);

        $this->crud->addField([ 
            'label'     => "Klant",
            'type'      => 'select2',
            'name'      => 'customer_id', // the db column for the foreign key

            // optional - manually specify the related model and attribute
            'model'     => "App\Models\Customer", // related model
            'attribute' => 'name', // foreign key attribute that is shown to user
        ]);
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
