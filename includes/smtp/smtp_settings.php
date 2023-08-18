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
    <div class="max-w-xl mx-auto   prose bg-white ">
    <?php
// Check if the form is submitted


$configFile = __DIR__ . '/../config.php';
$configData = include $configFile;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST) && !empty($_POST)) {
    // echo "<pre>";print_r($_POST);exit;
    // echo "test";exit;
        $configData['smtp_settings']['host'] = $_POST['smtp_host'];
        $configData['smtp_settings']['encryptionType'] = $_POST['encryption_type'];
        $configData['smtp_settings']['port'] = $_POST['smtp_port'];
        $configData['smtp_settings']['username'] = $_POST['smtp_username'];
        $configData['smtp_settings']['password'] = $_POST['smtp_password'];
        $configData['api_status'] = true;
        $configData['active_provider'] = 'smtp';


        $configContent = "<?php\n\nreturn " . var_export($configData, true) . ";\n";
        $result = file_put_contents($configFile, $configContent);
       
        if($result){
            echo '<p class="text-green-600 text-center pt-2">Successfully Updated!.</p>';
        } 
        else
        {
            echo '<p class="text-green-600 text-center pt-2">Opps!.</p>';
        }
        
    
}


?>


<div class="container mx-auto p-6">
    <h1 class="text-3xl font-semibold mb-4">Settings</h1>

    <div class="bg-white rounded shadow p-4">
        <form action="<?php echo $_SERVER['PHP_SELF'].'?page=brave-email-smtp&provider=smtp&tabview='.$_GET['tabview']; ?>" method="post">
            <div class="md:flex md:items-center mb-6">
                <div class="md:w-1/3">
                    <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4" for="inline-full-name">
                        SMTP HOST*
                    </label>
                </div>
                <div class="md:w-2/3">
                    <input name="smtp_host" value="<?php echo $configData['smtp_settings']['host']; ?>" required class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500" id="to_email" type="text">
                </div>
            </div>
            <div class="md:flex md:items-center mb-6">
                <div class="md:w-1/3">
                <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4" for="inline-full-name">
                    Encryption Type
                </label>
                </div>
                <div class="md:w-2/3">
                <?php $encryptionType = $configData['smtp_settings']['encryptionType']; ?>
                    <select name="encryption_type" class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="grid-state">
                        <option value="no" <?php echo ($encryptionType == "no"?"selected":""); ?>>None</option>
                        <option value="ssl" <?php echo ($encryptionType == "ssl"?"selected":""); ?>>SSL</option>
                        <option value="tls" <?php echo ($encryptionType == "tls"?"selected":""); ?>>TLS</option>
                    </select>
                </div>
            </div>
            <div class="md:flex md:items-center mb-6">
                <div class="md:w-1/3">
                    <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4" for="inline-full-name">
                        SMTP Port*
                    </label>
                </div>
                <div class="md:w-2/3">
                    <input name="smtp_port" value="<?php echo $configData['smtp_settings']['port']; ?>" required class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500" id="to_email" type="text">
                </div>
            </div>
            <div class="md:flex md:items-center mb-6">
                <div class="md:w-1/3">
                    <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4" for="inline-full-name">
                        SMTP Username*
                    </label>
                </div>
                <div class="md:w-2/3">
                    <input name="smtp_username" value="<?php echo $configData['smtp_settings']['username']; ?>" required class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500" id="to_email" type="text">
                </div>
            </div>
            <div class="md:flex md:items-center mb-6">
                <div class="md:w-1/3">
                    <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4" for="inline-full-name">
                        SMTP Password*
                    </label>
                </div>
                <div class="md:w-2/3">
                    <input name="smtp_password" value="<?php echo $configData['smtp_settings']['password']; ?>" required class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500" id="to_email" type="text">
                </div>
            </div>
            <div>
                <input type="submit" value="Update"
                       class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 cursor-pointer">
            </div>
        </form>
    </div>
</div>


    </div>
</body>

</html>