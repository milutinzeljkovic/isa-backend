<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ExampleTest extends DuskTestCase
{
    /**
     * A basic browser test example.
     *
     * @return void
     */
    public function testBasicExample()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/');
            $browser->click('@loginbtn');
            $browser->waitForText('email');

            $browser->append('input[name="email"]' , 'pacijent@mail.com');
            $browser->assertInputValue('input[name="email"]', 'pacijent@mail.com');




            $browser->type('pass', 'pass123');
            $browser->pause(1000);
            $browser->click('@loginbtn1');
            $browser->pause(1000);
            $browser->visit('/home');
            $browser->pause(1000);
            $browser->click('@search');
            $browser->pause(1000);
            $browser->click('@badge');
            $browser->pause(1000);
            $browser->click('@reserve');
            $browser->pause(3000);
         
            $browser->resize(1920, 1080);
        });
    }
}
