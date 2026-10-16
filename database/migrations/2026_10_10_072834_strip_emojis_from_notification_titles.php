<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Strip leading emojis + whitespace from all notification titles
        $notifications = DB::table('notifications')->select('id', 'title')->get();

        foreach ($notifications as $n) {
            $cleaned = $this->stripLeadingEmojis($n->title);
            if ($cleaned !== $n->title) {
                DB::table('notifications')
                    ->where('id', $n->id)
                    ->update(['title' => $cleaned]);
            }
        }
    }

    public function down(): void
    {
        // Not reversible
    }

    private function stripLeadingEmojis(string $text): string
    {
        // Match leading emoji/whitespace characters and trim them
        $pattern = '/^[\s\x{1F000}-\x{1FAFF}\x{2600}-\x{27BF}\x{1F300}-\x{1F5FF}\x{1F600}-\x{1F64F}\x{1F680}-\x{1F6FF}\x{1F1E0}-\x{1F1FF}\x{2700}-\x{27BF}\x{FE0F}\x{200D}]+/u';
        return preg_replace($pattern, '', $text);
    }
};