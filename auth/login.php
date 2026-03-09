<!DOCTYPE html>
<html>
<head>
    <title>Login SIAS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">

<div class="card shadow p-4" style="width:400px;">
    <h4 class="text-center mb-3">Login SIAS</h4>

    <?php if(isset($_GET['error'])){ ?>
        <div class="alert alert-danger">
            Login gagal! Username, password, atau role salah.
        </div>
    <?php } ?>

    <form method="POST" action="proses_login.php">

        <div class="mb-3">
            <label>Username / Email</label>
            <input type="text" name="username" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Login Sebagai</label>
            <select name="role" class="form-select" required>
                <option value="">-- Pilih Role --</option>
                <option value="guru">Guru</option>
                <option value="kepsek">Kepala Sekolah</option>
                <option value="ortu">Orang Tua</option>
                <option value="tu">Staff TU</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary w-100">
            Login
        </button>

    </form>
</div>

</body>
</html>