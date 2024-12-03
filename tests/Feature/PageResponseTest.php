<?php

use function Pest\Laravel\get;

it('gives back succesful respond for home page', function () {
    get(route('home'))->assertOk(); //assert = afirmacion
});
