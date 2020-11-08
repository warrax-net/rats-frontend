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
    
    $json = [
        'name' => $_POST['name'],
        'color' => $_POST['color'],
        'description' => $_POST['description'],
        'death_reason' => $_POST['death_reason'],
        'is_alive' => isset($_POST['is_alive']) && $_POST['is_alive'] == '1' ? 1 : 0,
        'birth_date_d' => $_POST['birth_date_d'],
        'birth_date_m' => $_POST['birth_date_m'],
        'birth_date_y' => $_POST['birth_date_y'],
        'death_date_d' => $_POST['death_date_d'],
        'death_date_m' => $_POST['death_date_m'],
        'death_date_y' => $_POST['death_date_y'],
        'arrival_date_d' => $_POST['arrival_date_d'],
        'arrival_date_m' => $_POST['arrival_date_m'],
        'arrival_date_y' => $_POST['arrival_date_y'],
    ];
    $postdata = json_encode($json);
    // echo $postdata;
    // Set up URL
    $url = SERVER_URL . '/api/rat/create.php';
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
        <form id="ratForm" action="add.php?success=<?php echo $success?>" method="post">
        <?php
            foreach ($_POST as $a => $b) {
                echo '<input type="hidden" name="'.htmlentities($a).'" value="'.htmlentities($b).'">';
            }
        ?>
        </form>
        <script type="text/javascript">
            document.getElementById('ratForm').submit();
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
