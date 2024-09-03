<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\Post;
use Illuminate\Support\Str;


class UpdatePostSlug extends Command
{
    protected $signature = 'update:slug';
    protected $description = 'Update slug';

    public function handle()
    {
        $posts = Post::all();
        $this->info("Start updating post slug");
        foreach ($posts as $post) {
            
            $slug = Str::slug($post->title);
            $this->info("Post ".$post->id." slug : ".$slug);
            
            if ($slug) {
                $post->update(['slug' => $slug]);
                $this->info("Updated slug for Post {$post->id}");
            }
        }
        $this->info("Finish updating duration for Lessons");

    }

}