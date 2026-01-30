<?
require "/var/www/vlav/data/www/wwl/inc/s3/vendor/autoload.php";
 
use Aws\S3\S3Client;
use Aws\Credentials\Credentials;
use Aws\S3\Exception\S3Exception;
class s3 {
	var $s3_client;
	function __construct($key) {
		$credentials['yogahelpyou']=[
						"key"	=> "1f7cfef5bd48487aa5042dba18c34e1a",
						"secret" => "98789b6aea58449dba2290e94e27aea1",
					   ];
		$this->s3_client = new S3Client([
		   "version" 	=> "latest",
		   "region"  	=> "ru-1",
		   "use_path_style_endpoint" => true,
		   "credentials" => $credentials[$key],
		   "endpoint" => "https://s3.storage.selcloud.ru"
		]);
	}
	function list_folders($bucket) {
		$objects = [];
		$nextToken = null;

		do {
			$params = [
				'Bucket' => $bucket,
				'MaxKeys' => 1000, // Maximum number of objects to fetch in a single request
			];

			if ($nextToken) {
				$params['ContinuationToken'] = $nextToken;
			}

			try {
				$result = $this->s3_client->listObjectsV2($params);
				
				$objects = array_merge($objects, $result['Contents']);
				
				$nextToken = $result['NextContinuationToken'] ?? null;
			} catch (AwsException $e) {
				echo $e->getMessage() . "<br>";
				break;
			}
		} while ($nextToken !== null);

		//print "HERE_".sizeof($objects);
		$arr=[];
		foreach ($objects as $object) {
			$dir=dirname($object['Key']);
			if(!in_array($dir,$arr) && $dir!='.')
				$arr[]= dirname($object['Key']);
		}
		//print nl2br(print_r($arr,true));
		return $arr;
	}
}

?>
