<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller; 
use App\Models\User; 

use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use GuzzleHttp\Client;

use Microsoft\Graph\Generated\Users\Item\Messages\MessagesRequestBuilderGetQueryParameters;
use Microsoft\Graph\Generated\Users\Item\Messages\MessagesRequestBuilderGetRequestConfiguration;

use Microsoft\Kiota\Authentication\Oauth\AuthorizationCodeContext;
use Microsoft\Graph\Generated\Users\Item\SendMail\SendMailPostRequestBody;
use Microsoft\Graph\Generated\Models\BodyType;
use Microsoft\Graph\Generated\Models\EmailAddress;
use Microsoft\Graph\Generated\Models\ItemBody;
use Microsoft\Graph\Generated\Models\Message;
use Microsoft\Graph\Generated\Models\Recipient;
use Microsoft\Graph\GraphServiceClient;
use Microsoft\Kiota\Abstractions\ApiException;

// request
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
// log facade
use Illuminate\Support\Facades\Log;



class Office365MailController extends Controller
{
    protected $clientId;
    protected $clientSecret;
    protected $redirectUri;
    protected $tenantId;

    public function __construct()
    {
        $this->clientId = env('O365_CLIENT_ID');
        $this->clientSecret = env('O365_CLIENT_SECRET');
        $this->redirectUri = env('O365_REDIRECT_URI');
        $this->tenantId = env('O365_TENANT_ID');
    }

    public function get_auth_full_url(){
        $url = "https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/authorize";

        $queryParams = http_build_query([
            'client_id' => $this->clientId,
            'response_type' => 'code',
            'redirect_uri' => $this->redirectUri,
            'response_mode' => 'query',
            'scope' => 'Mail.Read Mail.Send offline_access',
            'state' => csrf_token(),
        ]);

        $fullUrl = "{$url}?{$queryParams}";
        return $fullUrl;
    }

    public function redirectToProvider()
    {
        $fullUrl = $this->get_auth_full_url();

        // return json
        return response()->json([
            'url' => $fullUrl,
        ]);

    }

    public function handleProviderCallback(Request $request)
    {
        $code = $request->input('code');

        // Obtenir le token d'accès
        $client = new Client();
        $response = $client->post("https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/token", [
            'form_params' => [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'code' => $code,
                'redirect_uri' => $this->redirectUri,
                'grant_type' => 'authorization_code',
            ],
        ]);

        $tokenData = json_decode($response->getBody(), true);

        $accessToken = $tokenData['access_token'];
        $refreshToken = $tokenData['refresh_token'];

        $user = Auth::user();

        $user->is_login_office = true;

        $user->auth_code = $accessToken;
        $user->refresh_token = $refreshToken;

        $user->save();

        return response()->json(['message' => 'Connexion avec Office 365 réussie']);
        
    }

    public function getMails(Request $request)
    {
        // Obtenir le token d'accès depuis la session ou une autre source
        $user = Auth::user();
        $accessToken = $user->auth_code;

        $client = new Client();


        try {

            // Requête à l'API Microsoft Graph pour récupérer les emails
            $response = $client->get('https://graph.microsoft.com/v1.0/me/messages', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Accept' => 'application/json',
                ],
                'query' => [
                    '$top' => 1, 
                    '$select' => 'subject,from,receivedDateTime,bodyPreview,hasAttachments,body,importance,isRead,isDraft,internetMessageId,parentFolderId,sender,sentDateTime,subject,toRecipients,webLink',
                ],
            ]);

            // Décoder la réponse JSON
            $mails = json_decode($response->getBody(), true);

