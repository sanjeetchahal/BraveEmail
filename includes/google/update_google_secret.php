<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>



</head>

<body class="bg-gray-50">
   
 <!-- include tabs -->
    <?php include 'tabs.php'; ?>
    <!-- Content -->
    <div class="max-w-xl mx-auto   prose bg-white text-center py-9">
    <?php
// Check if the form is submitted
require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['credentials'])) {
    $uploadDir = __DIR__ . '/uploads/';
    $uploadedFile = $uploadDir . basename($_FILES['credentials']['name']);
    $uploadSuccess = move_uploaded_file($_FILES['credentials']['tmp_name'], $uploadedFile);

    if ($uploadSuccess) {
        $configFile = __DIR__ . '/../config.php';
        $configData = include $configFile;
        // get relative url. 
        $configData['client_secret'] = 'google/uploads/' . basename($_FILES['credentials']['name']);

        $configContent = "<?php\n\nreturn " . var_export($configData, true) . ";\n";
        file_put_contents($configFile, $configContent);

        $client = new Google_Client();
        $client->setApplicationName('Brave Email');
        $client->addScope(Google_Service_Gmail::GMAIL_SEND);
        $client->setAuthConfig(__DIR__ .'/../'. DIRECTORY_SEPARATOR. $configData['client_secret']);
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        //echo $currentURL; 
        //  exit; 
        $redirect_url = admin_url('admin.php?page=brave-email-smtp&provider=google&tabview=usage');
        $client->setRedirectUri($redirect_url);

        if (!$client->isAccessTokenExpired()) {
            // Save the access token for future use
            file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'uploads/token.json', json_encode($client->getAccessToken()));
        }
        
        // Check if token already exists
        echo '<p class="text-green-600 text-center pt-2">File uploaded successfully!.</p>';
        if (file_exists(__DIR__ . DIRECTORY_SEPARATOR .'uploads/token.json')) {
            $accessToken = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'uploads/token.json'), true);
            $client->setAccessToken($accessToken);
        } else {
            // Redirect the user to the authorization URL
            $authUrl = $client->createAuthUrl();
            echo '<a class="cursor-pointer mx-auto text-center text-white whitespace-nowrap bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" href="'.$authUrl.'">Authenticate</a>';
            exit;
            // header('Location: ' . $authUrl);
            // exit();
        }

       
    } else {
        echo '<p class="text-red-600 text-center pt-2">Failed to upload the file.</p>';
    }
}
?>


<div class="container mx-auto p-6">
    <h1 class="text-3xl font-semibold mb-4">Setup Google Gmail Api</h1>

    <div class="bg-white rounded shadow p-4">
        <h2 class="text-xl font-semibold mb-2">Upload JSON Credentials</h2>
        <p class="mb-4">Upload the JSON credentials file you downloaded from the Google App here:</p>
        <form action="<?php echo $_SERVER['PHP_SELF'].'?page=brave-email-smtp&provider=google&tabview='.$_GET['tabview']; ?>" method="post" enctype="multipart/form-data">
            <div class="mb-4">
                <input type="file" name="credentials" accept=".json" required>
            </div>
            <div>
                <input type="submit" value="Upload"
                       class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 cursor-pointer">
            </div>
        </form>
    </div>
</div>


    </div>
</body>

</html>