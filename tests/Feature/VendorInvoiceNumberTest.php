<?php

namespace Tests\Feature;

use App\Models\Supplier;
use App\Models\User;
use App\Models\VendorInvoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendorInvoiceNumberTest extends TestCase
{
    use RefreshDatabase;

    public function test_vendor_invoice_number_is_generated_and_shown_after_recording(): void
    {
        $admin = User::factory()->admin()->create(['role_id' => null]);
        $supplier = Supplier::create([
            'company_name' => 'Test Supplier',
            'supplier_code' => 'SUP-TEST',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.finance.payables.index'))
            ->assertOk()
            ->assertSee('Invoice number is generated automatically');

        $response = $this->actingAs($admin)->post(route('admin.finance.payables.invoices.store'), [
            'supplier_id' => $supplier->id,
            'invoice_number' => 'USER-PROVIDED-NUMBER',
            'amount' => 125.50,
            'invoice_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(30)->format('Y-m-d'),
        ]);

        $invoice = VendorInvoice::firstOrFail();
        $expectedInvoiceNumber = 'VIN-' . $invoice->created_at->format('Ymd') . '-' . str_pad((string) $invoice->id, 6, '0', STR_PAD_LEFT);

        $this->assertSame($expectedInvoiceNumber, $invoice->invoice_number);
        $response->assertRedirect(route('admin.finance.payables.index'))
            ->assertSessionHas('success', 'Vendor Invoice ' . $expectedInvoiceNumber . ' recorded successfully.');
    }
}
