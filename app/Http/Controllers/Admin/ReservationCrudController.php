<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ReservationRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Konekt\PdfInvoice\InvoicePrinter;
use Illuminate\Support\Facades\Storage;
use App\Models\Customer;

/**
 * Class ReservationCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ReservationCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation {
        store as traitStore;
    }
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
        $this->input = \Route::current()->parameter('input');

        CRUD::setModel(\App\Models\Reservation::class);

        CRUD::setRoute(config('backpack.base.route_prefix') . '/reservation/show/' . $this->input);

        //ordering the items by table ascending
        $this->crud->orderBy('table', 'asc');

        //checking the slug to check what needs to been shown and adding a clause for it
        switch ($this->input) {
            case 'future':
                CRUD::setEntityNameStrings('reservering', 'Toekomstige reserveringen');
                $this->crud->addClause('where', 'date_time_reservation', '>', date('Y-m-d'));
                break;
            case 'history':
                CRUD::setEntityNameStrings('reservering', 'Reserveringen geschiedenis');
                $this->crud->addClause('where', 'date_time_reservation', '<', date('Y-m-d'));
                break;
            case 'today':
                CRUD::setEntityNameStrings('reservering', 'Reserveringen vandaag');

                $this->crud->addClause('where', function ($query) {
                    //selecting all the reservations that are today
                    $query->where('date_time_reservation', '>=', date('Y-m-d'));
                    $query->where('date_time_reservation', '<=', date('Y-m-d', strtotime('+1 day')));
                });
                break;

            default:
                CRUD::setEntityNameStrings('reservering', 'Alle reserveringen');
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
        // Verberg de knop 'Voorbeeld'
        CRUD::denyAccess([
            'show'
        ]);

        CRUD::column('table')->label('Tafel');
        CRUD::column('date_time_reservation')->label('Datum en tijd van de reservering');
        CRUD::column('customer_id')->label('Klant');
        CRUD::column('amount')->label('Aantal volwassenen');
        CRUD::column('amount_k')->label('Aantal kinderen');
        CRUD::column('status')->label('Status');
        CRUD::column('allergies')->label('Allergieën');
        CRUD::column('notes')->label('Opmerkingen');

        // extra buttons in het 'actions' veld
        $this->crud->addButtonFromView('line', 'receiptPdfButton', 'receiptPdfButton', 'beginning');
        $this->crud->addButtonFromModelFunction('line', 'openOrders', 'openOrders', 'beginning');
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
        CRUD::field('amount')->label('Aantal volwassenen');
        CRUD::field('amount_k')->label('Aantal kinderen');
        CRUD::field('allergies')->label('Allergieën');
        CRUD::field('notes')->label('Opmerkingen');

        $this->crud->addField([
            // date selector
            'name'    => 'date_time_reservation',
            'label'   => 'Datum en tijd van de reservering',
            'type'    => 'datetime_picker',
            'datetime_picker_options' => [
                'format' => 'DD/MM/YYYY HH:mm',
                'minDate' => date('Y-m-d H:i'),
                'language' => 'nl',
                'tooltips' => [ //use this to translate the tooltips in the field
                    'today' => 'Vandaag',
                    'selectDate' => 'Selecteer een datum',
                ]
            ],
        ]);

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

    public function store(ReservationRequest $request)
    {
        // operations before save here
        // checking if there is a customer with the selected id
        $customer = Customer::where('id', $request->customer_id)->first();
        if ($customer) {
            //if true checking if the customer reserved the last time
            if ($customer->reservations->sortByDesc('created_at')->first()->status != 'reservation') {
                //if they did not reserve the last time, send a warning
                \Alert::add('warning', 'deze klant heeft een vorige keer niet gereserveerd')->flash();
            }
        }
        return $this->traitStore();
    }

    public function receipt($id)
    {
        $reservation = \App\Models\Reservation::find($id);
        $customer = \App\Models\Customer::find($reservation->customer_id);
        $orders = \App\Models\Order::where('reservation_id', $id)->get();
        $total = 0;

        $invoice = new InvoicePrinter($size = 'a4', $currency = '€', $language = 'nl');
        $invoice->changeLanguageTerm('discount', '');
        $invoice->changeLanguageTerm('product', 'Menu item');

        /* Header settings */
        $invoice->setLogo(Storage::path('img/logoSteak.jpeg'));    //logo image path
        $invoice->setColor("#3333ff");      // pdf color scheme
        $invoice->setType("Bon");    // Invoice Type
        $invoice->setReference($reservation->id);    // Reference
        $invoice->setDate(date('  d m Y', time()));   //Billing Date
        $invoice->setTime(date('H:i', time()));   //Billing Time

        $invoice->setFrom(array("Steak onder water", "0523 282 222", "Parkweg 1A1", "7772 XP Hardenberg"));
        $invoice->setTo(array($customer->name, 'Tafel: ' . $reservation->table, 'Reservatie datum en tijd: ' . $reservation->date_time_reservation, 'Totaal aantal gasten: ' . (intVal($reservation->amount) + intVal($reservation->amount_k))));

        foreach ($orders as $order) {
            $invoice->addItem($order->menu_item->name, '', $order->amount, ($order->menu_item->price / 100 * 9) * $order->amount, $order->menu_item->price, '', $order->menu_item->price * $order->amount);

            $total += $order->menu_item->price * $order->amount;
        }

        $invoice->addTotal("BTW 9%", $total / 100 * 9);
        $invoice->addTotal("Totaal", $total, true);

        $invoice->setFooternote("Steak onder water");

        $invoice->render('example1.pdf', 'I');
        /* I => Display on browser, D => Force Download, F => local path save, S => return document as string */
    }
}
