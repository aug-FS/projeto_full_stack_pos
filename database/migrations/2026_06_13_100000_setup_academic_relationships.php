<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add relationship to Turmas
        Schema::table('turmas', function (Blueprint $table) {
            $table->foreignId('professor_id')->nullable()->constrained('professores')->onDelete('set null');
        });

        // Create pivot table for Alunos and Turmas
        Schema::create('aluno_turma', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aluno_id')->constrained('alunos')->onDelete('cascade');
            $table->foreignId('turma_id')->constrained('turmas')->onDelete('cascade');
            $table->timestamps();
        });

        // Remove old column from Alunos
        Schema::table('alunos', function (Blueprint $table) {
            $table->dropColumn('turma');
        });
    }

    public function down(): void
    {
        Schema::table('alunos', function (Blueprint $table) {
            $table->string('turma')->nullable();
        });

        Schema::dropIfExists('aluno_turma');

        Schema::table('turmas', function (Blueprint $table) {
            $table->dropForeign(['professor_id']);
            $table->dropColumn('professor_id');
        });
    }
};
