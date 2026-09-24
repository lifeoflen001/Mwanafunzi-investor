<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'phone')) $table->string('phone')->nullable();
            if (! Schema::hasColumn('users', 'country')) $table->string('country', 2)->nullable();
            if (! Schema::hasColumn('users', 'status')) $table->string('status')->default('active');
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_email')->index();
            $table->string('customer_name');
            $table->string('phone')->nullable();
            $table->string('country', 2)->nullable();
            $table->string('status')->default('pending_payment');
            $table->string('payment_status')->default('pending');
            $table->string('payment_provider')->nullable();
            $table->string('internal_payment_reference')->nullable()->unique();
            $table->string('transaction_reference')->nullable()->index();
            $table->decimal('subtotal', 14, 2)->default('0.00');
            $table->decimal('discount', 14, 2)->default('0.00');
            $table->decimal('tax', 14, 2)->default('0.00');
            $table->decimal('total', 14, 2)->default('0.00');
            $table->string('currency', 3)->default('TZS');
            $table->string('tax_label')->nullable();
            $table->decimal('tax_rate', 8, 4)->default('0.0000');
            $table->boolean('prices_include_tax')->default(false);
            $table->timestamp('terms_accepted_at')->nullable();
            $table->json('billing_details')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'created_at']);
            $table->index(['payment_status', 'created_at']);
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->nullableMorphs('purchasable');
            $table->string('title');
            $table->decimal('unit_price', 14, 2)->default('0.00');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('line_total', 14, 2)->default('0.00');
            $table->string('currency', 3)->default('TZS');
            $table->json('snapshot')->nullable();
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('provider');
            $table->string('internal_reference')->unique();
            $table->string('provider_transaction_id')->nullable()->unique();
            $table->string('provider_reference')->nullable();
            $table->decimal('amount', 14, 2)->default('0.00');
            $table->string('currency', 3);
            $table->string('status')->default('pending');
            $table->json('metadata')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->index(['provider', 'status']);
        });

        Schema::create('payment_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event_key')->unique();
            $table->string('event_type');
            $table->string('provider');
            $table->json('payload')->nullable();
            $table->string('status')->default('received');
            $table->timestamp('processed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
            $table->index(['provider', 'event_type']);
        });

        Schema::create('product_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_version_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('path');
            $table->string('disk')->default('local');
            $table->string('mime_type');
            $table->string('extension', 12);
            $table->unsignedBigInteger('size')->default(0);
            $table->string('checksum', 64)->nullable();
            $table->string('download_policy')->default('current_and_future');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['product_id', 'product_version_id']);
        });

        Schema::create('entitlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('order_item_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_email')->index();
            $table->string('type');
            $table->string('status')->default('active');
            $table->foreignId('product_version_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'status']);
            $table->index(['customer_email', 'status']);
            $table->index(['product_id', 'course_id', 'type']);
        });

        Schema::create('download_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entitlement_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_asset_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('downloaded_at');
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'downloaded_at']);
        });

        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('entitlement_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('pending');
            $table->timestamp('enrolled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'course_id']);
            $table->index(['course_id', 'status']);
        });

        Schema::create('course_waitlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('email');
            $table->boolean('consented')->default(false);
            $table->string('status')->default('active');
            $table->timestamp('joined_at');
            $table->timestamps();
            $table->unique(['course_id', 'email']);
            $table->index(['course_id', 'status']);
        });

        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('amount', 14, 2);
            $table->string('currency', 3);
            $table->string('status')->default('requested');
            $table->string('provider_reference')->nullable();
            $table->text('reason')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['order_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('course_waitlists');
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('download_logs');
        Schema::dropIfExists('entitlements');
        Schema::dropIfExists('product_assets');
        Schema::dropIfExists('payment_events');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::table('users', function (Blueprint $table) {
            foreach (['phone', 'country', 'status'] as $column) {
                if (Schema::hasColumn('users', $column)) $table->dropColumn($column);
            }
        });
    }
};
