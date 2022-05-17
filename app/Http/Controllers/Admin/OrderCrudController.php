<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\OrderRequest;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Reservation;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class OrderCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class OrderCrudController extends CrudController
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

        $this->reservationId = \Route::current()->parameter('reservationId');

        CRUD::setModel(\App\Models\Order::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/order/' . $this->reservationId);

        //ordering the items in the list so the old items wont be shown
        $this->crud->orderBy('created_at', 'asc');

        //checking the slug to check what needs to been shown and adding a clause for it
        switch ($this->reservationId) {
            case 'bartender':
                CRUD::setEntityNameStrings('Bestelling', 'Bestellingen barman');
                $this->crud->addClause('where', function ($query) {
                    // adding all the drinks that are not served to an array so we can use it in the query later
                    $drinks = [];
                    foreach ($query->where('served', 0)->get() as $order) {
                        if ($order->menu_item->dishType->foodCategory->code == 'drk') {
                            $drinks[] = $order->id;
                        }
                    }
                    $query->whereIn('id', $drinks);
                });

                break;

            case 'chef':
                CRUD::setEntityNameStrings('Bestelling', 'Bestellingen kok');
                // $this->crud->addButtonFromModelFunction('line', 'openOrdersChef', 'openOrdersChef', 'beginning');

                $this->crud->addClause('where', function ($query) {
                    $food = [];
                    foreach ($query->get() as $order) {
                        if ($order->menu_item->dishType->foodCategory->code == 'vog') {
                            $food[] = $order->id;
                        }
                    }
                    $query->whereIn('id', $food);
                });
                break;

            default:
                CRUD::setEntityNameStrings('Bestelling', 'Bestellingen');
                $this->crud->addClause('where', 'reservation_id', $this->reservationId);
                break;
        }
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        //disabling some buttons so that the customer cant destroy the web app
        $this->crud->removeButton('create');
        CRUD::denyAccess([
            'update',
            'show'
        ]);


        CRUD::addColumn([
            'name' => 'table',
            'label' => 'Tafel',
            'entity' => 'reservation',
            'model' => 'App\Models\Reservation',
            'attribute' => 'id'
        ]);
        CRUD::column('amount')->label('Aantal');

        $this->crud->addColumn([
            'name'            => 'menu_item_id',
            'label'           => "Menu item",
            'type'            => 'model_function',
            'function_name'   => 'showMenuItemName',
        ]);

        $this->crud->addColumn([
            'name'            => 'served',
            'label'           => "Geserveerd?",
            'type'            => 'model_function',
            'function_name'   => 'change1ToYes0ToNo',
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
        CRUD::setValidation(OrderRequest::class);

        CRUD::field('id');
        CRUD::field('created_at');
        CRUD::field('updated_at');
        CRUD::field('amount');
        CRUD::field('status');
        CRUD::field('reservation_id');
        CRUD::field('menu_item_id');

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
