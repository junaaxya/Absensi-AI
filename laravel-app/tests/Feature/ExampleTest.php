<?php

it('returns a successful response', function () {
    $response = $this->get('/');

    // Root URL redirects to login page
    $response->assertRedirect('/login');
});
