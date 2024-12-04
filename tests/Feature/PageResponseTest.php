<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('gives back succesful respond for home page', function () {
    get(route('home'))->assertOk(); //assert = afirmacion
});
