<?php
require_once (__DIR__ . '/../constants.php');

/*
 *  CONFIGURE EVERYTHING HERE
 */

// if you are not debugging and don't need error reporting, turn this off by error_reporting(0);
error_reporting(E_ALL & ~E_NOTICE);

try
{

    if (empty($_POST)) {
        throw new \Exception('Form is empty');
    }
    $rat_ids = [];
    foreach ($_POST['rat_ids'] as $rat_id) {
        array_push($rat_ids, $rat_id);
    }
    $json = [
        'title' => $_POST['title'],
        'description' => $_POST['description'],
        'url' => $_POST['url'],
        'date_d' => $_POST['date_d'],
        'date_m' => $_POST['date_m'],
        'date_y' => $_POST['date_y'],
        'is_video' => isset($_POST['is_video']) && $_POST['is_video'] == '1' ? 1 : 0,
        'rat_ids' => $rat_ids,
    ];
    $postdata = json_encode($json);
    // echo $postdata;
    // Set up URL
    $url = SERVER_URL . '/api/photo/create.php';
    // echo $url;
    // Perform GET from URL and authorization headers
    $headers  = [
        'Content-Type: text/plain'
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);           
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_URL, $url);

    $response = curl_exec($ch);
    $info = null;
    if (!curl_errno($ch)) {
        $info = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    } else {
        return curl_error($ch);
    }
    curl_close($ch);

    if ($info === 200) {
        // If response HTTP status code is 200 OK
        $responseArr = json_decode($response, true); // JSON decode response body
        $success = urlencode($response);
        ?>
        <form id="photoForm" action="add.php?success=<?php echo $success?>" method="post">
        <?php
            foreach ($_POST as $a => $b) {
                echo '<input type="hidden" name="'.htmlentities($a).'" value="'.htmlentities($b).'">';
            }
        ?>
        </form>
        <script type="text/javascript">
            document.getElementById('photoForm').submit();
        </script>
        <?php
    } else {
        die($response);
    }
    
}
catch (\Exception $e)
{
    $responseArray = array('type' => 'danger', 'message' => $errorMessage);
}


// if requested by AJAX request return JSON response
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    $encoded = json_encode($responseArray);

    header('Content-Type: application/json');

    echo $encoded;
}
// else just display the message
else {
    echo $responseArray['message'];
}
