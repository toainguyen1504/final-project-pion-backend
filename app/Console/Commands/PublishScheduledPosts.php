<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;

class PublishScheduledPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posts:publish-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish scheduled posts when publish_at time is reached';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Auto publish bài viết đã hẹn giờ 
        $count = Post::where('visibility', 'scheduled_public')
            ->whereNotNull('publish_at')
            ->where('publish_at', '<=', now())
            ->update([
                'status' => 'published',
                'visibility' => 'public',
            ]);

        $this->info("Published {$count} scheduled posts.");

        return self::SUCCESS;
    }
}
