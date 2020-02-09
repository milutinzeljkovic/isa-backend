<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class BookRoomForAppointment extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/');
            $browser->click('@loginbtn');
            $browser->waitForText('email');
            $browser->append('input[name="email"]' , 'adminklinike@mail.com');
            $browser->assertInputValue('input[name="email"]', 'adminklinike@mail.com');
            $browser->type('pass', 'pass123');
            $browser->pause(1000);
            $browser->click('@loginbtn1');
            $browser->pause(1000);
            $browser->visit('/pending-appointment-requests');
            $browser->pause(2000);
            $browser->click('@accept');
            $browser->pause(2000);
            $browser->type('duration', '2 hours');
            $browser->pause(2000);
            $browser->type('discount', '15%');
            $browser->pause(2000);
            $browser->click('@addRoom');
            $browser->pause(3000);
            $browser->click('@reserveR');
            $browser->pause(3000);


        });
    }
}
