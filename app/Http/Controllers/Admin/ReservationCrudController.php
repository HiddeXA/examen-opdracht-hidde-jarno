<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ReservationRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Konekt\PdfInvoice\InvoicePrinter;
use Illuminate\Support\Facades\Storage;

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
        CRUD::setEntityNameStrings('reservering', 'reserveringen');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
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

        //buttons
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
        $invoice->setTo(array($customer->name, $customer->email, $customer->phone, ""));
        
        foreach ( $orders as $order ) {
            $invoice->addItem($order->menu_item->name, '' , $order->amount, $order->menu_item->price/100*9,$order->menu_item->price,'',$order->menu_item->price * $order->amount);

            $total += $order->menu_item->price * $order->amount;
        }

        $invoice->addTotal("BTW 9%", $total/100*9);
        $invoice->addTotal("Totaal", $total, true);

        $invoice->setFooternote("Steak onder water");

        $invoice->render('example1.pdf', 'I');
        /* I => Display on browser, D => Force Download, F => local path save, S => return document as string */
    }
}
