<?php

namespace Tests\Feature;

use Tests\TestCase;

class LiveSubmissionPageTest extends TestCase
{
    public function test_guest_can_open_live_create_page(): void
    {
        $response = $this->get('/lives/create');

        $response->assertOk();
    }
}
