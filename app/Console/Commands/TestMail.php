<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\Post;
use Illuminate\Support\Str;
// 
use App\Http\Controllers\NotificationController;
use App\Models\Notification;
use App\Models\User;
use App\Models\Community;
use App\Models\CommunityMember;
class TestMail extends Command
{
    protected $signature = 'test:mail';
    protected $description = 'test mail';

    public function handle()
    {
        $this->info("Start testing email");
       $emails = [
        // 'yves.ahyi@kpsgroupe.com',
        // 'yves.ahiizertyui@kpsgroupe.com',
        // 'ahyi.yves@kpsgroupe.com',
        // 'laura.melanie@kpsgroupe.com',
        // 'habiboulaye.boubacar@airliquide.com',
        // 'boubacar.ndiaye@airliquide.com',
        'tambia.egbebot@cgi.com',
        'tambia.hroo@cgi.com',
        // 'fabrice.tueche@cgi.com',
        // 'mickael.foursov@univ-rennes.fr'


       ];

        foreach($emails as $email){
            $this->test_mail($email);
        }
        $this->info("End testing email");
        

    }

    public function test_mail($email){
        $this->info("Email: ".$email);  
        $this->info("Email is valid: ".($this->verifyEmail($email) ? 'true' : 'false'));
    }

    public function verifyEmail($email) {
        // Get the domain of the email
        list($user, $domain) = explode('@', $email);
    
        // Get the MX records for the domain
        if (!getmxrr($domain, $mxHosts)) {
            return false; // No MX records found
        }
    
        // Get the first MX record
        $mxHost = $mxHosts[0];
    
        // Open an SMTP connection to the MX host
        $connect = @fsockopen($mxHost, 25);
        if (!$connect) {
            return false; // Unable to connect to the server
        }
    
        // Read the server response
        $response = fgets($connect, 1024);
    
        if (strpos($response, '220') === false) {
            return false; // Server did not respond with 220 code
        }
    
        // Say hello to the server
        fputs($connect, "HELO example.com\r\n");
        $response = fgets($connect, 1024);
    
        if (strpos($response, '250') === false) {
            return false; // Server did not respond with 250 code
        }
    
        // Tell the server the sender email
        fputs($connect, "MAIL FROM: <sender@example.com>\r\n");
        $response = fgets($connect, 1024);
    
        if (strpos($response, '250') === false) {
            return false; // Server did not respond with 250 code
        }
    
        // Ask the server if the recipient email exists
        fputs($connect, "RCPT TO: <$email>\r\n");
        $response = fgets($connect, 1024);
    
        // Close the connection
        fputs($connect, "QUIT\r\n");
        fclose($connect);
    
        // Check if the server response indicates the email exists
        if (strpos($response, '250') !== false || strpos($response, '450') !== false) {
            return true;
        }
    
        return false;
    }

    

}