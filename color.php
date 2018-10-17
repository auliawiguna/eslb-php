<link rel="stylesheet" type="text/css" href="style.css">
<?php
/*
wiguna@dinustech.com
18 June 2015
*/
/**/

/*Dapatkan pesan yang dikirim dari form pertama*/
$pesan = $_POST['pesan'];
$warna = $_POST['warna']; //NEW

$panjang_pesan = strlen($pesan);
$array_pesan = null;
$x = array();
for($i = 0 ; $i < $panjang_pesan ; $i++){
	$bin = sprintf( "%08d", decbin(ord(substr($pesan,$i,1)))); //konversi huruf per huruf pesan ke dalam bentuk biner 8 angka
	$array_pesan .= $bin;
    $x[] = $bin;
}
$array_pesan = str_split($array_pesan , 3); //konversi binary pesan ke array beranggota 3 angka
echo 'Pesan asli : ';
echo $pesan.'<br>';
echo 'Disisipkan di warna : ';
echo $warna.'<br>';
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


/*NEW*/
$r_asli = array();
$g_asli = array();
$b_asli = array();
$r_ubah = array();
$g_ubah = array();
$b_ubah = array();
/*NEW*/

$matrix_asli = array();
$matrix_ubah = array();

$pointer = 0;
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
        $r_asli[] = $r;
        $g_asli[] = $g;
        $b_asli[] = $b;
        $matrix_asli[] = array('R' => $r, 'G' => $g , 'B' => $b);
	    $x_array = array(decbin($r), decbin($g), decbin($b)) ; 

        $desimal_pixel_asli[] = $rgb; //masukkan desimal warna asli buat menghitung PNSR nanti
	    /*ambil array_pesan kumudian lakukan operasi bitwise dengan binary BLUE*/
	    if(isset($array_pesan[$pointer])){
		    //$x_array_hasil = array(decbin($r), decbin($g), (sprintf( "%08d", $array_pesan[$pointer])|decbin($b) )  ) ; 	    	
            if($warna == 'r'){
                /*misal disisipin ke Red*/
                $x_array_hasil = array(substr(decbin($r),0,5).$array_pesan[$pointer], decbin($g),  decbin($b)  ) ; 	    	
                $hexR = dechex(bindec( substr(decbin($r),0,5).$array_pesan[$pointer]));
                $hexG = dechex(bindec( decbin($g)));
                $hexB = dechex(bindec( decbin($b)));
                $r_ubah[] = hexdec($hexR);
                $g_ubah[] = hexdec($hexG);
                $b_ubah[] = hexdec($hexB);
                $matrix_ubah[] = array('R sisip' => hexdec($hexR) , 'G sisip' => hexdec($hexG) , 'B sisip' => hexdec($hexB));
                $desimal_pixel_diubah[] = hexdec($hexR.$hexG.$hexB); //masukkan desimal warna asli buat menghitung PNSR nanti, ubah HEX ke decimal                                
            }
            if($warna == 'g'){
                /*misal disisipin ke Green*/
                $x_array_hasil = array(decbin($r), substr(decbin($g),0,5).$array_pesan[$pointer],  decbin($b)  ) ; 	    	
                $hexR = dechex(bindec( decbin($r)));
                $hexG = dechex(bindec( substr(decbin($g),0,5).$array_pesan[$pointer]));
                $hexB = dechex(bindec( decbin($b)));            
                $r_ubah[] = hexdec($hexR);
                $g_ubah[] = hexdec($hexG);
                $b_ubah[] = hexdec($hexB);
                $matrix_ubah[] = array('R sisip' => hexdec($hexR) , 'G sisip' => hexdec($hexG) , 'B sisip' => hexdec($hexB));
                $desimal_pixel_diubah[] = hexdec($hexR.$hexG.$hexB); //masukkan desimal warna asli buat menghitung PNSR nanti, ubah HEX ke decimal                
            }
            if($warna == 'b'){
                /*misal disisipin ke Blue*/
                $x_array_hasil = array(decbin($r), decbin($g),  substr(decbin($b),0,5).$array_pesan[$pointer]  ) ; 	    	
                $hexR = dechex(bindec( decbin($r)));
                $hexG = dechex(bindec( decbin($g)));
                $hexB = dechex(bindec( substr(decbin($b),0,5).$array_pesan[$pointer]));            
                $r_ubah[] = hexdec($hexR);
                $g_ubah[] = hexdec($hexG);
                $b_ubah[] = hexdec($hexB);
                $matrix_ubah[] = array('R sisip' => hexdec($hexR) , 'G sisip' => hexdec($hexG) , 'B sisip' => hexdec($hexB));
                $desimal_pixel_diubah[] = hexdec($hexR.$hexG.$hexB); //masukkan desimal warna asli buat menghitung PNSR nanti, ubah HEX ke decimal                
            }
            
	    }else{
		    $x_array_hasil = array(decbin($r), decbin($g), decbin($b)); 	    	
            $desimal_pixel_diubah[] = $rgb; //masukkan desimal warna asli buat menghitung PNSR nanti            
            $r_ubah[] = $r;
            $g_ubah[] = $g;
            $b_ubah[] = $b;
            $matrix_ubah[] = array('R' => $r, 'G' => $g , 'B' => $b);            
	    }

	    $y_array[] = $x_array ; //turun ke baris pixel di bawahnya GAMBAR ASLI
	    $y_array_hasil[] = $x_array_hasil ; //turun ke baris pixel di bawahnya GAMBAR HASIL STEGANO
	    $pointer++; //pointer makin maju
	} 

	$colors[] = $y_array ; //masukkan binary RGB per baris ke variabe; $colors GAMBAR ASLI
	$colors_hasil[] = $y_array_hasil ; //masukkan binary RGB per baris ke variabe; $colors GAMBAR HASIL ENKRIPSI
}
?>


