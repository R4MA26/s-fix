<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJurnalPenyesuaianTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('jurnal_penyesuaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('akun_id')->constrained('akun')->onUpdate('cascade')->onDelete('cascade');
            $table->date('tanggal');
            $table->text('keterangan');
            $table->string('bukti', 128)->nullable();
            $table->boolean('debit_atau_kredit');
            $table->bigInteger('nilai');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('jurnal_penyesuaian');
        Schema::enableForeignKeyConstraints();
    }
}
