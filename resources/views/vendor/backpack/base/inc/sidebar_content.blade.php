<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i
            class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('customer') }}'><i
            class='nav-icon la la-address-card'></i>
        Klanten</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('food-category') }}'><i
            class='nav-icon la la-question'></i> Food categories</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('dish-type') }}'><i
            class='nav-icon la la-question'></i> Dish types</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('menu-item') }}'><i
            class='nav-icon la la-question'></i> Menu items</a></li>


<!-- reservations -->
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-book"></i>Reserveringen</a>
    <ul class="nav-dropdown-items">
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('reservation/show/all') }}'><i
                    class='nav-icon la la-book-open'></i> Alle reserveringen</a></li>

        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('reservation/show/future') }}'><i
                    class='nav-icon la la-book-open'></i> Toekomstige reserveringen</a></li>

        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('reservation/show/history') }}'><i
                    class='nav-icon la la-book-open'></i> Reserveringen geshidenis</a></li>

        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('reservation/show/today') }}'><i
                    class='nav-icon la la-book-open'></i> Reserveringen vandaag</a></li>
    </ul>
</li>
<!------------------>

<!-- reservations -->
<li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-sticky-note"></i>Bestellingen</a>
        <ul class="nav-dropdown-items">
                <li class='nav-item'><a class='nav-link' href='{{ backpack_url('order/bartender') }}'><i
                        class='nav-icon la la-cocktail'></i> Bestellingen barman</a></li>
        </ul>
    </li>
    <!------------------>


{{-- <li class='nav-item'><a class='nav-link' href='{{ backpack_url('drink') }}'><i class='nav-icon la la-question'></i> Drinks</a></li> --}}
{{-- <li class='nav-item'><a class='nav-link' href='{{ backpack_url('drink-category') }}'><i class='nav-icon la la-question'></i> Drink categories</a></li> --}}
