<?
function s_panel_upload_file($upload_id, $upload_dir, $section_num, $max_size = 0.3, $save_original=false) {
	global $db;
	$pic_exts=['jpg','JPG','png','PNG'];
    if(isset($_POST["del_".$upload_id])) {
        $msg = '';
        $upload_file = $upload_dir . $upload_id . ".jpg";
        if(file_exists($upload_file)) {
            unlink($upload_file);
        }
        print "<script>location='?saved=yes&section=$section_num&msg=".urlencode($msg)."#section_$section_num'</script>";
        return;
    }
    
    if(isset($_POST[$upload_id])) {
        if(isset($_FILES['file_'.$upload_id]) && $_FILES['file_'.$upload_id]['error'] === UPLOAD_ERR_OK) {
			$msg = '';
			// Maximum allowed file size in bytes
			$maxFileSize = $max_size ? intval($max_size*1000000) :5000000; // 5MB
			
			// Minimum required output image width in pixels
			$minWidth = 800; // 800 pixels
			
			// Get uploaded file information
			$fileName = $_FILES['file_'.$upload_id]['name'];
			$fileType = $_FILES['file_'.$upload_id]['type'];
			$fileSize = $_FILES['file_'.$upload_id]['size'];
			$fileTmpName = $_FILES['file_'.$upload_id]['tmp_name'];
			
			// Check if file size is within the allowed limit
			if($fileSize > $maxFileSize) {
				$msg = "Error: File size is too large (maximum is 5MB)";
			}
			
			// Check if file is a valid image
			$allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/gif'];
			if(!in_array($fileType, $allowedTypes)) {
				$msg = "Error: Only PNG, JPEG and GIF images are allowed - $fileType";
			}
            if($save_original) {
				if(empty($msg)) {
				// Save original file with original extension using just $upload_id
				$original_ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
				$original_file = $upload_dir . $upload_id . '.' . $original_ext;
				foreach($pic_exts AS $ext) {
					unlink($upload_dir . $upload_id . '.'.$ext);
				}
				
				// Move the uploaded file to preserve original quality and format
				if(move_uploaded_file($fileTmpName, $original_file)) {
					// Use the saved original file for further processing
					$msg = "ok Original file saved successfully.";
				}
			}
			} else {
				if(empty($msg)) {
					// Create image resource based on file type
					switch($fileType) {
						case 'image/png':
							$image = imagecreatefrompng($fileTmpName);
							break;
						case 'image/jpeg':
						case 'image/jpg':
							$image = imagecreatefromjpeg($fileTmpName);
							break;
						case 'image/gif':
							$image = imagecreatefromgif($fileTmpName);
							break;
						default:
							$image = false;
					}
					
					if(!$image) {
						$msg = "Error creating image file";
					} else {
						// Get original dimensions
						$originalWidth = imagesx($image);
						$originalHeight = imagesy($image);
						
						// Calculate new dimensions - NEVER upscale!
						if($originalWidth > $minWidth) {
							// If image is wider than minimum, scale it down proportionally
							$scale = $minWidth / $originalWidth;
							$newWidth = $minWidth;
							$newHeight = round($originalHeight * $scale);
						} else {
							// Keep original dimensions if smaller than minimum
							$newWidth = $originalWidth;
							$newHeight = $originalHeight;
						}
						
						// Create new image
						$outputImage = imagecreatetruecolor($newWidth, $newHeight);
						
						// Preserve transparency for PNG/GIF
						if($fileType == 'image/png' || $fileType == 'image/gif') {
							imagealphablending($outputImage, false);
							imagesavealpha($outputImage, true);
							$transparent = imagecolorallocatealpha($outputImage, 255, 255, 255, 127);
							imagefilledrectangle($outputImage, 0, 0, $newWidth, $newHeight, $transparent);
						}
						
						// Resample image
						imagecopyresampled($outputImage, $image, 0, 0, 0, 0, 
							$newWidth, $newHeight, $originalWidth, $originalHeight);
						
						// Save as JPEG with quality based on file size
						$upload_file = $upload_dir . $upload_id . ".jpg";
						
						// Adjust quality to target size without upscaling
						$targetQuality = 75; // Default quality
						if($fileSize > 1000000) { // If > 1MB
							$targetQuality = 65;
						} elseif($fileSize > 500000) { // If > 500KB
							$targetQuality = 70;
						}
						
						// Save the image
						imagejpeg($outputImage, $upload_file, $targetQuality);
						
						// Clean up
						imagedestroy($image);
						imagedestroy($outputImage);
						
						// Verify the saved file
						if(file_exists($upload_file)) {
							$savedInfo = getimagesize($upload_file);
							$msg = "ok Upload successful. Saved as {$savedInfo[0]}×{$savedInfo[1]} pixels.";
						} else {
							$msg = "Error saving file.";
						}
					}
				}
			}
			print "<script>location='?saved=yes&section=$section_num&msg=".urlencode($msg)."#section_$section_num'</script>";
        }
    }
    
    // Display existing image or placeholder
	foreach($pic_exts AS $ext) {
		$pic=$upload_dir . $upload_id . '.'.$ext;
		if(file_exists($pic))
			break;
	}
    //$pic = $upload_dir . $upload_id . '.jpg';
	$rnd = rand(1000000, 999999999);
    if(file_exists($pic)) {
        $preview = "<img src='$pic?n=$rnd' class='img-thumbnail img-fluid' style='max-width: 300px; height: auto;' alt=''>";
    } else {
        $preview = "<span class='badge badge-warning'>не загружено</span>";
    }
    ?>
    <div><?= $preview ?></div>
    <form method="POST" action="#section_<?= $section_num ?>" enctype="multipart/form-data">
        <label for="file_<?= $upload_id ?>">Выберите файл (jpg,png,менее <?= $max_size ?>Mb):</label>
        <input type="file" name="file_<?= $upload_id ?>" id="file_<?= $upload_id ?>" style='width:100%;'>
        <input type='hidden' name='section' value='<?= $section_num ?>'>
        <button type="submit" name='<?= $upload_id ?>' value="yes" class='btn btn-sm btn-secondary'>Выгрузить</button>
        <button type="submit" name='del_<?= $upload_id ?>' value="yes" class='btn btn-sm btn-secondary'>Удалить</button>
    </form>
    <?php
}

