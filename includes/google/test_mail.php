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
require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor/autoload.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['to_email'])) {
    // echo "<pre>";print_r($_POST);exit;
    // echo "test";exit;
    if($_POST['to_email']!=''){
        // include("brave_email_smtp.php");
        // $send_ob = new braveEmail();
        $emailConfig = array();

        $emailConfig['to_email'] = $_POST['to_email'];
        if($_POST['html_status'] == 'yes'){
            $emailConfig['message'] = "";
            $emailConfig['htmlPath'] = true;
        }
        else{
            $emailConfig['message'] = "This is for Testing";
            $emailConfig['htmlPath'] = false;
        }
        
        $emailConfig['subject'] = "Brave Email Testing";
        $result = $this->to_email($emailConfig);  
        if($result){
            echo '<p class="text-green-600 text-center pt-2">Successfully Sent!.</p>';
        } 
        else
        {
            echo '<p class="text-green-600 text-center pt-2">Opps!.</p>';
        }
        
    } else {
        echo '<p class="text-red-600 text-center pt-2">Failed to upload the file.</p>';
    }
}
?>


<div class="container mx-auto p-6">
    <h1 class="text-3xl font-semibold mb-4">Sent Email</h1>

    <div class="bg-white rounded shadow p-4">
        <form action="<?php echo $_SERVER['PHP_SELF'].'?page=brave_email_smtp&provider=google&tabview='.$_GET['tabview']; ?>" method="post" enctype="multipart/form-data">
            <div class="md:flex md:items-center mb-6">
                <div class="md:w-1/3">
                <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4" for="inline-full-name">
                    Send To
                </label>
                </div>
                <div class="md:w-2/3">
                <input name="to_email" required class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500" id="to_email" type="text">
                </div>
            </div>
            <div class="md:flex md:items-center mb-6">
                <div class="md:w-1/3">
                <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4" for="inline-full-name">
                    HTML
                </label>
                </div>
                <div class="md:w-2/3">
                    <select name="html_status" class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="grid-state">
                        <option value="no">No</option>
                        <option value="yes">Yes</option>
                       
                    </select>
                </div>
            </div>
            <div>
                <input type="submit" value="Send Email"
                       class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 cursor-pointer">
            </div>
        </form>
    </div>
</div>


    </div>
</body>

</html>