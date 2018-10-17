<link rel="stylesheet" type="text/css" href="style.css">
<?php
/*
wiguna@dinustech.com
18 June 2015
*/
/**/

/*Dapatkan pesan yang dikirim dari form pertama*/
$pesan = $_POST['pesan'];
$panjang_pesan = strlen($pesan);
$array_pesan = '';
$x = array();
for($i = 0 ; $i < $panjang_pesan ; $i++){
//    echo substr($pesan,$i,1).'<br>';
	$bin = sprintf( "%08d", decbin(ord(substr($pesan,$i,1)))); //konversi huruf per huruf pesan ke dalam bentuk biner 8 angka
	//$array_pesan[] = $bin; //tambahkan biner di atas ke list array_pesan
	$array_pesan .= $bin;
    $x[] = $bin;
}
//echo '<pre>';
//print_r($x);
//echo '</pre>';
//die();
$array_pesan = str_split($array_pesan , 3); //konversi binary pesan ke array beranggota 3 angka
echo 'Pesan asli : ';
echo $pesan.'<br>';
echo 'Konversi ke biner : <br>';
echo '<pre>';
print_r($array_pesan);
echo '</pre>';

/*ambil gambar yang namanya images.jpg */
$image = imagecreatefromjpeg('awan.jpg'); 

/*ambil panjang gambar X */
$width = imagesx($image);

/*ambil tinggi gambar Y */
$height = imagesy($image);

/*deklarasikan array penampung biner gambar X */
$colors = array();
$colors_hasil = array();
$pointer = 0;

$desimal_pixel_asli = array();
$desimal_pixel_diubah = array();

/*looping sebanyak tinggi gambar */
for ($y = 0; $y < $height; $y++) {
	$y_array = array() ;
	$y_array_hasil = array() ;

	/*looping sebanyak lebar gambar (ambil piksel dari kiri ke kanan) */
	for ($x = 0; $x < $width; $x++) {
	    $rgb = imagecolorat($image, $x, $y); //ambil nilai desimal gambar asli untuk ukuran 1 pixel di kordinat x , y 
	    $r = ($rgb >> 16) & 0xFF; //ubah nilai HEX RED ke format decimal
	    $g = ($rgb >> 8) & 0xFF; //ubah nilai HEX GREEN ke format decimal
	    $b = $rgb & 0xFF;  //ubah nilai HEX BLUE ke format decimal

	    /*masukkan nilai RGB dari pixel tersebut ke variabel x_array, 
	    sebelumnya ubah nilai dec RGB tersebut ke biner pakai fungsi decbin*/
	    $x_array = array(decbin($r), decbin($g), decbin($b)) ; 

        $desimal_pixel_asli[] = $rgb; //masukkan desimal warna asli buat menghitung PNSR nanti
	    /*ambil array_pesan kumudian lakukan operasi bitwise dengan binary BLUE*/
	    if(isset($array_pesan[$pointer])){
		    //$x_array_hasil = array(decbin($r), decbin($g), (sprintf( "%08d", $array_pesan[$pointer])|decbin($b) )  ) ; 	    	
		    $x_array_hasil = array(decbin($r), decbin($g),  substr(decbin($b),0,5).$array_pesan[$pointer]  ) ; 	    	
            $hexR = dechex(bindec( decbin($r)));
            $hexG = dechex(bindec( decbin($g)));
            $hexB = dechex(bindec( substr(decbin($b),0,5).$array_pesan[$pointer]));            
            $desimal_pixel_diubah[] = hexdec($hexR.$hexG.$hexB); //masukkan desimal warna asli buat menghitung PNSR nanti, ubah HEX ke decimal
            
	    }else{
		    $x_array_hasil = array(decbin($r), decbin($g), decbin($b)); 	    	
            $desimal_pixel_diubah[] = $rgb; //masukkan desimal warna asli buat menghitung PNSR nanti            
	    }

	    $y_array[] = $x_array ; //turun ke baris pixel di bawahnya GAMBAR ASLI
	    $y_array_hasil[] = $x_array_hasil ; //turun ke baris pixel di bawahnya GAMBAR HASIL STEGANO
	    $pointer++; //pointer makin maju
	} 
	$colors[] = $y_array ; //masukkan binary RGB per baris ke variabe; $colors GAMBAR ASLI
	$colors_hasil[] = $y_array_hasil ; //masukkan binary RGB per baris ke variabe; $colors GAMBAR HASIL ENKRIPSI
}
?>


<div class="col-md-12">
	<div class="col-md-6">
		<h4>Gambar sebelum enkripsi</h4>
		<?php
		echo '<pre>';
		print_r($colors);
		echo '</pre>';
		?>
	</div>
	<div class="col-md-6">
		<h4>Gambar sesudah enkripsi</h4>
		<?php
		echo '<pre>';
		print_r($colors_hasil);
		echo '</pre>';
		?>
	</div>

</div>

<?php
$width = count($colors_hasil[0]); // Dapatkan panjang binary (gambar)
$height = count($colors_hasil); // Dapatkan tinggi binary (gambar)
$img = imagecreatetruecolor($width, $height); //instansiasi object gambar

// looping sebanyak tinggi gambar
for ($y = 0; $y < $height; ++$y) {
	// looping sebanyak lebar gambar
    for ($x = 0; $x < $width; ++$x) {
    	//kembalikan nilai binary RGB ke Decimal RGB kemudian convert gabungan R dan G dan B ke titik warna bervariabel $titik
		$titik = imagecolorallocate($img, bindec($colors_hasil[$y][$x][0]), bindec($colors_hasil[$y][$x][1]), bindec($colors_hasil[$y][$x][2]) );     	
		
		/*tanamkan titik piksel di atas ke dalam calon gambar*/		
        imagesetpixel($img, $x, $y, $titik);
    }
}


@header('Content-Type: image/png'); // Lempar image ke browser
@unlink('hasil.jpg');
imagepng($img,'hasil.jpg'); // Simpan gambar baru dengan nama hasil.jpg
imagedestroy($img) ; //bersihkan semua kenangan...eh semua memory

//HITUNG MSE DISINI, Nah Ini yang krusial
$pointer = 0;
$pembilang = 0; //deklarasikan pembilang
foreach($desimal_pixel_asli as $desimal_asli){
    $pembilang += pow(($desimal_asli - $desimal_pixel_diubah[$pointer]),2);//(decimal pixel awal - decimal pixel diubah) dipangkat 2, fungsi pow untuk pangkat
    $pointer++ ; //pointer makin maju
}
$mse = $pembilang / ($width * $height); //pembilang dibagi luas image
//HITUNG MSE berakhir disini

//MULAI HITUNG PNSR
$pnsr =( 20 * log10(max($desimal_pixel_asli))) - (10 * log10($mse) );  //tak padakna laporan, semoga bener..hehe
//SELESAI HITUNG PNSR
?>
<h3>Nilai MSE = <?=$mse?></h3>
<h3>Nilai PNSR = <?=$pnsr?></h3>
<div class="col-md-12 clearfix" style="margin : 10px 0 0 0;">
	<div class="col-md-6">
		<div class="col-md-6">
			<h3>Sebelum Penyisipan Pesan</h3>
			<img src="awan.jpg">
		</div>
	</div>
	<div class="col-md-6">
		<div class="col-md-6">
			<h3>sESUDAH Penyisipan Pesan</h3>
			<img src="hasil.jpg">
		</div>
	</div>
</div>
