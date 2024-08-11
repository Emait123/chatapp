<?= $this->extend('layout/main.php') ?>

<?= $this->section('head_lib') ?>
    <link rel="stylesheet" href="<?= base_url('assets/custom/css/index.css') ?>">
<?= $this->endSection('head_lib') ?>

<?= $this->section('sidebar') ?>
    <?= $this->include('layout/sidebar.php', $user) ?>
<?= $this->endSection('sidebar') ?>

<?= $this->section('content') ?>
<main class="container-fluid d-flex flex-column justify-content-start align-items-start p-3">
    <div class="row">
        <div class="col-12">
            <h1>Danh sách yêu cầu nghỉ phép</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <table class="table w-100">
                <tr>
                    <th>#</th>
                    <th>Ngày xin</th>
                    <th>Ngày bắt đầu</th>
                    <th>Ngày kết thúc</th>
                    <th>Lý do nghỉ</th>
                    <th>Trạng thái</th>
                    <th>Tác vụ</th>
                </tr>
                <?php foreach($timeoffList as $k => $timeoff): ?>
                    <tr>
                        <td><?= $k+1 ?></td>
                        <td><?= $timeoff['request_date'] ?></td>
                        <td><?= $timeoff['start_date'] ?></td>
                        <td><?= isset($timeoff['end_date']) ? $timeoff['end_date'] : '' ?></td>
                        <td><?= $timeoff['reason'] ?></td>
                        <td>
                            <?php if($timeoff['approved'] == '1'): ?>
                                <?= 'Đã duyệt' ?>
                            <?php else: ?>
                                <?= 'Chưa duyệt' ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-delete" title="Xóa" data-id=<?= $timeoff['id'] ?> ><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</main>
<script>
    document.querySelectorAll('.btn-delete').forEach( (el) => {
        el.addEventListener('click', () => {
            if (!confirm('Xóa yêu cầu xin nghỉ phép này?')) {
                return;
            }
            let id = el.dataset.id
            let formData = new FormData();
            formData.append('action', 'delTimeOff');
            formData.append('id', id);

            //Gửi request đến server để lấy thông tin
            fetch('<?= url_to('TimeoffList::fetch') ?>', {
                method: 'POST',
                mode: 'no-cors',
                headers: { 'Content-Type': 'application/json' },
                body: formData
            })
            .then(
            (response) => {
                if (response.status !== 200) {
                    alert('Có lỗi khi xóa yêu cầu.');
                    return console.log('Lỗi, mã lỗi ' + response.status);
                }
                // parse response data
                response.json().then(data => {
                    console.log(data)
                    if (data['result'] == true) {
                        location.reload();
                    } else {
                        alert('Có lỗi khi xóa yêu cầu.');
                    }
                    return;
                })
            }
            )
            .catch(err => {
                alert('Có lỗi khi xóa yêu cầu.');
            return console.log('Error :-S', err)
            });
        });
    });
</script>
<?= $this->endSection('content') ?>