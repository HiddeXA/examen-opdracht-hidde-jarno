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

        $invoice = new InvoicePrinter();

        /* Header settings */
        $invoice->setLogo(Storage::path('img/logoSteak.jpeg'));    //logo image path
        $invoice->setColor("#3333ff");      // pdf color scheme
        $invoice->setType("Bon");    // Invoice Type
        //$invoice->setReference("INV-55033645");   // Reference
        $invoice->setDate(date('M dS ,Y', time()));   //Billing Date
        $invoice->setTime(date('h:i:s A', time()));   //Billing Time
       // $invoice->setDue(date('M dS ,Y', strtotime('+3 months')));    // Due Date
        $invoice->setFrom(array("Seller Name", "Sample Company Name", "128 AA Juanita Ave", "Glendora , CA 91740"));
        $invoice->setTo(array("Purchaser Name", "Sample Company Name", "128 AA Juanita Ave", "Glendora , CA 91740"));

        $invoice->addItem("AMD Athlon X2DC-7450", "2.4GHz/1GB/160GB/SMP-DVD/VB", 6, 0, 580, 0, 3480);
        $invoice->addItem("PDC-E5300", "2.6GHz/1GB/320GB/SMP-DVD/FDD/VB", 4, 0, 645, 0, 2580);
        $invoice->addItem('LG 18.5" WLCD', "", 10, 0, 230, 0, 2300);
        $invoice->addItem("HP LaserJet 5200", "", 1, 0, 1100, 0, 1100);

        $invoice->addTotal("Total", 9460);
        $invoice->addTotal("VAT 21%", 1986.6);
        $invoice->addTotal("Total due", 11446.6, true);

        $invoice->addBadge("Payment Paid");

        $invoice->addTitle("Important Notice");

        $invoice->addParagraph("No item will be replaced or refunded if you don't have the invoice with you.");

        $invoice->setFooternote("My Company Name Here");

        $invoice->render('example1.pdf', 'I');
        /* I => Display on browser, D => Force Download, F => local path save, S => return document as string */
    }
}
