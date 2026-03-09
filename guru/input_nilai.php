<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'guru'){
    header("Location: ../auth/login.php");
    exit;
}

if(isset($_POST['update'])){

    $id       = $_POST['id'];
    $tugas    = $_POST['tugas'];
    $uts      = $_POST['uts'];
    $uas      = $_POST['uas'];

    if($tugas < 0 || $tugas > 100 ||
       $uts < 0 || $uts > 100 ||
       $uas < 0 || $uas > 100){

        echo "<script>alert('Nilai harus antara 0 - 100');</script>";
    } else {

        $rata = ($tugas * 0.20) + ($uts * 0.40) + ($uas * 0.40);

        mysqli_query($conn,"
        UPDATE nilai 
        SET tugas='$tugas',
            uts='$uts',
            uas='$uas',
            rata_rata='$rata'
        WHERE id='$id'
        ");
    }
}

/* =====================
   INSERT DATA
===================== */
if(isset($_POST['simpan'])){

    $siswa_id = $_POST['siswa_id'];
    $mapel_id = $_POST['mapel_id'];
    $tugas    = $_POST['tugas'];
    $uts      = $_POST['uts'];
    $uas      = $_POST['uas'];

    // VALIDASI RANGE
    if($tugas < 0 || $tugas > 100 ||
       $uts < 0 || $uts > 100 ||
       $uas < 0 || $uas > 100){

        echo "<script>alert('Nilai harus antara 0 - 100');</script>";
    } else {

        // CEK DUPLIKAT
        $cek = mysqli_query($conn,"
            SELECT * FROM nilai 
            WHERE siswa_id='$siswa_id' 
            AND mapel_id='$mapel_id'
        ");

        if(mysqli_num_rows($cek) > 0){
            echo "<script>alert('Nilai siswa untuk mapel ini sudah ada!');</script>";
        } else {

            $rata = ($tugas * 0.20) + ($uts * 0.40) + ($uas * 0.40);

            mysqli_query($conn,"
            INSERT INTO nilai (siswa_id,mapel_id,tugas,uts,uas,rata_rata)
            VALUES ('$siswa_id','$mapel_id','$tugas','$uts','$uas','$rata')
            ");
        }
    }
}

/* =====================
   DELETE DATA
===================== */
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    mysqli_query($conn,"DELETE FROM nilai WHERE id='$id'");
}

/* =====================
   QUERY DATA
===================== */
$data = mysqli_query($conn,"
SELECT nilai.*, siswa.nama AS nama_siswa, mata_pelajaran.nama_mapel
FROM nilai
JOIN siswa ON nilai.siswa_id = siswa.id
JOIN mata_pelajaran ON nilai.mapel_id = mata_pelajaran.id
");

$edit_data = null;

if(isset($_GET['edit'])){
    $id = $_GET['edit'];
    $q = mysqli_query($conn,"SELECT * FROM nilai WHERE id='$id'");
    $edit_data = mysqli_fetch_assoc($q);
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Input Nilai</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">

<h3>Input Nilai Siswa</h3>

<!-- FORM -->
<div class="card p-3 mb-4">
<form method="POST">

<div class="row">

<div class="col-md-3">
<select name="siswa_id" class="form-select" required>
<option value="">-- Pilih Siswa --</option>
<?php
$siswa = mysqli_query($conn,"SELECT * FROM siswa");
while($s = mysqli_fetch_assoc($siswa)){
    echo "<option value='$s[id]'>$s[nama]</option>";
}
?>
</select>
</div>

<div class="col-md-3">
<select name="mapel_id" class="form-select" required>
<option value="">-- Pilih Mapel --</option>
<?php
$mapel = mysqli_query($conn,"SELECT * FROM mata_pelajaran");
while($m = mysqli_fetch_assoc($mapel)){
    echo "<option value='$m[id]'>$m[nama_mapel]</option>";
}
?>
</select>
</div>

<div class="col-md-2">
<input type="number" 
       name="tugas" 
       class="form-control"
       placeholder="Tugas"
       min="0"
       max="100"
       value="<?= $edit_data['tugas'] ?? '' ?>" 
       required>
</div>

<div class="col-md-2">
<input type="number" 
       name="uts" 
       class="form-control"
       placeholder="UTS"
       min="0"
       max="100"
       value="<?= $edit_data['uts'] ?? '' ?>" 
       required>
</div>

<div class="col-md-2">
<input type="number" 
       name="uas" 
       class="form-control"
       placeholder="UAS"
       min="0"
       max="100"
       value="<?= $edit_data['uas'] ?? '' ?>" 
       required>
</div>

</div>

<?php if($edit_data){ ?>
<input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
<button type="submit" name="update" class="btn btn-warning mt-3">
Update
</button>
<?php } else { ?>
<button type="submit" name="simpan" class="btn btn-primary mt-3">
Simpan
</button>
<?php } ?>

</form>
</div>

<!-- TABEL -->
<div class="card p-3">
<table class="table table-bordered">
<thead>
<tr>
<th>Siswa</th>
<th>Mapel</th>
<th>Tugas</th>
<th>UTS</th>
<th>UAS</th>
<th>Rata-rata</th>
<th>Aksi</th>
</tr>
</thead>

<tbody>
<?php while($row = mysqli_fetch_assoc($data)){ ?>
<tr>
<td><?= $row['nama_siswa'] ?></td>
<td><?= $row['nama_mapel'] ?></td>
<td><?= $row['tugas'] ?></td>
<td><?= $row['uts'] ?></td>
<td><?= $row['uas'] ?></td>
<td><strong><?= number_format($row['rata_rata'],2) ?></strong></td>
<td>
<a href="?edit=<?= $row['id'] ?>" class="btn btn-warning btn-sm">
Edit
</a>

<a href="?hapus=<?= $row['id'] ?>" 
class="btn btn-danger btn-sm"
onclick="return confirm('Yakin hapus?')">
Hapus
</a>
</td>
</tr>
<?php } ?>
</tbody>

</table>
</div>

</div>
</body>
</html>