<?php
$matrix_desimal_asli = array_chunk($desimal_pixel_asli,$height);
$matrix_desimal_hasil_sisipan = array_chunk($desimal_pixel_diubah,$height);
?>


<div class="col-md-12">
	<div class="col-md-6">
		<h4>Matrix gambar sebelum penyisipan</h4>
		<?php
		echo '<pre>';
		print_r(array_chunk($matrix_asli,$width));
		echo '</pre>';
		?>
	</div>
	<div class="col-md-6">
		<h4>Matrix gambar sesudah penyisipan</h4>
		<?php
		echo '<pre>';
		print_r(array_chunk($matrix_ubah,$width));
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
    	//kembalikan nilai binary RGB ke Decimal RGB kemudian convert gaawann R dan G dan B ke titik warna bervariabel $titik
		$titik = imagecolorallocate($img, bindec($colors_hasil[$y][$x][0]), bindec($colors_hasil[$y][$x][1]), bindec($colors_hasil[$y][$x][2]) );     	
		
		/*tanamkan titik piksel di atas ke dalam calon gambar*/		
        imagesetpixel($img, $x, $y, $titik);
    }
}


@header('Content-Type: image/png'); // Lempar image ke browser
@unlink('hasil.jpg');
@imagepng($img,'hasil.jpg'); // Simpan gambar baru dengan nama hasil.jpg
@imagedestroy($img) ; //bersihkan semua kenangan...eh semua memory

//HITUNG MSE DISINI, Nah Ini yang krusial
$pointer = 0;
$pembilang = 0; //deklarasikan pembilang
$p_r = $p_g = $p_b = 0;
foreach($desimal_pixel_asli as $desimal_asli){
    $pembilang += pow(($desimal_asli - $desimal_pixel_diubah[$pointer]),2);
    $p_r += pow(($r_asli[$pointer] - $r_ubah[$pointer]),2);
    $p_g += pow(($g_asli[$pointer] - $g_ubah[$pointer]),2);
    $p_b += pow(($b_asli[$pointer] - $b_ubah[$pointer]),2);
    $pointer++ ; //pointer makin maju
}
switch($warna){
    case 'r' : $j = $p_r; break;
    case 'g' : $j = $p_g; break;
    case 'b' : $j = $p_b; break;
}

$mse = (($p_r + $p_g +$p_b) / 3) / ($width * $height); 
$pnsr =  10 * log10( pow((pow( 2 ,8)-1),2) / $mse ); //OK 2

?>
<h3>Nilai MSE = <?=number_format($mse,4)?></h3>
<h3>Nilai PNSR = <?=number_format($pnsr, 4)?></h3>
<?php
//HITUNG MSE berakhir disini
die();

?>
<div class="col-md-12 clearfix" style="margin : 10px 0 0 0;">
	<div class="col-md-6">
		<div class="col-md-6">
			<h3>Gambar Sebelum Penyisipan Pesan</h3>
			<img src="awan.jpg">
		</div>
	</div>
	<div class="col-md-6">
		<div class="col-md-6">
			<h3>Gambar Sesudah Penyisipan Pesan</h3>
			<img src="hasil.jpg">
		</div>
	</div>
</div>
