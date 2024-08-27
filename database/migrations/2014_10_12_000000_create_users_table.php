<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->boolean('must_change_passwd')->default(false);
            $table->boolean('isAdmin');
            $table->boolean('isVendor');
            $table->boolean('isEmployee');
            $table->enum('vendor_loc', ['HQ', 'NTPS', 'LTPS', 'LKHEP', 'KLHEP', 'Longku', 'Narengi', 'Jagiroad'])->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        DB::table('users')->insert([
            ['name' => 'Admin',
            'email' => 'admin@apgcl.org',
            'password' => bcrypt('admin@2024'),
            'isAdmin' => 1,
            'isVendor' => 0,
            'isEmployee' => 0,
            'vendor_loc' => null,
            'created_at' => now(),
            'updated_at' => now(),],
            ['name' => 'Subhankar Sarkar',
            'email' => 'subhankar.sarkar@apgcl.org',
            'password' => bcrypt('Welcome@2022'),
            'isAdmin' => 0,
            'isVendor' => 0,
            'isEmployee' => 1,
            'vendor_loc' => null,
            'created_at' => now(),
            'updated_at' => now(),],
            ['name' => 'Nabarun Das',
            'email' => 'nabarundas.n@interlaceindia.com',
            'password' => bcrypt('Welcome@2022'),
            'isAdmin' => 0,
            'isVendor' => 1,
            'isEmployee' => 0,
            'vendor_loc' => null,
            'created_at' => now(),
            'updated_at' => now(),],
            ['name' => 'Niran Deori',
            'email' => 'niranbikomiyadeori.b@interlaceindia.com',
            'password' => bcrypt('Welcome@2022'),
            'isAdmin' => 0,
            'isVendor' => 1,
            'isEmployee' => 0,
            'vendor_loc' => 'HQ',
            'created_at' => now(),
            'updated_at' => now(),],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
