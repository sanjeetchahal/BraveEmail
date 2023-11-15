
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
        <div class="mb-3 bg-yellow-100 p-5">
            <pre>
&lt;?php

$emailConfig = array();
$emailConfig['subject'] = "";
$emailConfig['message'] = "";
$emailConfig['htmlPath'] = "path/example.html";
$emailConfig['to_email'] = $recipients;
$emailConfig['from_email'] = "";
$emailConfig['from_name'] = "example";
$emailConfig['attachments'] = array("htdoc/example/example.png"); // Add Multiple usin comma seperated
$send_ob = new braveEmail();
$send_ob->to_email($emailConfig);

?&gt;
</pre>
        </div>
        
    </div>
</div>


    </div>
</body>

</html>