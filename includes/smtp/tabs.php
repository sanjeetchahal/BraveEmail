<div class="max-w-xl mx-auto">

    <div class="flex justify-center mb-3 gap-2 items-center">
        <div>
            <img width="16" height="16" alt="Google Drive icon (2020)" src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/Google_Drive_icon_%282020%29.svg/512px-Google_Drive_icon_%282020%29.svg.png">

        </div>
        <div class="text-bold text-lg text-gray-500">Google Mail Settings</div>
    </div>
    <?php
      // JSON structure containing tab data
      $tabs = '[
          {"label": "Other SMTP Settings", "tabview": "settings"},
          {"label": "Usage", "tabview": "usage"} ,
          {"label": "Test Email", "tabview": "test-email"}      
          ]';

          // Decoding the JSON string into an array
      $tabsArray = json_decode($tabs, true);

      $currentPage = "";
      // Get the current PHP page filename
      if(isset($_GET['tabview'])){
        $currentPage = $_GET['tabview'];
      }
      
      
      // Loop through the tab array and create the tab headers
      echo '<div class="mb-5 text-sm font-medium text-center text-gray-500 border-b border-gray-200 dark:text-gray-500 dark:border-gray-700">';
      echo '<ul class="flex flex-wrap -mb-px justify-center">';
      foreach ($tabsArray as $tab) {
        $isActive = ($currentPage === $tab['tabview']) ? 'text-blue-600 border-blue-600 active dark:text-blue-500 dark:border-blue-500' : 'hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-500';
        $isDisabled = isset($tab['disabled']) && $tab['disabled'] ? 'text-gray-400 cursor-not-allowed dark:text-gray-500' : '';
        echo '<li class="mr-2">';
        echo '<a href="' . admin_url('admin.php?page=brave-email-smtp&provider=smtp&tabview='.$tab['tabview']) . '" class="inline-block p-4 border-b-2 border-transparent rounded-t-lg ' . $isActive . ' ' . $isDisabled . '" aria-current="page">' . $tab['label'] . '</a>';
        echo '</li>';
      }
      echo '</ul>';
      echo '</div>';
      ?>
      </div>