            // Retourner la liste des emails
            return response()->json($mails);

        } catch (\Exception $e) {
            // message 
            $error_message = $e->getMessage();
            // code
            $error_code = $e->getCode();
            if($error_code == 401){
                
                $user = Auth::user();
                $accessToken = $user->auth_code;
                $client = new Client();
                try {
                    // Requête à l'API Microsoft Graph pour récupérer les emails
                    $response = $client->get('https://graph.microsoft.com/v1.0/me/messages', [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $accessToken,
                            'Accept' => 'application/json',
                        ],
                        'query' => [
                            '$top' => 1, 
                            '$select' => 'subject,from,receivedDateTime,bodyPreview,hasAttachments,body,importance,isRead,isDraft,internetMessageId,parentFolderId,sender,sentDateTime,subject,toRecipients,webLink',
                        ],
                    ]);

                    // Décoder la réponse JSON
                    $mails = json_decode($response->getBody(), true);

                    // Retourner la liste des emails
                    return response()->json($mails);

                } catch (\Exception $e) {

                    // message 
                    $error_message = $e->getMessage();

                    // code
                    $error_code = $e->getCode();

                    if($error_code == 401){
                        $user->is_login_office = false;
                        $fullUrl = $this->get_auth_full_url();
                        // $user->auth_code = null;
                        $user->save();
                        return response()->json(['message'=>'Veuillez vous authentifier avec votre compte Office 365 pour accéder à.','url'=>$fullUrl], 523);
                    }
                }
                return response()->json(['error' => 'Failed to fetch emails: ' . $e->getMessage(),'error_code'=>$error_code], 500);
                    
            }
            return response()->json(['error' => 'Failed to fetch emails: ' . $e->getMessage(),'error_code'=>$error_code], 500);
        }
    }

    public function getMails_()
    {  
        $user = Auth::user();
        $tokenRequestContext = new AuthorizationCodeContext(
            $this->tenantId,
            $this->clientId,
            $this->clientSecret,
            $user->auth_code,
            $this->redirectUri,
        );
        $scopes = ['Mail.ReadWrite','User.Read'];
        $graphServiceClient = new GraphServiceClient($tokenRequestContext, $scopes);

        // PHP 8
        $messages = $graphServiceClient->me()->messages()->get(new MessagesRequestBuilderGetRequestConfiguration(
            headers: ['Prefer' => 'outlook.body-content-type=text']
        ))->wait();

        // $graph = new Graph();
        // $graph->setAccessToken($accessToken);

        // $mails = $graph->createRequest('GET', '/me/messages')
        //     ->setReturnType(Model\Message::class)
        //     ->execute();

        return response()->json($mails);
    }

    public function refreshOfficeToken()
    {
        // Obtenir le token de rafraîchissement depuis l'utilisateur authentifié
        $user = Auth::user();
        $refreshToken = $user->refresh_token;

        if (!$refreshToken) {
            return false;
        }

        $client = new Client();

        try {
            // Envoyer la requête POST pour rafraîchir le token
            $response = $client->post('https://login.microsoftonline.com/{tenant_id}/oauth2/v2.0/token', [
                'form_params' => [
                    'client_id' => env('OAUTH_CLIENT_ID'), // ID de l'application
                    'client_secret' => env('OAUTH_CLIENT_SECRET'), // Secret de l'application
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $refreshToken,
                    'redirect_uri' => env('OAUTH_REDIRECT_URI'), // URI de redirection utilisé lors de l'authentification initiale
                    'scope' => 'https://graph.microsoft.com/.default',
                ],
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
            ]);

            // Décoder la réponse JSON pour récupérer le nouveau token
            $tokenData = json_decode($response->getBody(), true);
            $newAccessToken = $tokenData['access_token'];
            $newRefreshToken = $tokenData['refresh_token'];

            // Sauvegarder les nouveaux tokens
            $user->auth_code = $newAccessToken;
            $user->refresh_token = $newRefreshToken;
            $user->is_login_office = true;

            $user->save();

            return  true;

        } catch (\Exception $e) {
            return false;
        }
    }


    public function sendMail($to, $subject, $body,$attachments=[])
    {
        // Obtenir le token d'accès depuis la session ou une autre source
        $user = Auth::user();
        $accessToken = $user->auth_code;


        $client = new Client();

        // Créer le contenu de l'email au format requis par l'API Microsoft Graph
        $emailData = [
            'message' => [
                'subject' => $subject,
                'body' => [
                    'contentType' => 'Text', // Ou 'HTML' si tu veux envoyer un email HTML
                    'content' => $body
                ],
                'toRecipients' => [
                    [
                        'emailAddress' => [
                            'address' => $to
                        ]
                    ]
                ]
            ]
        ];

        // Ajouter les pièces jointes si elles existent
        if (!empty($attachments)) {

            $emailData['message']['attachments'] = [];

            foreach ($attachments as $attachment) {

                $emailData['message']['attachments'][] = [
                    '@odata.type' => '#microsoft.graph.fileAttachment',
                    'name' => $attachment['name'],  
                    'contentBytes' => base64_encode(file_get_contents($attachment['url'])),  
                ];
            }
            
        }

        try {

            $response = $client->post('https://graph.microsoft.com/v1.0/me/sendMail', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => $emailData,
            ]);

            return true;

        } catch (\Exception $e) {

            $error_message = $e->getMessage();
            $error_code = $e->getCode();
            Log::error('Erreur lors de l\'envoi de l\'email: ' . $error_message);
            return $error_message;

        }
    }

    public function sendMail_(Request $request)
    {

        // validation
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string',
            'message' => 'required|string',
            'to' => 'required|string',
        ], $this->customMessages);

        $errors = $validator->errors();

        if ($errors->count()) {
            return response()->json(array('message' => $errors->first(), 'data' => $errors), 422);
        }


        $accessToken = session('token'); // Obtenez le token de session

        $graph = new Graph();
        $graph->setAccessToken($accessToken);

        $emailBody = [
            "message" => [
                "subject" => $request->input('subject'),
                "body" => [
                    "contentType" => "Text",
                    "content" => $request->input('message'),
                ],
                "toRecipients" => [
                    [
                        "emailAddress" => [
                            "address" => $request->input('to'),
                        ],
                    ],
                ],
            ],
            "saveToSentItems" => "true",
        ];

        $graph->createRequest('POST', '/me/sendMail')
            ->attachBody($emailBody)
            ->execute();

        return response()->json(['message' => 'Email sent successfully']);
    }
}