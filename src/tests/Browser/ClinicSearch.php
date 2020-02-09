<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ClinicSearch extends DuskTestCase
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
            $browser->append('input[name="email"]' , 'pacijent@mail.com');
            $browser->assertInputValue('input[name="email"]', 'pacijent@mail.com');
            $browser->type('pass', 'pass123');
            $browser->pause(1500);
            $browser->click('@loginbtn1');
            $browser->pause(1500);
            $browser->visit('/clinics');
            $browser->pause(3500);
            $browser->click('@showFilter');
            $browser->type('@adresa' , 'janka');
            $browser->pause(2500);
            $browser->click('@badgeDetails');
            $browser->pause(1500);
            $browser->click('@showDoctors');
            $browser->pause(1500);
            

            $browser->maximize();

        });
    }
}
