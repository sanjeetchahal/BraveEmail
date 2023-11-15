<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor/autoload.php';
$configFile = __DIR__ . '/../config.php';
$configData = include $configFile;

$client = new Google_Client();
$client->setApplicationName('Brave Email');
$client->addScope(Google_Service_Gmail::GMAIL_SEND);
$client->setAuthConfig(__DIR__ .'/../'. DIRECTORY_SEPARATOR. $configData['client_secret']);
$client->setAccessType('offline');
$client->setPrompt('select_account consent');
$redirect_url = admin_url('admin.php?page=brave-email-smtp&provider=google&tabview=usage');
$client->setRedirectUri($redirect_url);
$tokenStatus = true;
$errorMessage = "";
if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if(isset($token['error'])){
        $tokenStatus = false;
        $errorMessage = $token['error'];
    }else
    {

    
        $client->setAccessToken($token);
        file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'uploads/token.json', json_encode($client->getAccessToken()));
        $configData['api_status'] = true;
        $configData['active_provider'] = 'google';
        $configContent = "<?php\n\nreturn " . var_export($configData, true) . ";\n";
        file_put_contents($configFile, $configContent);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usage</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>



</head>

<body class="bg-gray-50">
   
 <!-- include tabs -->
    <?php include 'tabs.php'; ?>
    <!-- Content -->
    <div class="max-w-xl mx-auto   prose bg-white ">



<div class="container mx-auto p-6">
    <h1 class="text-3xl font-semibold mb-4">Sample Code</h1>

    <div class="bg-white rounded shadow p-4">
        <?php if($tokenStatus){ ?>
        <div class="mb-3 bg-yellow-100 p-5">
            <pre>
&lt;?php

$emailConfig = array();
$emailConfig['subject'] = "";
$emailConfig['message'] = "hello";
$emailConfig['htmlPath'] = false;
$emailConfig['to_email'] = "test1@exmple.com, test2@exampple.com"; // add multiple recipients with comma separated
$emailConfig['from_email'] ="";
$emailConfig['from_name'] = "";
$emailConfig['attachments'] = array("htdoc/example/example.png"); // Add Multiple usin comma seperated
$send_ob = new braveEmail();
$send_ob->to_email($emailConfig);

?&gt;
</pre>
        </div>
        <?php } ?>
        <?php if(!$tokenStatus){ ?>
            <div class="mb-3 bg-yellow-100 p-5">
                <h3><?php echo $errorMessage; ?></h3>
            </div>
        <?php } ?>  
    </div>
</div>


    </div>
</body>

</html>