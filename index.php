
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="style.css">
		<title>ESLB</title>
	</head>
	<body>
			<div class="col-md-12 clearfix" style="margin : 10px 0 10px 0;">
				<div class="col-md-3">
					<img src="awan.jpg">
				</div>
			</div>
		<form method="POST" action="color.php">
			<div class="col-md-12">
				<div class="col-md-3">
				    Masukkan Pesan ke gambar di atas
				</div>
				<div class="col-md-4">
					<input type="text" name="pesan" class="form-control">
				</div>
			</div>
			<div class="col-md-12">
				<div class="col-md-3">
				    Disisipi di warna apa
				</div>
				<div class="col-md-4">
                    <select name='warna'>
                        <option value="r">Merah (R)</option>
                        <option value="g">Hijau (G)</option>
                        <option value="b">Biru (B)</option>
                    </select>
				</div>
				<hr>
				<div class="col-md-1"></div>
				<div class="col-md-4">
					<button type="submit" class="btn btn-primary">Kirim dan Convert Ke Gambar</button>
				</div>
			</div>
		</form>

	</body>
</html>
