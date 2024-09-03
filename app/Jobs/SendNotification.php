<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
// models notifications, user,community , communitymembers, post
use App\Models\Notification;
use App\Models\User;
use App\Models\Community;
use App\Models\CommunityMember;
use App\Models\Post;

class SendNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $post;

    /**
     * Create a new job instance.
     */
    public function __construct($post)
    {
        $this->post = $post;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $post = $this->post;
        $community = Community::find($post->communityId);
        $author = $post->author;

        $authorMember = CommunityMember::where('userId', $author->id)->where('communityId', $post->communityId)->first();
        if($authorMember->role =='member'){
            return ;
        }

        $members = CommunityMember::where('communityId', $post->communityId)->where('isActive',true)->get();

        $title = 'Nouveau post de '.$author->username;
        $content = $post->title;
        $slug = '/p/'.$post->slug;

        foreach ($members as $member) {
            if ($member->userId != $author->id) {
                Notification::create([
                    'title' => $title,
                    'content' => $content,
                    'userId' => $member->userId,
                    'slug' => $slug,
                ]);
            }
        }
    }
}
