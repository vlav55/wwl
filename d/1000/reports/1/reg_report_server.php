<?php
include "/var/www/vlav/data/www/wwl/inc/db.class.php";
chdir("..");
include "init.inc.php";
$db=new db($database);

header('Content-Type: application/json');

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
    $razdel_result =$db->query($razdel_query);
    $razdel_options = [];
    while ($row = $db->fetch_assoc($razdel_result)) {
        $razdel_options[] = [
            'id' => $row['id'],
            'name' => $row['razdel_name']
        ];
    }


    // Fetch Tags options
    $tags_query = "SELECT id, tag_name FROM tags"; 
    $tags_result =$db->query($tags_query);
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
    $manager_result =$db->query($manager_query);
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
    $partner_result =$db->query($partner_query);
    $partner_options = [];
    while ($row = $db->fetch_assoc($partner_result)) {
        $partner_options[] = [
            'id' => $row['partner_id'],
            'name' => $row['partner_name']
        ];
    }


    // Fetch earliest payment date from avangard
    $earliest_registration_query = "SELECT MIN(DATE(FROM_UNIXTIME(tm))) as earliest_date FROM msgs WHERE source_id=12";
    $earliest_registration_result =$db->query($earliest_registration_query);
    $earliest_registration_data = $db->fetch_assoc($earliest_registration_result);
    $earliest_date = $earliest_registration_data['earliest_date'];

    // Return the data as JSON
    echo json_encode([
        'utm' => $utm_options,
        'razdel' => $razdel_options,
        'tags' => $tags_options,
        'manager' => $manager_options,
        'partner' => $partner_options,
        // 'product' => $product_options,
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
    $utm_source = $_POST['utm_source'];
    $utm_medium = $_POST['utm_medium'];
    $utm_campaign = $_POST['utm_campaign'];
    $utm_content = $_POST['utm_content'];
    $utm_term = $_POST['utm_term'];





    $whereConditions = [
        "DATE(FROM_UNIXTIME(msgs.tm)) BETWEEN '$startDate' AND '$endDate'",
        "msgs.source_id=12"
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
        $whereConditions[] = "tags_op.tag_id = '$tagsEscaped'";
    }
    if (!empty($manager)) {
        $managerEscaped = $db->real_escape_string($manager);
        $whereConditions[] = "managerUsers.klid = '$managerEscaped' AND managerUsers.access_level = 4";
    }
    if (!empty($partner)) {
        $partnerEscaped = $db->real_escape_string($partner);
        $whereConditions[] = "cards.user_id = '$partnerEscaped'";
    }


    $joins = [
        "JOIN cards ON msgs.uid = cards.uid",
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

    $whereClause = implode(" AND ", $whereConditions);
    $joinClause = implode(" ", $joins);

    // Report by Registration Date (tm):
    $queryByDate = "
        SELECT 
            DATE(FROM_UNIXTIME(msgs.tm)) AS registrationDate,
            COUNT(DISTINCT msgs.uid) AS distinct_uid_count,
            COUNT(msgs.uid) AS total_uid_count
        FROM msgs
        $joinClause
        WHERE $whereClause
        GROUP BY DATE(FROM_UNIXTIME(msgs.tm))
        ORDER BY DATE(FROM_UNIXTIME(msgs.tm)) DESC
    ";

    // Report by Tags:
    $queryTags = "
        SELECT 
            tags_op.tag_id,
            tags.tag_name,
            COUNT(DISTINCT msgs.uid) AS distinct_uid_count,
            COUNT(msgs.uid) AS total_uid_count
        FROM msgs
        $joinClause
        WHERE $whereClause
        GROUP BY tags_op.tag_id, tags.tag_name
    ";

    // Report by Managers:
    $queryManagers = "
        SELECT 
            managerUsers.klid AS manager_id,
            managerUsers.real_user_name AS manager,
            COUNT(DISTINCT msgs.uid) AS distinct_uid_count,
            COUNT(msgs.uid) AS total_uid_count
        FROM msgs
        $joinClause
        WHERE $whereClause
        GROUP BY managerUsers.klid, managerUsers.real_user_name
    ";

    // Report by Partners:
    $queryPartners = "
        SELECT 
            partnerUsers.id AS partner_id,
            partnerUsers.real_user_name AS partner,
            COUNT(DISTINCT msgs.uid) AS distinct_uid_count,
            COUNT(msgs.uid) AS total_uid_count
        FROM msgs
        $joinClause
        WHERE $whereClause
        GROUP BY partnerUsers.id, partnerUsers.real_user_name
    ";

    // Report by Razdels:
    $queryRazdels = "
        SELECT 
            razdel.id AS razdel_id,
            razdel.razdel_name,
            COUNT(DISTINCT msgs.uid) AS distinct_uid_count,
            COUNT(msgs.uid) AS total_uid_count
        FROM msgs
        $joinClause
        WHERE $whereClause
        GROUP BY razdel.id, razdel.razdel_name
    ";

    // Report by UTM Sources:
    $queryUTMSources = "
        SELECT 
            aggregated_utm.utm_source,   
            COUNT(DISTINCT msgs.uid) AS distinct_uid_count,
            COUNT(msgs.uid) AS total_uid_count
        FROM msgs
        $joinClause
        WHERE $whereClause
        GROUP BY aggregated_utm.utm_source
    ";
    $queryUTMMediums = "
        SELECT 
            aggregated_utm.utm_medium,   
            COUNT(DISTINCT msgs.uid) AS distinct_uid_count,
            COUNT(msgs.uid) AS total_uid_count
        FROM msgs
        $joinClause
        WHERE $whereClause
        GROUP BY aggregated_utm.utm_medium
    ";
    $queryUTMCampaigns = "
        SELECT 
            aggregated_utm.utm_campaign,   
            COUNT(DISTINCT msgs.uid) AS distinct_uid_count,
            COUNT(msgs.uid) AS total_uid_count
        FROM msgs
        $joinClause
        WHERE $whereClause
        GROUP BY aggregated_utm.utm_campaign
    ";
    $queryUTMContents = "
        SELECT 
            aggregated_utm.utm_content,   
            COUNT(DISTINCT msgs.uid) AS distinct_uid_count,
            COUNT(msgs.uid) AS total_uid_count
        FROM msgs
        $joinClause
        WHERE $whereClause
        GROUP BY aggregated_utm.utm_content
    ";
    $queryUTMTerms = "
        SELECT 
            aggregated_utm.utm_term,   
            COUNT(DISTINCT msgs.uid) AS distinct_uid_count,
            COUNT(msgs.uid) AS total_uid_count
        FROM msgs
        $joinClause
        WHERE $whereClause
        GROUP BY aggregated_utm.utm_term
    ";


    
    $queries = [
        "dates" => $queryByDate,
        "tags" => $queryTags,
        "managers" => $queryManagers,
        "partners" => $queryPartners,
        "razdels" => $queryRazdels,
        "utm_sources" => $queryUTMSources,
        "utm_mediums" => $queryUTMMediums,
        "utm_campaigns" => $queryUTMCampaigns,
        "utm_contents" => $queryUTMContents,
        "utm_terms" => $queryUTMTerms,
    ];
    
    $results = [];
    
    foreach ($queries as $key => $query) {
        $data = [];
        $result = $db->query($query);
        while ($row = $db->fetch_assoc($result)) {
            $data[] = $row;
        }
        $results[$key] = $data;
    }
    
    echo json_encode($results);
}
?>
