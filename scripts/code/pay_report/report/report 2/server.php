<?php
$host = "localhost"; 
$username = "USERNAME";  
$password = "PASSWORD";  
$dbname = "DB_NAME"; 

// Create a connection
$conn = mysqli_connect($host, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

header('Content-Type: application/json');

class db {
    private $conn;

    function __construct($database) {
        $this->conn = $database;
    }

    function query($qstr) {
        return mysqli_query($this->conn, $qstr);
    }

    function fetch_assoc($res) {
        return mysqli_fetch_assoc($res);
    }

    function real_escape_string($str) {
        return mysqli_real_escape_string($this->conn, $str);
    }
}

$db = new db($conn);

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    
    // Fetch UTM options
    $utm_params = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term'];
    $utm_options = [];

    foreach ($utm_params as $param) {
        $utm_query = "SELECT DISTINCT $param FROM utm";
        $utm_result = $db->query($utm_query);
        $values = [];
        while ($row = $db->fetch_assoc($utm_result)) {
            $values[] = $row[$param];
        }
        $utm_options[$param] = $values;
    }



    // Fetch Razdel options
    $razdel_query = "SELECT id, razdel_name FROM razdel";  
    $razdel_result = $db->query($razdel_query);
    $razdel_options = [];
    while ($row = $db->fetch_assoc($razdel_result)) {
        $razdel_options[] = [
            'id' => $row['id'],
            'name' => $row['razdel_name']
        ];
    }


    // Fetch Tags options
    $tags_query = "SELECT id, tag_name FROM tags"; 
    $tags_result = $db->query($tags_query);
    $tags_options = [];
    while ($row = $db->fetch_assoc($tags_result)) {
        $tags_options[] = [
            'id' => $row['id'],
            'name' => $row['tag_name']
        ];
    }

    // Fetch Managers options
    $manager_query = "
        SELECT DISTINCT cards.id AS manager_id, users.real_user_name AS manager_name
        FROM cards
        JOIN users ON cards.id = users.klid
        WHERE users.access_level = 4
    ";
    $manager_result = $db->query($manager_query);
    $manager_options = [];
    while ($row = $db->fetch_assoc($manager_result)) {
        $manager_options[] = [
            'id' => $row['manager_id'],
            'name' => $row['manager_name']
        ];
    }

    // Fetch Partners options
    $partner_query = "
        SELECT DISTINCT cards.user_id AS partner_id, users.real_user_name AS partner_name
        FROM cards
        JOIN users ON cards.user_id = users.id
    ";
    $partner_result = $db->query($partner_query);
    $partner_options = [];
    while ($row = $db->fetch_assoc($partner_result)) {
        $partner_options[] = [
            'id' => $row['partner_id'],
            'name' => $row['partner_name']
        ];
    }

    // Fetch Product options
    $product_query = "SELECT id, descr FROM product";
    $product_result = $db->query($product_query);
    $product_options = [];
    while ($row = $db->fetch_assoc($product_result)) {
        $product_options[] = [
            'id' => $row['id'],
            'description' => $row['descr']
        ];


    // Fetch earliest payment date from avangard
    $earliest_payment_query = "SELECT MIN(DATE(FROM_UNIXTIME(tm))) as earliest_date FROM avangard";
    $earliest_payment_result = $db->query($earliest_payment_query);
    $earliest_payment_data = $db->fetch_assoc($earliest_payment_result);
    $earliest_date = $earliest_payment_data['earliest_date'];}

    // Return the data as JSON
    echo json_encode([
        'utm' => $utm_options,
        'razdel' => $razdel_options,
        'tags' => $tags_options,
        'manager' => $manager_options,
        'partner' => $partner_options,
        'product' => $product_options,
        'earliest_date' => $earliest_date,
        'current_date' => date("Y-m-d")
    ]);
}

