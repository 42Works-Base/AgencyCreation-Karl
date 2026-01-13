<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('agencies', function (Blueprint $table) {
            if (! Schema::hasColumn('agencies', 'logo_path')) {
                $table->string('logo_path')->nullable()->after('type');
            }
            if (! Schema::hasColumn('agencies', 'email_logo_path')) {
                $table->string('email_logo_path')->nullable()->after('logo_path');
            }
            if (! Schema::hasColumn('agencies', 'background_image_path')) {
                $table->string('background_image_path')->nullable()->after('email_logo_path');
            }
            if (! Schema::hasColumn('agencies', 'skin_color')) {
                $table->string('skin_color', 7)->nullable()->after('background_image_path'); // store hex like #1a2b3c
            }
        });
    }

    public function down()
    {
        Schema::table('agencies', function (Blueprint $table) {
            $table->dropColumn(['logo_path', 'email_logo_path', 'background_image_path', 'skin_color']);
        });
    }
};
