<?php

namespace Tests\Feature;

use App\Mail\CourseAccessActivated;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AutomaticPurchaseAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_purchase_creates_inactive_account_without_requesting_password()
    {
        $participantRole = Role::create([
            'name' => 'participant',
            'label' => 'Peserta',
        ]);
        $course = $this->createCourse();

        $response = $this->post(route('purchase.store', $course), [
            'name' => 'Peserta Baru',
            'email' => 'baru@example.com',
            'phone' => '08123456789',
            'company' => 'Contoh Indonesia',
        ]);

        $user = User::where('email', 'baru@example.com')->firstOrFail();
        $order = Order::where('user_id', $user->id)->firstOrFail();

        $response->assertRedirect(route('payments.show', $order->invoice_number));
        $this->assertSame($participantRole->id, $user->role_id);
        $this->assertFalse($user->is_active);
        $this->assertSame('waiting_payment', $order->status);
        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'status' => 'waiting_confirmation',
        ]);
    }

    public function test_inactive_account_cannot_login_before_payment_is_verified()
    {
        $user = User::factory()->create([
            'email' => 'pending@example.com',
            'is_active' => false,
            'password' => Hash::make('password-lama'),
        ]);

        $response = $this->post(route('login.attempt'), [
            'email' => $user->email,
            'password' => 'password-lama',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_admin_verification_activates_account_enrolls_course_and_sends_credentials()
    {
        Mail::fake();

        $adminRole = Role::create([
            'name' => 'super-admin',
            'label' => 'Super Admin',
        ]);
        $participantRole = Role::create([
            'name' => 'participant',
            'label' => 'Peserta',
        ]);
        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
            'is_active' => true,
        ]);
        $participant = User::factory()->create([
            'role_id' => $participantRole->id,
            'email' => 'aktivasi@example.com',
            'is_active' => false,
        ]);
        $course = $this->createCourse();
        $order = Order::create([
            'user_id' => $participant->id,
            'course_id' => $course->id,
            'invoice_number' => 'INV-TEST-001',
            'subtotal' => $course->price,
            'discount' => 0,
            'total' => $course->price,
            'status' => 'payment_submitted',
        ]);
        Payment::create([
            'order_id' => $order->id,
            'method' => 'manual_transfer',
            'status' => 'submitted',
            'amount' => $order->total,
        ]);

        $response = $this
            ->actingAs($admin)
            ->put(route('admin.payments.verify', $order));

        $response->assertSessionHas('status');
        $participant->refresh();
        $order->refresh();

        $this->assertTrue($participant->is_active);
        $this->assertNotNull($participant->activation_credentials_sent_at);
        $this->assertSame('paid', $order->status);
        $this->assertDatabaseHas('enrollments', [
            'user_id' => $participant->id,
            'course_id' => $course->id,
            'order_id' => $order->id,
        ]);

        Mail::assertSent(CourseAccessActivated::class, function (CourseAccessActivated $mail) use ($participant) {
            return $mail->hasTo($participant->email)
                && $mail->temporaryPassword !== null
                && Hash::check($mail->temporaryPassword, $participant->password);
        });
    }

    private function createCourse(): Course
    {
        return Course::create([
            'title' => 'Paket Belajar Digital',
            'slug' => 'paket-belajar-digital',
            'summary' => 'Paket pengujian alur pembelian.',
            'price' => 250000,
            'level' => 'Beginner',
            'status' => 'published',
        ]);
    }
}
