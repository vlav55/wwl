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


if (isset($_GET['action']) && $_GET['action'] == 'getDetailsByOrder' && isset($_GET['order_numbers'])) {
    $order_numbers = explode(',', $_GET['order_numbers']); 

    $order_numbers = array_map(function($order) use ($db) {
        return $db->real_escape_string($order);
    }, $order_numbers);
    
    $orderString = "'" . implode("','", $order_numbers) . "'";    
    
    $query = "SELECT DISTINCT avangard.order_number, avangard.*, DATE(FROM_UNIXTIME(avangard.tm)) AS paymentDate, cards.razdel, cards.uid, cards.id as card_id
        FROM avangard 
        LEFT JOIN cards ON avangard.vk_uid = cards.uid
        LEFT JOIN users AS managerUsers ON cards.id = managerUsers.klid AND managerUsers.access_level = 4
        LEFT JOIN users AS partnerUsers ON cards.user_id = partnerUsers.id
        LEFT JOIN product ON avangard.product_id = product.id
        LEFT JOIN utm ON cards.uid = utm.uid
        LEFT JOIN razdel ON cards.razdel = razdel.id
        LEFT JOIN tags_op ON cards.uid = tags_op.uid
        LEFT JOIN tags ON tags_op.tag_id = tags.id
        WHERE avangard.order_number IN ($orderString)";

    if (isset($_GET['tag_id'])) {
        $tag_id = intval($_GET['tag_id']);
        $query .= " AND tags.id = $tag_id";
    }

    if (isset($_GET['razdel_id'])) {
        $razdel_id = intval($_GET['razdel_id']);
        $query .= " AND razdel.id = $razdel_id";
    }

    if (isset($_GET['manager_id'])) {
        $manager_id = intval($_GET['manager_id']);
        $query .= " AND managerUsers.klid = $manager_id AND managerUsers.access_level=4";
    }

    if (isset($_GET['partner_id'])) {
        $partner_id = intval($_GET['partner_id']);
        $query .= " AND partnerUsers.id = $partner_id";
    }

    if (isset($_GET['product_id'])) {
        $product_id = intval($_GET['product_id']);
        $query .= " AND product.id = $product_id";
    }

    if (isset($_GET['paymentDate']) && is_array($_GET['paymentDate'])) {
        $dates = array_map(function ($date) use ($db) {
            return "'" . $db->real_escape_string($date) . "'";
        }, $_GET['paymentDate']);
        $datesList = implode(',', $dates);
        $query .= " AND DATE(FROM_UNIXTIME(avangard.tm)) IN ($datesList)";
    }

    if (isset($_GET['utm_source'])) {
        $utm_source = $db->real_escape_string($_GET['utm_source']);
        $query .= " AND utm.utm_source = '$utm_source'";
    }

    if (isset($_GET['utm_medium'])) {
        $utm_medium = $db->real_escape_string($_GET['utm_medium']);
        $query .= " AND utm.utm_medium = '$utm_medium'";
    }

    if (isset($_GET['utm_campaign'])) {
        $utm_campaign = $db->real_escape_string($_GET['utm_campaign']);
        $query .= " AND utm.utm_campaign = '$utm_campaign'";
    }

    if (isset($_GET['utm_content'])) {
        $utm_content = $db->real_escape_string($_GET['utm_content']);
        $query .= " AND utm.utm_content = '$utm_content'";
    }

    if (isset($_GET['utm_term'])) {
        $utm_term = $db->real_escape_string($_GET['utm_term']);
        $query .= " AND utm.utm_term = '$utm_term'";
    }

    $query .= " AND avangard.res = 1";


    // print_r($query);


    $result = $db->query($query);

    $data = [];
    while ($row = $db->fetch_assoc($result)) {
        $data[] = $row;
    }
    
    // Return as JSON
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    
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

    $joins = [
        "JOIN cards ON avangard.vk_uid = cards.uid",
        "LEFT JOIN product ON avangard.product_id = product.id",
        "LEFT JOIN razdel ON cards.razdel = razdel.id",
        "LEFT JOIN tags_op ON cards.uid = tags_op.uid",
        "LEFT JOIN tags ON tags_op.tag_id = tags.id",
        "LEFT JOIN users AS managerUsers ON cards.id = managerUsers.klid AND managerUsers.access_level = 4",
        "LEFT JOIN users AS partnerUsers ON cards.user_id = partnerUsers.id",
        "LEFT JOIN (
            SELECT uid, 
                   MAX(utm_source) AS utm_source, 
                   MAX(utm_medium) AS utm_medium,
                   MAX(utm_campaign) AS utm_campaign,
                   MAX(utm_content) AS utm_content,
                   MAX(utm_term) AS utm_term
            FROM utm
            GROUP BY uid
        ) AS aggregated_utm ON cards.uid = aggregated_utm.uid"
    ];
    

    if (!empty($utm_source)) {
        $utmSourceEscaped = $db->real_escape_string($utm_source);
        $whereConditions[] = "aggregated_utm.utm_source = '$utmSourceEscaped'";
    } 
    if (!empty($utm_medium)) {
        $utmMediumEscaped = $db->real_escape_string($utm_medium);
        $whereConditions[] = "aggregated_utm.utm_medium = '$utmMediumEscaped'";
    }

    if (!empty($utm_campaign)) {
        $utmCampaignEscaped = $db->real_escape_string($utm_campaign);
        $whereConditions[] = "aggregated_utm.utm_campaign = '$utmCampaignEscaped'";
    }

    if (!empty($utm_content)) {
        $utmContentEscaped = $db->real_escape_string($utm_content);
        $whereConditions[] = "aggregated_utm.utm_content = '$utmContentEscaped'";
    }

    if (!empty($utm_term)) {
        $utmTermEscaped = $db->real_escape_string($utm_term);
        $whereConditions[] = "aggregated_utm.utm_term = '$utmTermEscaped'";
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
    $joinClause = implode(" ", $joins);

    $query = "SELECT 
            DISTINCT avangard.order_number,
            DATE(FROM_UNIXTIME(avangard.tm)) as paymentDate,
            avangard.amount,
            cards.uid,
            razdel.razdel_name,
            razdel.id as razdel_id,
            tags.tag_name,
            tags.id as tag_id,
            managerUsers.real_user_name AS manager,
            cards.id AS manager_id,
            partnerUsers.real_user_name AS partner,
            cards.user_id AS partner_id,
            product.descr AS product_description,
            product.id AS product_id,
            aggregated_utm.utm_source,
            aggregated_utm.utm_medium,
            aggregated_utm.utm_campaign,
            aggregated_utm.utm_content,
            aggregated_utm.utm_term
        FROM avangard
        $joinClause
        WHERE $whereClause
        AND avangard.res = 1
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