else {

    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];
    $razdel = $_POST['razdel'];
    $tags = $_POST['tags'];
    $manager = $_POST['manager'];
    $partner = $_POST['partner'];  
    $product = $_POST['product'];
    $utm_source = $_POST['utm_source'];
    $utm_medium = $_POST['utm_medium'];
    $utm_campaign = $_POST['utm_campaign'];
    $utm_content = $_POST['utm_content'];
    $utm_term = $_POST['utm_term'];


    $whereConditions = [
        "DATE(FROM_UNIXTIME(avangard.tm)) BETWEEN '$startDate' AND '$endDate'"
    ];

    if (!empty($utm_source)) {
        $utmSourceEscaped = $db->real_escape_string($utm_source);
        $whereConditions[] = "utm.utm_source = '$utmSourceEscaped'";
    } 
    if (!empty($utm_medium)) {
        $utmMediumEscaped = $db->real_escape_string($utm_medium);
        $whereConditions[] = "utm.utm_medium = '$utmMediumEscaped'";
    }

    if (!empty($utm_campaign)) {
        $utmCampaignEscaped = $db->real_escape_string($utm_campaign);
        $whereConditions[] = "utm.utm_campaign = '$utmCampaignEscaped'";
    }

    if (!empty($utm_content)) {
        $utmContentEscaped = $db->real_escape_string($utm_content);
        $whereConditions[] = "utm.utm_content = '$utmContentEscaped'";
    }

    if (!empty($utm_term)) {
        $utmTermEscaped = $db->real_escape_string($utm_term);
        $whereConditions[] = "utm.utm_term = '$utmTermEscaped'";
    }

    if (!empty($razdel)) {
        $razdelEscaped = $db->real_escape_string($razdel);
        $whereConditions[] = "razdel.id = '$razdelEscaped'";
    }

    if (!empty($tags)) {
        $tagsEscaped = $db->real_escape_string($tags);
        $whereConditions[] = "tags.id = '$tagsEscaped'";
    }

    if (!empty($manager)) {
        $managerEscaped = $db->real_escape_string($manager);
        $whereConditions[] = "managerUsers.klid = '$managerEscaped' AND managerUsers.access_level = 4";
    }

    if (!empty($partner)) {
        $partnerEscaped = $db->real_escape_string($partner);
        $whereConditions[] = "cards.user_id = '$partnerEscaped'";
    }

    if (!empty($product)) {
        $productEscaped = $db->real_escape_string($product);
        $whereConditions[] = "product.id = '$productEscaped'";
    }

    $whereClause = implode(" AND ", $whereConditions);

    $query = "
        SELECT 
            DATE(FROM_UNIXTIME(avangard.tm)) as paymentDate,
            SUM(avangard.amount1) as amount,
            razdel.razdel_name,
            tags.tag_name,
            managerUsers.real_user_name AS manager,
            partnerUsers.real_user_name AS partner,
            product.descr AS product_description,
            utm.utm_source,
            utm.utm_medium,
            utm.utm_campaign,
            utm.utm_content,
            utm.utm_term
        FROM avangard
        JOIN cards ON avangard.vk_uid = cards.uid
        LEFT JOIN product ON avangard.product_id = product.id
        LEFT JOIN utm ON cards.uid = utm.uid
        LEFT JOIN razdel ON cards.razdel = razdel.id
        LEFT JOIN tags_op ON cards.uid = tags_op.uid
        LEFT JOIN tags ON tags_op.tag_id = tags.id
        LEFT JOIN users AS managerUsers ON cards.id = managerUsers.klid AND managerUsers.access_level > 3
        LEFT JOIN users AS partnerUsers ON cards.user_id = partnerUsers.id
        WHERE $whereClause
        GROUP BY 
            DATE(FROM_UNIXTIME(avangard.tm)),
            razdel.razdel_name,
            tags.tag_name,
            managerUsers.real_user_name,
            partnerUsers.real_user_name,
            product.descr,
            utm.utm_source,
            utm.utm_medium,
            utm.utm_campaign,
            utm.utm_content,
            utm.utm_term
        ORDER BY paymentDate
    ";




    $result = $db->query($query);
    $data = [];
    while ($row = $db->fetch_assoc($result)) {
        $data[] = $row;
    }

    echo json_encode($data);
}
?>