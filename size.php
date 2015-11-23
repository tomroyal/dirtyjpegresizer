<?php
	
// quick and dirty image resizer
// expects an input directory, inside which are more directories, each containing jpegs
// can either scan them all by calling walkdirs($indir), or use SQLi to selectively process some dirs by name	
	
$servername = "localhost";
$username = "";
$password = "";
$dbname = "";

$indir = "./infiles/";
$outdir = 'out';
$target = 400;	

// Create connection
$conn = mysqli_connect($servername, $username, $password);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_select_db($conn,$dbname);

// done

// exception handler so IM errors don't stop execution

function tomexcep ($excep){
	// var_dump($excep);
}

set_exception_handler('tomexcep');

function resizeit($imgpath,$imgname){

	global $outdir;
	global $target;
		
	try {
		$im = new imagick("./".$imgpath."/".$imgname);
		$imageprops = $im->getImageGeometry();
		$width = $imageprops['width'];
		$height = $imageprops['height'];
		if($width > $height){
		    $newHeight = $target;
		    $newWidth = ($target / $height) * $width;
		}else{
		    $newWidth = $target;
		    $newHeight = ($target / $width) * $height;
		}
		$im->resizeImage($newWidth,$newHeight, imagick::FILTER_LANCZOS, 0.9, true);
		$im->writeImage( "./".$outdir."/th_".$imgname );
	} catch (ImagickException $e){
		 echo ('Error on '.$imgpath.'/'.$imgname."\r\n");
	}		
	//echo ('wrote th_'.$imgname."\r\n");
}

function checkpics($inpath){
	// parse dir inpath, resize all *.j* files
	$dirhandle2 = opendir($inpath);
	while ($file2 = readdir($dirhandle2)){
		if (is_file($inpath.'/'.$file2)){
			// echo ("file ".$file2);	
			if(strpos(strtolower($file2),'.j') === FALSE){
				// not a jpeg
			}
			else {
				resizeit($inpath,$file2);
			}
			
		};
	
	}
	
}

function walkdirs($indir){
	
	// walk dir $indir, send all subfolders to checkpics
	$dirhandle = opendir($indir);
	while ($file = readdir($dirhandle)){
		if(is_dir($indir . $file) && $file != '.' && $file != ".."){
			// $file is a folder
			// look inside it
			checkpics($indir.$file);	
		}
		else {
			
		};
		
	}
	
};

// SQLi usage

$q1 = "SELECT * FROM "; //  your query here!
$r1 = $conn->query($q1);
while ($row1 = $r1->fetch_assoc()) {
	$workingid =  $row1['id'];
	echo ('Processing '.$indir.$workingid."\r\n"); 
	checkpics($indir.$workingid); // resize images in /inputdir/id/
};


// or process all:

// walkdirs($indir);

mysql_close($conn);

?>