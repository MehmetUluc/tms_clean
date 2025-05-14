<?php

namespace Tests\Feature;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Tms\Core\Models\Agency;
use Tms\Agency\Models\AgencyUser;
use Tms\Agency\Models\AgencyContact;
use Tms\Agency\Models\AgencyContract;
use Tms\Agency\Models\AgencyCommission;
use Tms\Agency\Models\AgencyPaymentTerm;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AgencyPluginTest extends TestCase
{
    // use RefreshDatabase;

    protected $agency;

    public function setUp(): void
    {
        parent::setUp();

        // Test acentesi oluştur
        $this->agency = Agency::create([
            'name' => 'Test Agency',
            'code' => 'TEST001',
            'email' => 'test@example.com',
            'is_active' => true,
            'slug' => 'test-agency'
        ]);
    }

    #[Test]
    public function it_can_create_agency_users()
    {
        $agencyUser = AgencyUser::create([
            'agency_id' => $this->agency->id,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '5551234567',
            'position' => 'Manager',
            'is_active' => true,
            'permissions' => ['view', 'edit', 'create']
        ]);

        $this->assertNotNull($agencyUser);
        $this->assertEquals('John Doe', $agencyUser->name);
        $this->assertEquals('john@example.com', $agencyUser->email);
        $this->assertEquals('Manager', $agencyUser->position);
        $this->assertTrue(in_array('edit', $agencyUser->permissions));
    }

    #[Test]
    public function it_can_create_agency_contacts()
    {
        $contact = AgencyContact::create([
            'agency_id' => $this->agency->id,
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'phone' => '5559876543',
            'position' => 'Sales Representative',
            'department' => 'Sales',
            'is_primary' => true,
            'notes' => 'Main contact for bookings'
        ]);

        $this->assertNotNull($contact);
        $this->assertEquals('Jane Smith', $contact->name);
        $this->assertEquals('Sales', $contact->department);
        $this->assertTrue($contact->is_primary);
    }

    #[Test]
    public function it_can_create_agency_contracts()
    {
        $contract = AgencyContract::create([
            'agency_id' => $this->agency->id,
            'name' => '2025 Season Contract',
            'start_date' => '2025-05-01',
            'end_date' => '2025-10-31',
            'status' => 'active',
            'document_path' => 'contracts/agency_001_2025.pdf',
            'terms' => 'Standard terms and conditions apply',
            'notes' => 'Signed on May 1, 2025'
        ]);

        $this->assertNotNull($contract);
        $this->assertEquals('2025 Season Contract', $contract->name);
        $this->assertEquals('2025-05-01', $contract->start_date);
        $this->assertEquals('2025-10-31', $contract->end_date);
        $this->assertEquals('active', $contract->status);
    }

    #[Test]
    public function it_can_create_agency_commissions()
    {
        $commission = AgencyCommission::create([
            'agency_id' => $this->agency->id,
            'name' => 'Standard Commission',
            'type' => 'percentage',
            'value' => 10.00,
            'start_date' => '2025-05-01',
            'end_date' => '2025-12-31',
            'is_active' => true
        ]);

        $this->assertNotNull($commission);
        $this->assertEquals('Standard Commission', $commission->name);
        $this->assertEquals('percentage', $commission->type);
        $this->assertEquals(10.00, $commission->value);
    }

    #[Test]
    public function it_can_create_agency_payment_terms()
    {
        $paymentTerm = AgencyPaymentTerm::create([
            'agency_id' => $this->agency->id,
            'name' => 'Net 30',
            'days' => 30,
            'description' => 'Payment due within 30 days of invoice date',
            'is_default' => true
        ]);

        $this->assertNotNull($paymentTerm);
        $this->assertEquals('Net 30', $paymentTerm->name);
        $this->assertEquals(30, $paymentTerm->days);
        $this->assertTrue($paymentTerm->is_default);
    }
}