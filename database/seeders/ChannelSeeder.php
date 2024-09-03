<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Channel;
use App\Models\Community;

class ChannelSeeder extends Seeder
{
    public function run()
    {
       $channel = "Général";

        $communities = Community::all();
        // check if the channel already exists with communityId
        foreach ($communities as $community) {
            $check = Channel::where('name', $channel)->where('communityId', $community->id)->first();
            if (!$check) {
                Channel::create([
                    'name' => $channel,
                    'communityId' => $community->id,
                ]);
                $this->command->info('Channel ajouté à la communauté '.$community->name.'.') ;
            }
        }

    }
}
