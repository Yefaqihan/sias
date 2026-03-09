<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'guru'){
    header("Location: ../auth/login.php");
    exit;
}

/* =====================
   UPDATE DATA
===================== */
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
        
        echo "<script>alert('Data berhasil diupdate!'); window.location='input_nilai.php';</script>";
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
            
            echo "<script>alert('Data berhasil disimpan!'); window.location='input_nilai.php';</script>";
        }
    }
}

/* =====================
   DELETE DATA
===================== */
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    mysqli_query($conn,"DELETE FROM nilai WHERE id='$id'");
    echo "<script>alert('Data berhasil dihapus!'); window.location='input_nilai.php';</script>";
}

/* =====================
   QUERY DATA UTAMA
===================== */
$data = mysqli_query($conn,"
SELECT nilai.*, siswa.nama AS nama_siswa, mata_pelajaran.nama_mapel
FROM nilai
JOIN siswa ON nilai.siswa_id = siswa.id
JOIN mata_pelajaran ON nilai.mapel_id = mata_pelajaran.id
");

/* =====================
   QUERY DATA EDIT
===================== */
$edit_data = null;

if(isset($_GET['edit'])){
    $id = $_GET['edit'];
    // Lakukan JOIN agar nama siswa dan mapel bisa ditampilkan di Form Edit
    $q = mysqli_query($conn,"
        SELECT nilai.*, siswa.nama AS nama_siswa, mata_pelajaran.nama_mapel 
        FROM nilai 
        JOIN siswa ON nilai.siswa_id = siswa.id
        JOIN mata_pelajaran ON nilai.mapel_id = mata_pelajaran.id
        WHERE nilai.id='$id'
    ");
    $edit_data = mysqli_fetch_assoc($q);
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Input Nilai</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="d-flex">

    <div class="bg-dark text-white p-3" style="width:250px; min-height:100vh;">
        <h4>SIAS</h4>
        <hr>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="dashboard.php" class="nav-link text-white">Dashboard</a>
            </li>
            <li class="nav-item">
                <a href="input_nilai.php" class="nav-link text-white">Input Nilai</a>
            </li>
            <li class="nav-item">
                <a href="input_kehadiran.php" class="nav-link text-white">Input Kehadiran</a>
            </li>
            <li class="nav-item">
                <a href="kelola_materi.php" class="nav-link text-white">Kelola Materi</a>
            </li>
            <li class="nav-item">
                <a href="hitung_rapor.php" class="nav-link text-white">Hitung Rapor</a>
            </li>
            <li class="nav-item mt-3">
                <a href="../auth/logout.php" class="nav-link text-danger">Logout</a>
            </li>
        </ul>
    </div>

    <div class="p-4 w-100 bg-light">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Input Nilai Siswa</h3>
            <span><strong><?php echo $_SESSION['user']['nama']; ?></strong></span>
        </div>

        <?php if($edit_data): ?>
            <div class="card shadow-sm p-3 mb-4">
                <h5 class="mb-3">Edit Nilai Siswa</h5>
                <form method="POST">
                    <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Siswa</label>
                            <input type="text" class="form-control" value="<?= $edit_data['nama_siswa'] ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mata Pelajaran</label>
                            <input type="text" class="form-control" value="<?= $edit_data['nama_mapel'] ?>" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Nilai Tugas</label>
                            <input type="number" name="tugas" class="form-control" min="0" max="100" value="<?= $edit_data['tugas'] ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nilai UTS</label>
                            <input type="number" name="uts" class="form-control" min="0" max="100" value="<?= $edit_data['uts'] ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nilai UAS</label>
                            <input type="number" name="uas" class="form-control" min="0" max="100" value="<?= $edit_data['uas'] ?>" required>
                        </div>
                    </div>

                    <button type="submit" name="update" class="btn btn-warning">Update Nilai</button>
                    <a href="input_nilai.php" class="btn btn-secondary">Batal</a>
                </form>
            </div>

        <?php else: ?>
            <div class="card shadow-sm p-3 mb-4">
                <h5 class="mb-3">Input Nilai Baru</h5>
                <form method="POST">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">Siswa</label>
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
                            <label class="form-label">Mapel</label>
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
                            <label class="form-label">Tugas</label>
                            <input type="number" name="tugas" class="form-control" placeholder="0-100" min="0" max="100" required>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">UTS</label>
                            <input type="number" name="uts" class="form-control" placeholder="0-100" min="0" max="100" required>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">UAS</label>
                            <input type="number" name="uas" class="form-control" placeholder="0-100" min="0" max="100" required>
                        </div>
                    </div>

                    <button type="submit" name="simpan" class="btn btn-primary mt-3">Simpan Nilai</button>
                </form>
            </div>
        <?php endif; ?>


        <div class="card shadow-sm p-3">
            <h5 class="mb-3">Daftar Nilai</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
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
                                <a href="?edit=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="?hapus=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus data ini?')">Hapus</a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div> </div> </body>
</html>