<?= $this->extend('layout/main.php') ?>

<?= $this->section('head_lib') ?>
    <link rel="stylesheet" href="<?= base_url('assets/custom/css/index.css') ?>">
    <script src="<?= base_url('assets/custom/js/index.js') ?>" defer></script>
<?= $this->endSection('head_lib') ?>

<?= $this->section('sidebar') ?>
    <?= $this->include('layout/sidebar.php', $user) ?>
<?= $this->endSection('sidebar') ?>

<?= $this->section('content') ?>
<main class="w-100">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <table class="table">
                    <tr>
                        <th>#</th>
                        <th>Ngày xin</th>
                        <th>Ngày bắt đầu</th>
                        <th>Ngày kết thúc</th>
                        <th>Lý do nghỉ</th>
                    </tr>
                    <?php foreach($timeoffList as $k => $timeoff): ?>
                        <tr>
                            <td><?= $k+1 ?></td>
                            <td><?= $timeoff['request_date'] ?></td>
                            <td><?= $timeoff['start_date'] ?></td>
                            <td><?= isset($timeoff['end_date']) ? $timeoff['end_date'] : '' ?></td>
                            <td><?= $timeoff['reason'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</main>
<?= $this->endSection('content') ?>