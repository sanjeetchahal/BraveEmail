<?php
    $plugin_image_url = plugins_url('../assets/images', __FILE__);
    $configFile = __DIR__ . '/config.php';
    $configData = include $configFile;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 p-10">
    <div class="container bg-white px-4 py-4 ">
        <h1 class="text-xl">Brave Email SMTP</h1>
        
    </div>
    <div id="template-layout" data-v-app="">
    <div class="mx-5 my-5">
        <div class="grid sm:grid-cols-3 2xl:grid-cols-3 gap-10">
            <div class="w-full group border border-gray-200 rounded-lg shadow-lg">
                <div class="bg-primary relative py-5 px-3 overflow-hidden pt-3 flex justify-center items-center">
                    <img
                        class="object-contain h-full"
                        src="<?php echo $plugin_image_url.'/google.png'?>"
                        alt="New Modern Minimalist Home Moodboard">
                </div>
                <div class="bg-white px-5 py-5 lg:flex space-y-5 lg:justify-between lg:items-center lg:space-x-5 lg:space-y-0">
                    <div class="lg:text-sm font-semibold tracking-tight text-gray-900 mb-3 lg:mb-0">
                        <?php  
                        if(isset($configData['active_provider']) && $configData['active_provider'] == "google" && isset($configData['api_status']) && $configData['api_status']){
                            echo '<span class="text-success">Configured</span>';
                        }
                        else
                        {
                            echo '<span>Not Configured</span>';
                        }
                        ?>    
                        
                    </div>
                    
                    <a href="<?php echo admin_url('admin.php?page=brave-email-smtp&provider=google&tabview='); ?>"
                        class="cursor-pointer text-white whitespace-nowrap bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        Get Started </a>
                </div>
            </div>
            <div class="w-full group border border-gray-200 rounded-lg shadow-lg">
                <div class="bg-primary relative py-5 px-3 overflow-hidden pt-3 flex justify-center items-center">
                    <h2 class="my-1" style="font-size: 24px;height: 60px;">Other SMTP</h2>
                </div>
                <div class="bg-white px-5 py-5 lg:flex space-y-5 lg:justify-between lg:items-center lg:space-x-5 lg:space-y-0">
                    <div class="lg:text-sm font-semibold tracking-tight text-gray-900 mb-3 lg:mb-0">
                            <?php  
                            if(isset($configData['active_provider']) && $configData['active_provider'] == "smtp" && isset($configData['api_status']) && $configData['api_status']){
                                echo '<span class="text-success">Configured</span>';
                            }
                            else
                            {
                                echo '<span>Not Configured</span>';
                            }
                            ?>    
                            
                        </div>
                        
                    
                    <a href="<?php echo admin_url('admin.php?page=brave-email-smtp&provider=smtp&tabview=settings'); ?>" class="cursor-pointer text-white whitespace-nowrap bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        Get Started </a>
                </div>
            </div>
            
        </div>
    </div>
    
</div>
</body>

</html>