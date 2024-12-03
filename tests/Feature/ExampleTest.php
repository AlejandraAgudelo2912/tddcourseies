<?php

it('gives back succesful respond for home page', function () {
    $response = $this->get('/');
    $response->assertStatus(200);
});