function s_panel_upload_file___($upload_id,$upload_dir,$section_num,$max_size=0.3) {
	if(isset($_POST["del_".$upload_id])) {
		$msg='';
		$upload_file = $upload_dir . $upload_id.".jpg";
		unlink($upload_file);
		print "<script>location='?saved=yes&section=$section_num&msg=".urlencode($msg)."#section_$section_num'</script>";
	}
	if(isset($_POST[$upload_id])) {
		$msg='';
		if (isset($_FILES['file_'.$upload_id]) && $_FILES['file_'.$upload_id]['error'] === UPLOAD_ERR_OK) {
			// Maximum allowed file size in bytes
			$maxFileSize = 5000000; // 5MB

			// Minimum required output file size in bytes
			$minFileSize = 200000; // 200KB

			// Minimum required output image width in pixels
			$minWidth = 800; // 800 pixels

			// Get uploaded file information
			$fileName = $_FILES['file_'.$upload_id]['name'];
			$fileType = $_FILES['file_'.$upload_id]['type'];
			$fileSize = $_FILES['file_'.$upload_id]['size'];
			$fileTmpName = $_FILES['file_'.$upload_id]['tmp_name'];

			// Check if file size is within the allowed limit
			if ($fileSize > $maxFileSize) {
				$msg= "Error: File size is too large (maximum is 5MB)";
				//exit;
			}

			// Check if file is a valid image
			if (!in_array($fileType, array('image/png', 'image/jpeg', 'image/gif'))) {
				$msg= "Error: Only PNG, JPEG and GIF images are allowed - $fileType";
				//exit;
			}

			// Create a new image resource from the uploaded file
			if ($fileType == 'image/png') {
				$image = imagecreatefrompng($fileTmpName);
			} else if ($fileType == 'image/jpeg') {
				$image = imagecreatefromjpeg($fileTmpName);
			} else if ($fileType == 'image/gif') {
				$image = imagecreatefromgif($fileTmpName);
			}
			if(!$image) {
				$msg="Error creating image file";
			}
			if(empty($msg)) {

				// Get the dimensions of the original image
				$originalWidth = imagesx($image);
				$originalHeight = imagesy($image);

				// Calculate the scaling ratio required to reach the minimum output file size and minimum output image width
				$scalingRatio = max(sqrt($minFileSize / $fileSize), $minWidth / $originalWidth);

				// Calculate the new dimensions of the scaled image
				if($originalWidth>$minWidth) {
					$newWidth = round($originalWidth * $scalingRatio);
					$newHeight = round($originalHeight * $scalingRatio);
				} else {
					$newWidth = $originalWidth;
					$newHeight = $originalHeight;
				}

				// Create a new image resource with the scaled dimensions
				$outputImage = imagecreatetruecolor($newWidth, $newHeight);

				// Scale the original image to fit the new dimensions
				imagecopyresampled($outputImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

				// Save the scaled image as a JPEG file with 90% quality
				$upload_file = $upload_dir . $upload_id.".jpg";
				imagejpeg($outputImage, $upload_file, 75);

				// Free up memory by destroying the image resources
				imagedestroy($image);
				imagedestroy($outputImage);
			}
		}
		print "<script>location='?saved=yes&section=$section_num&msg=".urlencode($msg)."#section_$section_num'</script>";
	}
	$pic=$upload_dir . $upload_id.'.jpg';
	$rnd=rand(1000000,999999999);
	$preview=(file_exists($pic))?"<img src='$pic?n=$rnd' class='img-thumbnail img-responsive' width='300' alt=''>":"<span class='badge badge-warning' >не загружено</span>";
	?>
	<div><?=$preview?></div>
<!--
	<form id="f_<?=$upload_id?>" action="#section_<?=$section_num?>" method="POST" enctype="multipart/form-data">
-->
	  <label for="file_<?=$upload_id?>">Выберите файл (jpg,png,менее <?=$max_size?>Mb):</label>
	  <input type="file" name="file_<?=$upload_id?>" id="file_<?=$upload_id?>" style='width:100%;'>
	  <input type='hidden' name='section' value='<?=$section_num?>'>
	  <button type="submit"  name='<?=$upload_id?>' value="yes" class='btn btn-sm btn-secondary' >Выгрузить</button>
	  <button type="submit"  name='del_<?=$upload_id?>' value="yes" class='btn btn-sm btn-secondary' >Удалить</button>
<!--
	</form>
-->
	<?
}

?>
