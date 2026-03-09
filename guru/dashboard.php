<?php
session_start();
include "../config/koneksi.php";

if($_SESSION['user']['role'] != 'guru'){
    header("Location: ../auth/login.php");
    exit;
}

$id_guru = $_SESSION['user']['id'];

/* ===============================
   QUERY TOTAL KELAS (UNIVERSAL)
================================= */
$q_kelas = mysqli_query($conn,"
SELECT COUNT(DISTINCT s.kelas_id) AS total_kelas
FROM mata_pelajaran mp
JOIN nilai n ON mp.id = n.mapel_id
JOIN siswa s ON n.siswa_id = s.id
");

$data_kelas = mysqli_fetch_assoc($q_kelas);

/* ===============================
   QUERY NILAI BELUM LENGKAP (UNIVERSAL)
================================= */
// Dihapus kondisi: mp.guru_id = '$id_guru' AND
$q_belum = mysqli_query($conn,"
SELECT COUNT(*) AS belum_lengkap
FROM nilai n
JOIN mata_pelajaran mp ON n.mapel_id = mp.id
WHERE (n.tugas IS NULL OR n.uts IS NULL OR n.uas IS NULL)
");

$data_belum = mysqli_fetch_assoc($q_belum);

/* ===============================
   QUERY DATA NILAI (UNIVERSAL)
================================= */
// Dihapus kondisi: WHERE mp.guru_id = '$id_guru'
$q_nilai = mysqli_query($conn,"
SELECT s.nama, mp.nama_mapel, n.tugas, n.uts, n.uas, n.rata_rata
FROM nilai n
JOIN siswa s ON n.siswa_id = s.id
JOIN mata_pelajaran mp ON n.mapel_id = mp.id
ORDER BY n.id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Guru</title>
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
            <h3>Dashboard Guru</h3>
            <span><strong><?php echo $_SESSION['user']['nama']; ?></strong></span>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm p-3">
                    <h6>Total Kelas (Semua)</h6>
                    <h3><?php echo $data_kelas['total_kelas'] ?? 0; ?></h3>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm p-3">
                    <h6>Nilai Belum Lengkap (Semua)</h6>
                    <h3><?php echo $data_belum['belum_lengkap'] ?? 0; ?></h3>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <span>Data Nilai Siswa (Semua Data Terbaru)</span>
                <a href="input_nilai.php" class="btn btn-sm btn-light">Kelola Nilai</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Siswa</th>
                                <th>Mata Pelajaran</th>
                                <th>Tugas</th>
                                <th>UTS</th>
                                <th>UAS</th>
                                <th>Rata-rata</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($q_nilai) > 0) { ?>
                                <?php while($row = mysqli_fetch_assoc($q_nilai)){ ?>
                                <tr>
                                    <td><?php echo $row['nama']; ?></td>
                                    <td><?php echo $row['nama_mapel']; ?></td>
                                    <td><?php echo $row['tugas']; ?></td>
                                    <td><?php echo $row['uts']; ?></td>
                                    <td><?php echo $row['uas']; ?></td>
                                    <td><strong><?php echo number_format($row['rata_rata'] ?? 0, 2); ?></strong></td>
                                </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="6" class="text-center">Belum ada data nilai yang diinput.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

</div>

</body>
</html>